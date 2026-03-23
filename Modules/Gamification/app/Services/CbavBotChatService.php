<?php

namespace Modules\Gamification\App\Services;

use App\Models\Settings;
use App\Models\User;

/**
 * Serviço de chat do CBAV Bot.
 * Por padrão usa base local (Bíblia, insights). Com IA habilitada (admin ou config), pode delegar respostas a um provedor.
 */
class CbavBotChatService
{
    public function __construct(
        protected CbavBotRuleEngineService $ruleEngine
    ) {}

    /**
     * Responde à mensagem do usuário. Prioriza regras/base local; com IA habilitada pode usar provedor externo.
     */
    /**
     * Resposta que pode incluir formatted_html (para ação revise_format).
     */
    public function respondWithOptionalFormat(User $user, string $message, ?string $routeName, array $context): array
    {
        if (! empty($context['sermon_studio']) && ($context['action'] ?? '') === 'revise_format') {
            return $this->respondSermonReviseFormat($context);
        }
        $reply = $this->respond($user, $message, $routeName, $context);
        return ['reply' => $reply ?? '', 'formatted_html' => null];
    }

    public function respond(User $user, string $message, ?string $routeName = null, array $context = []): ?string
    {
        if (! empty($context['family_tree']) && ! empty($context['user_id'])) {
            $targetUser = User::find($context['user_id']);

            return $targetUser ? $this->respondFamilyTreeAnalysis($targetUser) : 'Usuário não encontrado.';
        }

        if (! empty($context['family_demographics'])) {
            return $this->respondFamilyDemographicsAnalysis($context['family_demographics']);
        }

        if (! empty($context['sermon_studio']) && ! empty($context['action'])
            && in_array($context['action'], ['suggest_illustration', 'check_coherence', 'historical_research'], true)) {
            return $this->respondSermonStudio($user, $message, $context);
        }

        $insight = $this->ruleEngine->evaluate($user, $routeName);
        if ($insight && ! empty($insight['content'])) {
            return $insight['content'];
        }

        $bibleResponse = $this->respondFromBible($message);
        if ($bibleResponse !== null) {
            return $bibleResponse;
        }

        $aiEnabled = Settings::get('cbav_bot_ai_enabled', config('gamification.bot.ai_enabled', false));
        $aiApiKey = Settings::get('cbav_bot_ai_api_key', config('gamification.bot.ai_api_key', ''));
        if ($aiEnabled && $aiApiKey !== '') {
            return $this->respondWithAi($user, $message, $routeName, $context);
        }

        return $this->getFallbackResponse($context);
    }

