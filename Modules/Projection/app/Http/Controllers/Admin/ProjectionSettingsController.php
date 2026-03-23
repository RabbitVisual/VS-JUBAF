<?php

namespace Modules\Projection\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectionSettingsController extends Controller
{
    public const KEY_VIEWER_ENABLED = 'projection_viewer_enabled';
    public const KEY_VIEWER_TOKEN = 'projection_viewer_token';
    public const KEY_DESKTOP_LOGIN_ENABLED = 'projection_desktop_login_enabled';
    public const KEY_DESKTOP_ALLOWED_ROLES = 'projection_desktop_allowed_roles';
    public const GROUP = 'projection';

    public function index(): View
    {
        $viewerEnabled = (bool) Settings::get(self::KEY_VIEWER_ENABLED, false);
        $viewerToken = (string) Settings::get(self::KEY_VIEWER_TOKEN, '');
        $viewerTokenMasked = $viewerToken !== '' ? '••••••••' . substr($viewerToken, -4) : '';

        $viewerScreenUrl = route('projection.screen.public') . '?viewer_token=SEU_TOKEN';

        $desktopLoginEnabled = (bool) Settings::get(self::KEY_DESKTOP_LOGIN_ENABLED, false);
        $desktopAllowedRoles = Settings::get(self::KEY_DESKTOP_ALLOWED_ROLES, null);
        if (is_string($desktopAllowedRoles)) {
            $desktopAllowedRoles = json_decode($desktopAllowedRoles, true) ?: [];
        }
        $desktopAllowedRoles = is_array($desktopAllowedRoles) ? $desktopAllowedRoles : [];
        $roles = \App\Models\Role::orderBy('name')->get(['id', 'name', 'slug']);

        return view('projection::admin.settings', [
            'viewerEnabled' => $viewerEnabled,
            'viewerToken' => $viewerToken,
            'viewerTokenMasked' => $viewerTokenMasked,
            'viewerStateUrl' => url('/api/v1/projection/viewer/state'),
            'viewerScreenUrl' => $viewerScreenUrl,
            'desktopLoginEnabled' => $desktopLoginEnabled,
            'desktopAllowedRoles' => $desktopAllowedRoles,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'projection_viewer_enabled' => 'nullable|boolean',
            'projection_viewer_token' => 'nullable|string|max:128',
            'projection_desktop_login_enabled' => 'nullable|boolean',
            'projection_desktop_allowed_roles' => 'nullable|array',
            'projection_desktop_allowed_roles.*' => 'string|exists:roles,slug',
        ]);

        Settings::set(
            self::KEY_VIEWER_ENABLED,
            $request->boolean('projection_viewer_enabled'),
            'boolean',
            self::GROUP,
            'Permitir tela de projeção sem login (via token)'
        );

        $newToken = $request->input('projection_viewer_token');
        if ($newToken !== null && $newToken !== '') {
            Settings::set(
                self::KEY_VIEWER_TOKEN,
                $newToken,
                'string',
                self::GROUP,
                'Token para acesso à tela sem login'
            );
        } elseif ($request->boolean('projection_viewer_enabled') && $request->input('projection_viewer_token_clear') === '1') {
            Settings::set(self::KEY_VIEWER_TOKEN, '', 'string', self::GROUP, 'Token para acesso à tela sem login');
        }

        Settings::set(
            self::KEY_DESKTOP_LOGIN_ENABLED,
            $request->boolean('projection_desktop_login_enabled'),
            'boolean',
            self::GROUP,
            'Permitir login no app desktop de projeção'
        );

        $allowedRoles = $request->input('projection_desktop_allowed_roles', []);
        $allowedRoles = is_array($allowedRoles) ? array_values(array_filter($allowedRoles)) : [];
        Settings::set(
            self::KEY_DESKTOP_ALLOWED_ROLES,
            json_encode($allowedRoles),
            'json',
            self::GROUP,
            'Roles permitidos para o app desktop (vazio = todos autenticados)'
        );

        Settings::clearCache();

        return redirect()->route('admin.projection.settings.index')
            ->with('success', 'Configurações de projeção atualizadas.');
    }
}
