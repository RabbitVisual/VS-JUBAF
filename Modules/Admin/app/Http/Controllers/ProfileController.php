<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the admin profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('role');

        return view('admin::profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('admin::profile.edit', compact('user'));
    }

    /**
     * Update the admin profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',

            // Identificação
            'cpf' => 'nullable|string|max:14',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'marital_status' => 'nullable|in:solteiro,casado,divorciado,viuvo,uniao_estavel',

            // Endereço
            'zip_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',

            // Contato de Emergência
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',

            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Generate full name
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        // Hash password if provided
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Upload photo
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        // Remove password confirmation
        unset($validated['password_confirmation']);

        $user->update($validated);

        // Refresh user to get updated data
        $user->refresh();


        return redirect()->route('admin.profile.show')
            ->with('success', 'Perfil atualizado com sucesso!');
    }
}
