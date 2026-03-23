<?php

namespace Modules\Gamification\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Gamification\App\Services\CbavBotChatService;
use Modules\Gamification\App\Services\CbavBotRuleEngineService;

class CbavBotController extends Controller
{
    public function __construct(
        protected CbavBotRuleEngineService $ruleEngine,
        protected CbavBotChatService $chat
    ) {}

    private function getVerseOfTheDay(): ?array
    {
        if (! class_exists(\Modules\Bible\App\Services\BibleApiService::class)) {
            return null;
        }
        try {
            $bible = app(\Modules\Bible\App\Services\BibleApiService::class);
            $verse = $bible->getRandomVerse();
            if (! $verse || ! $verse->relationLoaded('chapter')) {
                $verse?->load('chapter.book');
            }
            if ($verse && $verse->chapter && $verse->chapter->book) {
                return [
                    'text' => $verse->text,
                    'reference' => $verse->chapter->book->name . ' ' . $verse->chapter->chapter_number . ':' . $verse->verse_number,
                ];
            }
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }

    /**
     * Chat com o Elias (IA tutor). Aceita message e opcionalmente lesson_id para contexto da lição.
     * Respostas seguem princípios batistas e usam a Bíblia como base.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:2000',
            'lesson_id' => 'nullable|integer|exists:ebd_lessons,id',
            'context' => 'nullable|array',
            'context.sermon_studio' => 'nullable|boolean',
            'context.action' => 'nullable|string|in:suggest_illustration,check_coherence,historical_research,revise_format',
            'context.main_point' => 'nullable|string',
            'context.reference' => 'nullable|string',
            'context.book_number' => 'nullable|integer',
            'context.sermon_id' => 'nullable|integer',
            'context.excerpt' => 'nullable|string|max:2000',
            'context.full_content' => 'nullable|string|max:100000',
        ]);

        $user = auth()->user();
        $context = $validated['context'] ?? [];
        $message = trim((string) ($validated['message'] ?? ''));

        // Sermon Studio: ação sem texto → mensagem padrão para o tipo de ação
        if (($message === '') && ! empty($context['sermon_studio']) && ! empty($context['action'])) {
            $action = $context['action'];
            $message = match ($action) {
                'suggest_illustration' => 'Sugerir ilustração bíblica para o ponto central do sermão.',
                'check_coherence' => 'Verificar coerência com princípios batistas e CBB.',
                'historical_research' => 'Pesquisa histórica e contexto do texto.',
                'revise_format' => 'Revisar e formatar o sermão para o púlpito.',
                default => 'Consultar Elias sobre o sermão.',
            };
        }

        if ($message === '') {
            return response()->json([
                'reply' => 'Envie uma mensagem ou use os botões do Elias (Sermon Studio / EBD) para obter dicas.',
            ], 200);
        }

        if (! empty($validated['lesson_id'])) {
            $lesson = \Modules\EBD\App\Models\EBDLesson::find($validated['lesson_id']);
            if ($lesson) {
                $context['lesson_title'] = $lesson->title;
                $context['lesson_description'] = $lesson->description ?? '';
            }
        }

        if (! empty($context['sermon_studio']) && ($context['action'] ?? '') === 'revise_format') {
            $result = $this->chat->respondWithOptionalFormat($user, $message, null, $context);
            return response()->json([
                'reply' => $result['reply'] ?? '',
                'formatted_html' => $result['formatted_html'] ?? null,
            ]);
        }

        $reply = $this->chat->respond($user, $message, null, $context);

        return response()->json(['reply' => $reply ?? 'Posso te ajudar com a lição e com versículos. Consulte a Bíblia como base.']);
    }

    /**
     * Dismissa um insight (session) e define cooldown para não mostrar outro imediatamente.
     */
    public function dismiss(Request $request): JsonResponse
    {
        $request->validate(['insight_key' => 'nullable|string|max:255']);
        $key = $request->input('insight_key');
        if ($key) {
            $dismissed = session('cbav_bot_dismissed', []);
            if (! in_array($key, $dismissed, true)) {
                $dismissed[] = $key;
                session(['cbav_bot_dismissed' => $dismissed]);
            }
            session(['cbav_bot_last_shown_time' => time()]);
        }
        return response()->json(['ok' => true]);
    }

    /**
     * Página "Análise detalhada": conquistas, versículo, dicas.
     */
    public function analysis(): View
    {
        $user = auth()->user();
        $topic = request()->query('topic');
        $insight = $this->ruleEngine->evaluate($user, request()->route()?->getName());
        $tourId = $insight['tour_id'] ?? CbavBotRuleEngineService::getTourIdForRoute(request()->route()?->getName() ?? '');

        if ($topic === 'notifications') {
            $insight = array_merge($insight ?? [], [
                'content' => 'Use o botão « Ir para preferências » abaixo para configurar o que receber e o horário de silêncio.',
            ]);
        }

        $verseOfTheDay = $this->getVerseOfTheDay();

        return view('gamification::cbav-bot.analysis', [
            'user' => $user,
            'insight' => $insight,
            'tourId' => $tourId,
            'verseOfTheDay' => $verseOfTheDay,
            'topic' => $topic,
        ]);
    }
}
