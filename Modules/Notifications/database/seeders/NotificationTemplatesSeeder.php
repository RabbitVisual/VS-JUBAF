<?php

namespace Modules\Notifications\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Notifications\App\Models\NotificationTemplate;

/**
 * Seeds production-ready notification templates for all system notification types.
 * Templates use placeholders: {{ title }}, {{ message }}, {{ action_url }}, {{ action_text }}
 * Idempotent: updates existing by key or creates new.
 */
class NotificationTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = $this->templates();

        foreach ($templates as $data) {
            NotificationTemplate::updateOrCreate(
                ['key' => $data['key']],
                [
                    'name' => $data['name'],
                    'subject' => $data['subject'],
                    'body' => $data['body'],
                    'channels' => $data['channels'] ?? ['in_app', 'email', 'webpush'],
                    'variables' => $data['variables'] ?? ['title', 'message', 'action_url', 'action_text'],
                    'is_active' => $data['is_active'] ?? true,
                ]
            );
        }
    }

    /**
     * @return array<int, array{key: string, name: string, subject: string, body: string, channels?: array, variables?: array, is_active?: bool}>
     */
    protected function templates(): array
    {
        return [
            [
                'key' => 'worship_roster',
                'name' => 'Escala de louvor publicada',
                'subject' => 'Você foi escalado — {{ title }}',
                'body' => "<p>Olá!</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV — Sistema de Gestão da Igreja</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'ebd_lesson',
                'name' => 'Nova lição da EBD',
                'subject' => 'Nova lição disponível — {{ title }}',
                'body' => "<p>Olá!</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#059669;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV — EBD</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'churchcouncil_minutes',
                'name' => 'Ata do conselho pendente de assinatura',
                'subject' => 'Ata pendente de visto — {{ title }}',
                'body' => "<p>Prezado(a) conselheiro(a),</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#0d9488;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV — Conselho</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'sermon_collaboration',
                'name' => 'Convite para co-autoria de sermão',
                'subject' => 'Convite para colaborar — {{ title }}',
                'body' => "<p>Olá!</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#7c3aed;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV — Sermões</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'treasury_approval',
                'name' => 'Despesa aguardando aprovação do conselho',
                'subject' => 'Aprovação pendente — {{ title }}',
                'body' => "<p>Prezado(a) administrador(a),</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#dc2626;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV — Tesouraria</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'event_registration',
                'name' => 'Inscrição em evento confirmada',
                'subject' => 'Inscrição confirmada — {{ title }}',
                'body' => "<p>Olá!</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#0284c7;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV — Eventos</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'payment_completed',
                'name' => 'Pagamento confirmado',
                'subject' => 'Pagamento confirmado — {{ title }}',
                'body' => "<p>Olá!</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#16a34a;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'student_leveled_up',
                'name' => 'Subiu de nível (EBD)',
                'subject' => 'Parabéns! Você subiu de nível — {{ title }}',
                'body' => "<p>Parabéns!</p>\n\n<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#ca8a04;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV — EBD</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
            [
                'key' => 'generic',
                'name' => 'Outras notificações',
                'subject' => '{{ title }}',
                'body' => "<p><strong>{{ title }}</strong></p>\n\n<p>{{ message }}</p>\n\n<p><a href=\"{{ action_url }}\" style=\"display:inline-block;padding:10px 20px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;\">{{ action_text }}</a></p>\n\n<p style=\"color:#6b7280;font-size:12px;margin-top:24px;\">VertexCBAV</p>",
                'channels' => ['in_app', 'email', 'webpush'],
            ],
        ];
    }
}
