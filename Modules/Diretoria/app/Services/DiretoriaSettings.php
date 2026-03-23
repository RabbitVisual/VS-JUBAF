<?php

namespace Modules\Diretoria\App\Services;

use App\Models\Settings;

/**
 * Central access to Church diretoria settings stored in App\Models\Settings (jubaf_diretoria_* keys).
 * Use this service everywhere (admin, member panel, PDFs, approvals) so settings are applied consistently.
 */
class DiretoriaSettings
{
    public const KEY_NAME = 'jubaf_diretoria_name';

    public const KEY_MEETING_FREQUENCY = 'jubaf_diretoria_meeting_frequency';

    public const KEY_QUORUM_PERCENTAGE = 'jubaf_diretoria_quorum_percentage';

    public const KEY_VOTING_DEADLINE_DAYS = 'jubaf_diretoria_voting_deadline_days';

    public const KEY_AUTO_APPROVE_BUDGET_LIMIT = 'jubaf_diretoria_auto_approve_budget_limit';

    public const KEY_APPROVAL_DEADLINE_DAYS = 'jubaf_diretoria_approval_deadline_days';

    public const KEY_ENABLED_APPROVAL_TYPES = 'jubaf_diretoria_enabled_approval_types';

    public const KEY_EMAIL_NOTIFICATIONS = 'jubaf_diretoria_email_notifications';

    public const KEY_REMINDER_NOTIFICATIONS = 'jubaf_diretoria_reminder_notifications';

    public const KEY_VOTING_REMINDERS = 'jubaf_diretoria_voting_reminders';

    public const KEY_ALLOW_ADMIN_APPROVAL = 'jubaf_diretoria_allow_admin_approval';

    /**
     * Get a single setting value.
     */
    public static function get(string $key, $default = null)
    {
        return Settings::get($key, $default);
    }

    /**
     * diretoria display name (used in titles, PDFs, member panel, admin).
     */
    public static function diretoriaName(): string
    {
        return (string) self::get(self::KEY_NAME, 'Diretoria');
    }

    /**
     * Meeting frequency: weekly, biweekly, monthly, quarterly.
     */
    public static function meetingFrequency(): string
    {
        return (string) self::get(self::KEY_MEETING_FREQUENCY, 'monthly');
    }

    /**
     * Minimum quorum percentage (1–100) for meetings.
     */
    public static function quorumPercentage(): int
    {
        return (int) self::get(self::KEY_QUORUM_PERCENTAGE, 50);
    }

    /**
     * Default voting deadline in days (used when creating agendas/votes).
     */
    public static function votingDeadlineDays(): int
    {
        return (int) self::get(self::KEY_VOTING_DEADLINE_DAYS, 7);
    }

    /**
     * Budget limit below which approvals can be auto-approved (Treasury/approvals integration).
     */
    public static function autoApproveBudgetLimit(): float
    {
        return (float) self::get(self::KEY_AUTO_APPROVE_BUDGET_LIMIT, 1000);
    }

    /**
     * Max days for an approval request to be answered.
     */
    public static function approvalDeadlineDays(): int
    {
        return (int) self::get(self::KEY_APPROVAL_DEADLINE_DAYS, 15);
    }

    /**
     * List of enabled approval type slugs: budget, project, personnel, policy, facility, other.
     *
     * @return array<int, string>
     */
    public static function enabledApprovalTypes(): array
    {
        $raw = self::get(self::KEY_ENABLED_APPROVAL_TYPES, '["budget","project","policy"]');
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : ['budget', 'project', 'policy'];
        }
        return is_array($raw) ? $raw : ['budget', 'project', 'policy'];
    }

    public static function emailNotifications(): bool
    {
        return (bool) self::get(self::KEY_EMAIL_NOTIFICATIONS, true);
    }

    public static function reminderNotifications(): bool
    {
        return (bool) self::get(self::KEY_REMINDER_NOTIFICATIONS, true);
    }

    public static function votingReminders(): bool
    {
        return (bool) self::get(self::KEY_VOTING_REMINDERS, true);
    }

    public static function allowAdminApproval(): bool
    {
        return (bool) self::get(self::KEY_ALLOW_ADMIN_APPROVAL, false);
    }

    /**
     * Return all diretoria settings as a keyed array (form keys: diretoria_name, meeting_frequency, ...).
     * Used by the admin settings form and by views that need multiple values.
     *
     * @return array<string, mixed>
     */
    public static function getAll(): array
    {
        $enabledTypes = self::get(self::KEY_ENABLED_APPROVAL_TYPES, '["budget","project","policy"]');
        if (is_string($enabledTypes)) {
            $enabledTypes = json_decode($enabledTypes, true) ?? ['budget', 'project', 'policy'];
        }
        if (! is_array($enabledTypes)) {
            $enabledTypes = ['budget', 'project', 'policy'];
        }

        return [
            'diretoria_name' => self::get(self::KEY_NAME, 'Diretoria'),
            'meeting_frequency' => self::get(self::KEY_MEETING_FREQUENCY, 'monthly'),
            'quorum_percentage' => (int) self::get(self::KEY_QUORUM_PERCENTAGE, 50),
            'voting_deadline_days' => (int) self::get(self::KEY_VOTING_DEADLINE_DAYS, 7),
            'auto_approve_budget_limit' => (float) self::get(self::KEY_AUTO_APPROVE_BUDGET_LIMIT, 1000),
            'approval_deadline_days' => (int) self::get(self::KEY_APPROVAL_DEADLINE_DAYS, 15),
            'enabled_approval_types' => $enabledTypes,
            'email_notifications' => (bool) self::get(self::KEY_EMAIL_NOTIFICATIONS, true),
            'reminder_notifications' => (bool) self::get(self::KEY_REMINDER_NOTIFICATIONS, true),
            'voting_reminders' => (bool) self::get(self::KEY_VOTING_REMINDERS, true),
            'allow_admin_approval' => (bool) self::get(self::KEY_ALLOW_ADMIN_APPROVAL, false),
        ];
    }
}
