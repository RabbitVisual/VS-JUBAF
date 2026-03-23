<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialVolunteer;

class VolunteerController extends Controller
{
    public function index(): View
    {
        $volunteers = SocialVolunteer::with('user')
            ->latest()
            ->paginate(15);

        $totalHours = SocialVolunteer::sum('total_hours');

        return view('socialaction::admin.volunteers.index', compact('volunteers', 'totalHours'));
    }

    public function create(): View
    {
        // Usuários que ainda não são voluntários
        $usersWithoutVolunteer = User::whereDoesntHave('socialVolunteer')
            ->orderBy('name')
            ->get();

        return view('socialaction::admin.volunteers.create', [
            'users'             => $usersWithoutVolunteer,
            'skillsLabels'      => SocialVolunteer::skillsLabels(),
            'availabilityLabels'=> SocialVolunteer::availabilityLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'      => 'required|exists:users,id|unique:social_volunteers,user_id',
            'role'         => 'required|in:leader,helper',
            'phone'        => 'nullable|string|max:20',
            'skills'       => 'nullable|array',
            'skills.*'     => 'string|in:food_prep,distribution,transport,administration,pastoral,health,teaching,other',
            'availability' => 'nullable|array',
            'availability.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'bio'          => 'nullable|string|max:1000',
            'is_active'    => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        SocialVolunteer::create($validated);

        return redirect()->route('socialaction.admin.volunteers.index')
            ->with('success', 'Voluntário cadastrado com sucesso.');
    }

    public function edit(int $id): View
    {
        $volunteer = SocialVolunteer::with('user')->findOrFail($id);

        return view('socialaction::admin.volunteers.edit', [
            'volunteer'         => $volunteer,
            'skillsLabels'      => SocialVolunteer::skillsLabels(),
            'availabilityLabels'=> SocialVolunteer::availabilityLabels(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $volunteer = SocialVolunteer::findOrFail($id);

        $validated = $request->validate([
            'role'         => 'required|in:leader,helper',
            'phone'        => 'nullable|string|max:20',
            'skills'       => 'nullable|array',
            'skills.*'     => 'string',
            'availability' => 'nullable|array',
            'availability.*' => 'string',
            'total_hours'  => 'nullable|numeric|min:0',
            'bio'          => 'nullable|string|max:1000',
            'is_active'    => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $volunteer->update($validated);

        return redirect()->route('socialaction.admin.volunteers.index')
            ->with('success', 'Voluntário atualizado com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        SocialVolunteer::findOrFail($id)->delete();

        return redirect()->route('socialaction.admin.volunteers.index')
            ->with('success', 'Voluntário removido.');
    }
}