    private function respondFromBible(string $message): ?string
    {
        if (! class_exists(\Modules\Bible\App\Services\BibleApiService::class)) {
            return null;
        }
        $q = trim($message);
        if (strlen($q) < 2) {
            return null;
        }
        try {
            $bible = app(\Modules\Bible\App\Services\BibleApiService::class);
            $verses = $bible->search($q, 3);
            if ($verses->isEmpty()) {
                return null;
            }
            $verses->load('chapter.book');
            $lines = $verses->map(fn ($v) => '"' . $v->text . '" — ' . ($v->chapter && $v->chapter->book ? $v->chapter->book->name . ' ' . $v->chapter->chapter_number . ':' . $v->verse_number : '') . '')->toArray();
            return 'Encontrei na Bíblia: ' . implode(' ', $lines);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function respondWithAi(User $user, string $message, ?string $routeName, array $context = []): ?string
    {
        // Estrutura para integração futura com OpenAI/etc. Não chamar API sem key válida.
        return null;
    }

    /**
     * Análise da árvore familiar para o Admin: composição e sugestões (ex.: mesmo sobrenome).
     */
    public function respondFamilyTreeAnalysis(User $user): string
    {
        $user->load(['relationships.relatedUser']);
        $accepted = $user->relationships->where('status', \App\Models\UserRelationship::STATUS_ACCEPTED);
        $pending = $user->relationships->where('status', \App\Models\UserRelationship::STATUS_PENDING);
        $labels = \App\Models\UserRelationship::relationshipTypeLabels();

        $lines = [];
        $lines[] = "**Composição familiar de {$user->name}**\n";

        if ($accepted->isNotEmpty()) {
            $lines[] = 'Vínculos confirmados:';
            foreach ($accepted as $rel) {
                $name = $rel->related_user_id && $rel->relatedUser
                    ? $rel->relatedUser->name
                    : ($rel->related_name ?? '—');
                $lines[] = '  • ' . ($labels[$rel->relationship_type] ?? $rel->relationship_type) . ': ' . $name;
            }
            $lines[] = '';
        }

        if ($pending->isNotEmpty()) {
            $lines[] = 'Aguardando confirmação:';
            foreach ($pending as $rel) {
                $name = $rel->related_user_id && $rel->relatedUser
                    ? $rel->relatedUser->name
                    : ($rel->related_name ?? '—');
                $lines[] = '  • ' . ($labels[$rel->relationship_type] ?? $rel->relationship_type) . ': ' . $name;
            }
            $lines[] = '';
        }

        if ($accepted->isEmpty() && $pending->isEmpty()) {
            $lines[] = 'Nenhum vínculo familiar cadastrado ainda. Adicione parentes na edição do perfil.';
        }

        $lastName = $user->last_name ?? trim(explode(' ', $user->name, 2)[1] ?? '');
        if ($lastName !== '') {
            $sameLastName = User::query()
                ->where('id', '!=', $user->id)
                ->where(function ($q) use ($lastName) {
                    $q->where('last_name', 'like', $lastName)
                        ->orWhere('name', 'like', '%' . $lastName . '%');
                })
                ->limit(5)
                ->get(['id', 'name']);
            if ($sameLastName->isNotEmpty()) {
                $lines[] = "\nSugestão: há outros membros com sobrenome semelhante a \"{$lastName}\". Considere verificar se há parentesco:";
                foreach ($sameLastName as $u) {
                    $lines[] = '  • ' . $u->name;
                }
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Análise pastoral a partir dos dados demográficos (Admin dashboard).
     * Gera sugestões baseadas em composição familiar e destaques.
     */
    public function respondFamilyDemographicsAnalysis(array $summary): string
    {
        $comp = $summary['composition'] ?? [];
        $highlights = $summary['pastoral_highlights'] ?? [];
        $byNeighborhood = $summary['by_neighborhood'] ?? [];
        $totalNuclei = $summary['total_nuclei'] ?? 0;

        $lines = ["**Análise pastoral com base nos dados demográficos**\n"];

        if ($totalNuclei > 0) {
            $lines[] = '• Total de núcleos/famílias identificados: ' . $totalNuclei;
            if (! empty($comp['complete_families'])) {
                $lines[] = '• Famílias completas (casal + filhos): ' . $comp['complete_families'] . ' — Sugere-se eventos focados em criação de filhos e fortalecimento do lar.';
            }
            if (! empty($comp['monoparental'])) {
                $lines[] = '• Famílias monoparentais: ' . $comp['monoparental'] . ' — Considere grupos de apoio e acompanhamento pastoral específico.';
            }
            if (! empty($comp['couples'])) {
                $lines[] = '• Casais sem filhos no cadastro: ' . $comp['couples'] . ' — Oportunidade para ministério de casais e eventos para fortalecimento do matrimônio.';
            }
            if (! empty($comp['individuals'])) {
                $lines[] = '• Membros individuais (sem núcleo cadastrado): ' . $comp['individuals'] . ' — Sugere-se ministério de integração e discipulado para vínculo familiar e comunitário.';
            }
        }

        if ($highlights !== []) {
            $lines[] = "\n**Destaques:**";
            foreach ($highlights as $h) {
                $lines[] = '• ' . $h;
            }
        }

        if ($byNeighborhood !== []) {
            $lines[] = "\n**Por região:** " . implode(', ', array_map(fn ($area, $count) => "{$area}: {$count}", array_keys($byNeighborhood), $byNeighborhood));
        }

        $lines[] = "\n*Recomendação geral: use esses dados em reuniões de planejamento e conselho para priorizar eventos e ministérios conforme o perfil da igreja.*";

        return implode("\n", $lines);
    }

    private function getFallbackResponse(array $context = []): string
    {
        if (! empty($context['lesson_title'])) {
            return 'Sobre a lição "' . $context['lesson_title'] . '": posso te ajudar com versículos e princípios bíblicos. Consulte a Bíblia como base (2 Timóteo 3:16). Se tiver dúvidas doutrinárias, fale com seu professor ou pastor.';
        }

        return 'Posso te ajudar com dicas do painel, versículos e sua jornada. Complete seu perfil, leia a Bíblia e participe dos eventos!';
    }

    /**
     * Sermon Studio: sugerir ilustração, verificar coerência (CBB), pesquisa histórica.
     */
    private function respondSermonStudio(User $user, string $message, array $context): string
    {
        $action = $context['action'] ?? '';

        if ($action === 'suggest_illustration') {
            $mainPoint = trim((string) ($context['main_point'] ?? ''));
            if ($mainPoint === '' && ! empty($context['excerpt'])) {
                $mainPoint = mb_substr(strip_tags((string) $context['excerpt']), 0, 300);
            }
            if ($mainPoint === '') {
                $mainPoint = $message ?: 'amor e fé';
            }
            if (! class_exists(\Modules\Bible\App\Services\BibleApiService::class)) {
                return 'O módulo Bíblia não está disponível. Sugestão: use uma ilustração que reflita o ponto central do seu sermão.';
            }
            try {
                $bible = app(\Modules\Bible\App\Services\BibleApiService::class);
                $searchTerms = $this->illustrationSearchTerms($mainPoint);
                $verses = collect();
                foreach ($searchTerms as $term) {
                    if ($term === '') {
                        continue;
                    }
                    $verses = $bible->search($term, 5);
                    if ($verses->isNotEmpty()) {
                        break;
                    }
                }
                if ($verses->isEmpty()) {
                    return $this->suggestIllustrationFallback($mainPoint);
                }
                $verses->load('chapter.book');
                $lines = $verses->map(fn ($v) => $v->chapter && $v->chapter->book
                    ? $v->chapter->book->name . ' ' . $v->chapter->chapter_number . ':' . $v->verse_number . ' — "' . mb_substr($v->text, 0, 80) . '..."'
                    : '')->filter()->take(3)->values()->toArray();
                return 'Sugestões de versículos ilustrativos: ' . implode("\n", $lines);
            } catch (\Throwable $e) {
                return $this->suggestIllustrationFallback($mainPoint);
            }
        }

        if ($action === 'check_coherence') {
            return "Checklist de coerência (princípios batistas / CBB):\n"
                . "• A Escritura é a autoridade suprema (Sola Scriptura).\n"
                . "• Interpretação literal-gramatorial no contexto.\n"
                . "• Cristo como centro da revelação.\n"
                . "• Igreja local visível e autonomia.\n"
                . "• Sacerdócio universal do crente.\n"
                . "Revise se sua interpretação e aplicação estão alinhadas a esses princípios.";
        }

        if ($action === 'historical_research') {
            $reference = $context['reference'] ?? $message;
            $bookNumber = $context['book_number'] ?? null;
            if (! $bookNumber && $reference && class_exists(\Modules\Bible\App\Services\BibleApiService::class)) {
                try {
                    $bible = app(\Modules\Bible\App\Services\BibleApiService::class);
                    $find = $bible->findByReference($reference);
                    if ($find && isset($find['book_number'])) {
                        $bookNumber = $find['book_number'];
                    }
                } catch (\Throwable $e) {
                }
            }
            if ($bookNumber && class_exists(\Modules\Bible\App\Services\BibleApiService::class)) {
                try {
                    $bible = app(\Modules\Bible\App\Services\BibleApiService::class);
                    $panorama = $bible->getPanoramaByBookNumber((int) $bookNumber);
                    if ($panorama) {
                        $out = "Contexto histórico (panorama do livro):\n";
                        $out .= "Autor: " . ($panorama['author'] ?? '—') . "\n";
                        $out .= "Data: " . ($panorama['date_written'] ?? '—') . "\n";
                        $out .= "Tema central: " . ($panorama['theme_central'] ?? '—') . "\n";
                        $out .= "Destinatários: " . ($panorama['recipients'] ?? '—') . "\n";
                        $out .= "\nPara mais detalhes, consulte um comentário bíblico.";
                        return $out;
                    }
                } catch (\Throwable $e) {
                }
            }
            return 'Informe a referência ou o livro para obter o contexto histórico. Para mais detalhes, consulte um comentário bíblico.';
        }

        return $this->getFallbackResponse($context);
    }

    /**
     * Gera termos de busca para ilustração: texto principal e depois palavras-chave (evitando stopwords).
     */
    private function illustrationSearchTerms(string $mainPoint): array
    {
        $terms = [];
        $first = mb_substr($mainPoint, 0, 50);
        if (trim($first) !== '') {
            $terms[] = trim($first);
        }
        $stopwords = ['quando', 'no', 'na', 'do', 'da', 'dos', 'das', 'em', 'o', 'a', 'os', 'as', 'um', 'uma', 'de', 'e', 'que', 'para', 'com', 'se', 'ao', 'aos', 'pelo', 'pela', 'nos', 'nas', 'pelo', 'pela', 'meio', 'do', 'da', 'ao', 'aos', 'su', 'sua', 'seus', 'suas', 'este', 'esta', 'esse', 'essa', 'aquele', 'aquela', 'isso', 'isto', 'como', 'mais', 'mas', 'sem', 'sob', 'sobre', 'entre', 'até', 'após', 'desde', 'durante', 'perante', 'mediante', 'salvo', 'segundo', 'consoante'];
        $words = preg_split('/\s+/u', mb_strtolower($mainPoint), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $w) {
            $w = preg_replace('/[^\p{L}\p{N}]/u', '', $w);
            if ($w !== '' && mb_strlen($w) >= 2 && ! in_array($w, $stopwords, true)) {
                $terms[] = $w;
            }
        }
        return array_values(array_unique(array_slice($terms, 0, 6)));
    }

    /**
     * Quando a busca não encontra versículos, sugere passagens por tema (sofrimento, Deus, confiança, etc.).
     */
    private function suggestIllustrationFallback(string $mainPoint): string
    {
        $lower = mb_strtolower($mainPoint);
        $suggestions = [];
        if (preg_match('/sofrimento|dor|tribulação|provação|adversidade/i', $lower)) {
            $suggestions[] = 'Jó 1–2 (Jó no sofrimento); Salmos 22; 2 Coríntios 1.3-7; Hebreus 12.1-11; Tiago 1.2-4.';
        }
        if (preg_match('/deus fala|voz de deus|palavra de deus|revelação/i', $lower)) {
            $suggestions[] = '1 Reis 19.11-13 (voz suave); Hebreus 1.1-2; 2 Timóteo 3.16; Salmo 19.';
        }
        if (preg_match('/confiança|confiar|fé|crença/i', $lower)) {
            $suggestions[] = 'Hebreus 11; Romanos 4; Provérbios 3.5-6; Salmo 37.5.';
        }
        if (preg_match('/caminho|entender|entendimento/i', $lower)) {
            $suggestions[] = 'Isaías 55.8-9; Provérbios 3.5-6; Romanos 11.33-36.';
        }
        if (empty($suggestions)) {
            $suggestions[] = 'Salmos 23; Romanos 8.28-39; Filipenses 4.4-7 (temas gerais de consolo e direção).';
        }
        return 'Para o tema "' . mb_substr($mainPoint, 0, 50) . (mb_strlen($mainPoint) > 50 ? '...' : '') . '", considere estas passagens para ilustração ou apoio: ' . implode(' ', $suggestions) . ' Pode usar o botão @ na barra do editor para inserir o texto do versículo a partir da referência.';
    }

    /**
     * Revisão/formação do sermão para o púlpito: normaliza estrutura HTML sem alterar o conteúdo do autor.
     * Retorna reply + formatted_html para o front aplicar no editor.
     */
    private function respondSermonReviseFormat(array $context): array
    {
        $html = trim((string) ($context['full_content'] ?? ''));
        if ($html === '') {
            return [
                'reply' => 'Nenhum conteúdo enviado. Escreva ou cole o sermão no editor e clique novamente em "Revisar com Elias".',
                'formatted_html' => null,
            ];
        }
        $formatted = $this->formatForPulpit($html);
        $reply = "Estrutura do sermão normalizada para o púlpito:\n"
            . "• Parágrafos e espaços ajustados.\n"
            . "• Citações bíblicas (blockquotes) mantidas e identificadas.\n"
            . "• Títulos (H2/H3) preservados para impressão.\n\n"
            . "Clique em \"Aplicar formatação no editor\" abaixo para atualizar. O teu texto não foi alterado, apenas organizado para impressão.";

        return ['reply' => $reply, 'formatted_html' => $formatted];
    }

    /**
     * Normaliza o HTML do sermão para impressão no púlpito: estrutura consistente, sem remover conteúdo.
     */
    private function formatForPulpit(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/si', '', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $html);
        $encoding = '<?xml encoding="UTF-8">';
        $doc = new \DOMDocument('1.0', 'UTF-8');
        @$doc->loadHTML($encoding . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $body = $doc->getElementsByTagName('body')->item(0);
        if (! $body) {
            return $this->normalizeHtmlFallback($html);
        }
        $this->normalizeNode($body);
        $out = $doc->saveHTML($body);
        $out = preg_replace('/^<\?xml[^>]*>\s*/', '', $out ?? '');
        $out = trim(preg_replace('/\s*<\/?body>\s*/', '', $out));
        return $out !== '' ? $out : $html;
    }

    private function normalizeNode(\DOMNode $node): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $node->nodeValue = trim($node->nodeValue ?? '');
            return;
        }
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return;
        }
        $tag = strtolower($node->nodeName);
        if (in_array($tag, ['script', 'style'], true)) {
            $node->parentNode?->removeChild($node);
            return;
        }
        foreach (iterator_to_array($node->childNodes) as $child) {
            $this->normalizeNode($child);
        }
        $toRemove = [];
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_TEXT_NODE && trim($child->nodeValue ?? '') === '') {
                $toRemove[] = $child;
            }
        }
        foreach ($toRemove as $c) {
            $node->removeChild($c);
        }
        if ($tag === 'blockquote' && ! $node->hasAttribute('class')) {
            $node->setAttribute('class', 'bible-ref');
        }
        if ($tag === 'p') {
            $text = trim($node->textContent ?? '');
            if ($text === '' || $text === "\u{00A0}") {
                $node->parentNode?->removeChild($node);
            }
        }
    }

    private function normalizeHtmlFallback(string $html): string
    {
        $html = preg_replace('/\s*<p>\s*<\/p>\s*/', "\n", $html);
        $html = preg_replace('/\s*<p>\s*<br\s*\/?>\s*<\/p>\s*/', "\n", $html);
        $html = trim(preg_replace('/\n{3,}/', "\n\n", $html));
        return $html;
    }
}
