<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthCheckController extends Controller
{
    /**
     * Check if a user exists in the database by email or CPF
     */
    public function checkUserExist(Request $request)
    {
        $request->validate([
            'value' => 'required|string',
            'type' => 'required|string|in:email,cpf',
        ]);

        $value = $request->input('value');
        $type = $request->input('type');

        if ($type === 'cpf') {
            $value = preg_replace('/[^0-9]/', '', $value);
            // Robust search: strips dots and dashes from DB field to match stripped input
            $exists = User::whereRaw("REPLACE(REPLACE(cpf, '.', ''), '-', '') = ?", [$value])->exists();
        } else {
            $exists = User::where('email', $value)->exists();
        }

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Membro encontrado' : 'Membro não encontrado em nossos registros.',
        ]);
    }
}
