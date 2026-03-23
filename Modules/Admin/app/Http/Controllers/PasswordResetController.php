<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetLog;
use App\Models\Settings;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    /**
     * Display a listing of the password reset requests.
     */
    public function index()
    {
        $resets = PasswordResetLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin::password-resets.index', compact('resets'));
    }

    /**
     * Show the settings for password reset emails.
     */
    public function settings()
    {
        $settings = [
            'recovery_email_subject' => Settings::get('recovery_email_subject', 'Recuperação de Senha'),
            'recovery_email_title' => Settings::get('recovery_email_title', 'Recuperação de Senha'),
            'recovery_email_footer' => Settings::get('recovery_email_footer', 'Powered by Vertex Solutions LTDA'),
        ];

        return view('admin::password-resets.settings', compact('settings'));
    }

    /**
     * Update settings for password reset emails.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'recovery_email_subject' => 'required|string|max:255',
            'recovery_email_title' => 'required|string|max:255',
            'recovery_email_footer' => 'required|string|max:255',
        ]);

        Settings::set('recovery_email_subject', $request->input('recovery_email_subject'));
        Settings::set('recovery_email_title', $request->input('recovery_email_title'));
        Settings::set('recovery_email_footer', $request->input('recovery_email_footer'));

        return back()->with('success', 'Configurações de e-mail atualizadas com sucesso!');
    }
}
