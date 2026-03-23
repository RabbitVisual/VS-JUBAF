<?php

namespace Modules\Treasury\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreasuryPermission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'permission_level',
        'can_view_reports',
        'can_create_entries',
        'can_edit_entries',
        'can_delete_entries',
        'can_manage_campaigns',
        'can_manage_goals',
        'can_export_data',
    ];

    protected $casts = [
        'can_view_reports' => 'boolean',
        'can_create_entries' => 'boolean',
        'can_edit_entries' => 'boolean',
        'can_delete_entries' => 'boolean',
        'can_manage_campaigns' => 'boolean',
        'can_manage_goals' => 'boolean',
        'can_export_data' => 'boolean',
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se o usuário tem permissão para visualizar relatórios
     */
    public function canViewReports(): bool
    {
        return $this->can_view_reports || $this->permission_level === 'admin';
    }

    /**
     * Verifica se o usuário tem permissão para criar entradas
     */
    public function canCreateEntries(): bool
    {
        return $this->can_create_entries || in_array($this->permission_level, ['editor', 'admin']);
    }

    /**
     * Verifica se o usuário tem permissão para editar entradas
     */
    public function canEditEntries(): bool
    {
        return $this->can_edit_entries || in_array($this->permission_level, ['editor', 'admin']);
    }

    /**
     * Verifica se o usuário tem permissão para deletar entradas
     */
    public function canDeleteEntries(): bool
    {
        return $this->can_delete_entries || $this->permission_level === 'admin';
    }

    /**
     * Verifica se o usuário tem permissão para gerenciar campanhas
     */
    public function canManageCampaigns(): bool
    {
        return $this->can_manage_campaigns || $this->permission_level === 'admin';
    }

    /**
     * Verifica se o usuário tem permissão para gerenciar metas
     */
    public function canManageGoals(): bool
    {
        return $this->can_manage_goals || $this->permission_level === 'admin';
    }

    /**
     * Verifica se o usuário tem permissão para exportar dados
     */
    public function canExportData(): bool
    {
        return $this->can_export_data || $this->permission_level === 'admin';
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin(): bool
    {
        return $this->permission_level === 'admin';
    }

    /**
     * Retorna a permissão do usuário ou uma permissão "admin" sintética para admins do sistema sem registro.
     * Evita firstOrFail() quando o usuário é admin (role slug admin) mas não tem linha em treasury_permissions.
     */
    public static function forUserOrAdmin(User $user): self
    {
        $permission = self::where('user_id', $user->id)->first();
        if ($permission) {
            return $permission;
        }
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return self::adminDefault();
        }
        abort(403, 'Você não tem permissão para acessar a Tesouraria.');
    }

    /**
     * Instância sintética com permissão total (para admins do sistema sem registro em treasury_permissions).
     */
    public static function adminDefault(): self
    {
        $p = new self([
            'permission_level' => 'admin',
            'can_view_reports' => true,
            'can_create_entries' => true,
            'can_edit_entries' => true,
            'can_delete_entries' => true,
            'can_manage_campaigns' => true,
            'can_manage_goals' => true,
            'can_export_data' => true,
        ]);
        $p->id = 0;
        $p->exists = false;

        return $p;
    }
}
