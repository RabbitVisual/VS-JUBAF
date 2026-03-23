<?php

declare(strict_types=1);

namespace Modules\Gamification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Gamification\Models\Insight;

/**
 * Insights por página do painel do membro (Igreja). Fallback do bot Elias quando nenhuma regra de coaching aplica.
 * trigger_event: page_dashboard, page_bible, page_treasury, page_donations, page_sermons, page_ministries, page_events, page_profile, page_ebd, page_intercessor, page_notifications.
 */
class PageGospelInsightsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // page_dashboard
            ['trigger_event' => 'page_dashboard', 'content' => 'No dashboard você vê um resumo do seu perfil, conquistas e atalhos. A recomendação de leitura do dia fica aqui por 24h.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_dashboard', 'content' => 'Que bom ter você por aqui! Confira a leitura do dia no card fixo e use o menu para explorar Bíblia, eventos e ministérios.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_dashboard', 'content' => 'Comece o dia conferindo a recomendação de leitura bíblica no seu painel. Ela muda a cada 24 horas.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_dashboard', 'content' => 'Seu painel é o ponto de partida. Daqui você acessa Bíblia, eventos, EBD, doações e muito mais.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_dashboard', 'content' => 'Hoje recomendo a leitura que está fixa no seu dashboard. Ela fica disponível por 24 horas.', 'level' => 'info', 'is_active' => true],
            // page_bible
            ['trigger_event' => 'page_bible', 'content' => 'A Palavra é viva e eficaz. Use a busca ou navegue por livro e capítulo para edificar-se.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_bible', 'content' => 'Guarde versículos favoritos para reler quando precisar. Tudo fica salvo no seu perfil.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_bible', 'content' => 'Que tal conferir na Bíblia o versículo que edifica o coração? A leitura diária faz diferença.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_bible', 'content' => 'Escolha uma versão e um livro para ler. A meditação na Palavra fortalece a fé no dia a dia.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_bible', 'content' => 'Aqui você tem a Bíblia completa, sem sair do painel. Use os favoritos para voltar aos trechos que marcaram você.', 'level' => 'info', 'is_active' => true],
            // page_treasury
            ['trigger_event' => 'page_treasury', 'content' => 'Na tesouraria você acompanha entradas, campanhas e metas. A transparência fortalece a confiança.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_treasury', 'content' => 'Use os relatórios para entender como os recursos são aplicados na obra. Tudo à disposição aqui.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_treasury', 'content' => 'Campanhas e metas ficam organizadas nesta área. Acompanhe e participe com consciência.', 'level' => 'info', 'is_active' => true],
            // page_donations
            ['trigger_event' => 'page_donations', 'content' => '"Cada um dê conforme determinou em seu coração." Suas ofertas e dízimos podem ser feitos aqui de forma segura.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_donations', 'content' => 'Contribuir é um ato de adoração. Use o botão de doar para ofertar e acompanhe seu histórico nesta página.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_donations', 'content' => 'Deus ama quem dá com alegria. Aqui você faz e acompanha suas contribuições à obra.', 'level' => 'info', 'is_active' => true],
            // page_sermons
            ['trigger_event' => 'page_sermons', 'content' => 'Sermões e estudos ficam reunidos aqui. Ouça e assista quando quiser para se edificar.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_sermons', 'content' => 'A fé vem pelo ouvir. Explore a biblioteca de mensagens e marque seus favoritos.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_sermons', 'content' => 'Que tal ouvir uma mensagem hoje? Os sermões estão organizados por série e tema.', 'level' => 'info', 'is_active' => true],
            // page_ministries
            ['trigger_event' => 'page_ministries', 'content' => 'Cada um com seu dom: descubra os ministérios e veja onde você pode servir com propósito.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_ministries', 'content' => 'Fazer parte de um ministério é colocar os talentos a serviço do Reino. Explore e inscreva-se.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_ministries', 'content' => 'Aqui você encontra música, juventude, casais e outras frentes. Encontre o seu lugar.', 'level' => 'info', 'is_active' => true],
            // page_events
            ['trigger_event' => 'page_events', 'content' => 'Eventos e cultos estão aqui. Faça sua inscrição e participe da programação da igreja.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_events', 'content' => 'A comunhão se fortalece quando nos encontramos. Confira os eventos e garanta sua vaga.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_events', 'content' => 'Não fique de fora: veja a lista de eventos e inscreva-se nos que tocaram seu coração.', 'level' => 'info', 'is_active' => true],
            // page_profile
            ['trigger_event' => 'page_profile', 'content' => 'Mantenha seu perfil atualizado. Dados completos ajudam a igreja a te conhecer e a te servir melhor.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_profile', 'content' => 'Foto e dados em dia refletem cuidado. Edite seu perfil sempre que precisar.', 'level' => 'info', 'is_active' => true],
            // page_ebd
            ['trigger_event' => 'page_ebd', 'content' => 'A EBD é lugar de estudo e crescimento. Acompanhe lições, turmas e jogos bíblicos aqui.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_ebd', 'content' => 'Escola Bíblica Dominical: suas turmas, progresso e atividades estão nesta área.', 'level' => 'info', 'is_active' => true],
            // page_intercessor
            ['trigger_event' => 'page_intercessor', 'content' => 'A oração move montanhas. Envie pedidos de oração e acompanhe os que você se comprometeu a orar.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_intercessor', 'content' => 'Aqui a igreja ora junta. Cadastre pedidos e veja como participar da rede de intercessão.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_intercessor', 'content' => 'Cada pedido de oração é levado à rede. Envie o seu e comprometa-se a orar por outros.', 'level' => 'info', 'is_active' => true],
            // page_notifications
            ['trigger_event' => 'page_notifications', 'content' => 'Fique por dentro: avisos, lembretes e novidades da igreja aparecem aqui.', 'level' => 'info', 'is_active' => true],
            ['trigger_event' => 'page_notifications', 'content' => 'Confira as notificações para não perder eventos e comunicados importantes.', 'level' => 'info', 'is_active' => true],
        ];

        foreach ($rows as $row) {
            Insight::firstOrCreate(
                [
                    'trigger_event' => $row['trigger_event'],
                    'content' => $row['content'],
                ],
                ['level' => $row['level'], 'is_active' => $row['is_active']]
            );
        }
    }
}
