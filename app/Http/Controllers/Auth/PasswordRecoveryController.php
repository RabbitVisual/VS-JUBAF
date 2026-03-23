<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetLog;
use App\Models\User;
use App\Notifications\Auth\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordRecoveryController extends Controller
{
    /**
     * Handle the password reset link request.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'value' => 'required',
            'type' => 'required|in:email,cpf',
        ]);

        $type = $request->input('type');
        $value = $request->input('value');
        $user = null;

        if ($type === 'email') {
            $user = User::where('email', $value)->first();
        } else {
            // Robust CPF matching
            $strippedCpf = preg_replace('/[^0-9]/', '', $value);
            $user = User::whereRaw("REPLACE(REPLACE(cpf, '.', ''), '-', '') = ?", [$strippedCpf])->first();
        }

        // Log the attempt
        $log = PasswordResetLog::create([
            'user_id' => $user?->id,
            'type' => $type,
            'identifier' => $value,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $user ? 'sent' : 'failed',
        ]);

        if (! $user) {
            // We return success anyway to prevent user enumeration,
            // but the frontend already checks if the user exists.
            return response()->json([
                'success' => true,
                'message' => 'Se os dados estiverem corretos, você receberá um e-mail com instruções.',
            ]);
        }

        // Create token (Laravel default)
        $token = Password::createToken($user);

        \Log::info('Triggering password reset notification', [
            'user_id' => $user->id,
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.'.config('mail.default').'.host', 'N/A'),
            'from' => config('mail.from.address'),
        ]);

        // Send Professional Notification
        $user->notify(new PasswordResetNotification($token, $user));

        return response()->json([
            'success' => true,
            'message' => 'Link de recuperação enviado com sucesso!',
        ]);
    }
}
