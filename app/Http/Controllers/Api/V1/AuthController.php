<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Projection\App\Http\Controllers\Admin\ProjectionSettingsController;

/**
 * API v1 auth endpoints (e.g. desktop app login).
 */
class AuthController extends Controller
{
    /**
     * Login for the Vertex Projector desktop app.
     * POST /api/v1/auth/desktop-login
     * Body: { "email": "...", "password": "..." }
     * Returns: { "data": { "token": "...", "user": { "id", "name", "email" } } }
     * 403 if desktop login is disabled or user has no allowed role.
     */
    public function desktopLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $enabled = (bool) Settings::get(ProjectionSettingsController::KEY_DESKTOP_LOGIN_ENABLED, false);
        if (! $enabled) {
            return response()->json([
                'message' => 'Login no app desktop está desativado. Ative em Admin > Projeção > Configurações.',
            ], 403);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        $allowedRoles = Settings::get(ProjectionSettingsController::KEY_DESKTOP_ALLOWED_ROLES, null);
        if (is_string($allowedRoles)) {
            $allowedRoles = json_decode($allowedRoles, true);
        }
        if (is_array($allowedRoles) && count($allowedRoles) > 0) {
            $userRoleSlug = $user->role?->slug;
            if (! $userRoleSlug || ! in_array($userRoleSlug, $allowedRoles, true)) {
                Auth::logout();

                return response()->json([
                    'message' => 'Você não tem permissão para usar o app de projeção. Entre em contato com o administrador.',
                ], 403);
            }
        }

        $user->tokens()->where('name', 'projection-desktop')->delete();
        $token = $user->createToken('projection-desktop')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ]);
    }
}
