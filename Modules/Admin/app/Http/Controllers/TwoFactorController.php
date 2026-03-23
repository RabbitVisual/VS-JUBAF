<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorController extends Controller
{
    public function __construct(
        protected TwoFactorAuthService $twoFactor
    ) {}

    /**
     * Exibe a página de configuração 2FA (status atual ou fluxo de ativação com QR).
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('role');

        $setupSecret = session('2fa_setup_secret');
        $qrPngBase64 = null;
        if ($setupSecret) {
            $issuer = \App\Models\Settings::get('site_name', 'Vertex CBAV');
            $url = $this->twoFactor->getQrCodeUrl($user->email, $setupSecret, $issuer);
            $qrPng = QrCode::format('png')->size(200)->margin(1)->generate($url);
            $qrPngBase64 = 'data:image/png;base64,' . base64_encode($qrPng);
        }

        return view('admin::profile.two-factor', [
            'user' => $user,
            'qrPngBase64' => $qrPngBase64,
        ]);
    }

    /**
     * Inicia o setup: gera secret e redireciona para exibir QR.
     */
    public function setup(Request $request)
    {
        $user = Auth::user();
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.profile.2fa.show')
                ->with('info', '2FA já está ativo para sua conta.');
        }

        $secret = $this->twoFactor->generateSecret();
        session(['2fa_setup_secret' => $secret]);

        return redirect()->route('admin.profile.2fa.show');
    }

    /**
     * Confirma o setup com o código de 6 dígitos e ativa 2FA para o usuário.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6|regex:/^[0-9]+$/',
        ], [
            'code.required' => 'Informe o código de 6 dígitos do aplicativo.',
            'code.size' => 'O código deve ter exatamente 6 dígitos.',
        ]);

        $user = Auth::user();
        $secret = session('2fa_setup_secret');
        if (! $secret) {
            return redirect()->route('admin.profile.2fa.show')
                ->with('error', 'Sessão de configuração expirada. Inicie a ativação novamente.');
        }

        if (! $this->twoFactor->verify($secret, $request->input('code'))) {
            return redirect()->route('admin.profile.2fa.show')
                ->withErrors(['code' => 'Código inválido. Verifique o aplicativo e tente novamente.']);
        }

        $this->twoFactor->enableForUser($user, $secret);
        session()->forget('2fa_setup_secret');

        return redirect()->route('admin.profile.2fa.show')
            ->with('success', 'Autenticação em duas etapas ativada com sucesso.');
    }

    /**
     * Desativa 2FA para o usuário (exige senha atual por segurança).
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string|current_password',
        ], [
            'password.required' => 'Informe sua senha para desativar o 2FA.',
        ]);

        $user = Auth::user();
        $this->twoFactor->disableForUser($user);
        session()->forget('2fa_setup_secret');

        return redirect()->route('admin.profile.2fa.show')
            ->with('success', 'Autenticação em duas etapas desativada.');
    }

    /**
     * Retorna a imagem do QR Code (usado quando secret está na sessão).
     */
    public function qrImage(Request $request)
    {
        $secret = session('2fa_setup_secret');
        if (! $secret) {
            abort(404);
        }

        $user = Auth::user();
        $issuer = \App\Models\Settings::get('site_name', 'Vertex CBAV');
        $url = $this->twoFactor->getQrCodeUrl($user->email, $secret, $issuer);
        $png = QrCode::format('png')->size(200)->margin(1)->generate($url);

        return response($png)->header('Content-Type', 'image/png');
    }
}
