<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Gamification\App\Models\Badge;

class BadgeController extends Controller
{
    /**
     * Display a listing of badges.
     */
    public function index()
    {
        $badges = Badge::ordered()->get();

        return view('admin::badges.index', compact('badges'));
    }

    /**
     * Show the form for creating a new badge.
     */
    public function create()
    {
        $icons = $this->getAvailableIcons();
        $criteriaTypes = $this->getCriteriaTypes();

        return view('admin::badges.create', compact('icons', 'criteriaTypes'));
    }

    /**
     * Store a newly created badge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'points_required' => 'nullable|integer|min:0',
            'criteria_type' => 'required|in:manual,auto,points,time_congregating,is_baptized,profile_complete,ministries_count,ministries_joined,bible_favorites,contributions_made,events_attended',
            'criteria_value' => 'nullable|array',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        Badge::create($validated);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge criado com sucesso!');
    }

    /**
     * Display the specified badge.
     */
    public function show(Badge $badge)
    {
        $users = $badge->users()->with('role')->paginate(20);

        return view('admin::badges.show', compact('badge', 'users'));
    }

    /**
     * Show the form for editing the specified badge.
     */
    public function edit(Badge $badge)
    {
        $icons = $this->getAvailableIcons();
        $criteriaTypes = $this->getCriteriaTypes();

        return view('admin::badges.edit', compact('badge', 'icons', 'criteriaTypes'));
    }

    /**
     * Update the specified badge.
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'points_required' => 'nullable|integer|min:0',
            'criteria_type' => 'required|in:manual,auto,points,time_congregating,is_baptized,profile_complete,ministries_count,ministries_joined,bible_favorites,contributions_made,events_attended',
            'criteria_value' => 'nullable|array',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $badge->update($validated);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge atualizado com sucesso!');
    }

    /**
     * Remove the specified badge.
     */
    public function destroy(Badge $badge)
    {
        $badge->delete();

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge removido com sucesso!');
    }

    /**
     * Atribuir badge manualmente a um usuário
     */
    public function awardToUser(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (! $user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id, [
                'earned_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            return back()->with('success', "Badge '{$badge->name}' atribuído ao usuário {$user->name}!");
        }

        return back()->with('error', 'Usuário já possui este badge!');
    }

    /**
     * Remover badge de um usuário
     */
    public function removeFromUser(Badge $badge, User $user)
    {
        $user->badges()->detach($badge->id);

        return back()->with('success', "Badge '{$badge->name}' removido do usuário {$user->name}!");
    }

    /**
     * Retorna ícones disponíveis (Font Awesome Pro – nome curto)
     */
    private function getAvailableIcons()
    {
        return [
            'award' => 'Prêmio / Conquista',
            'book-bible' => 'Bíblia',
            'cake-candles' => 'Aniversário',
            'calendar-check' => 'Evento confirmado',
            'crown' => 'Coroa',
            'champagne-glasses' => 'Celebração',
            'circle-check' => 'Concluído',
            'circle-user' => 'Perfil / Rosto',
            'door-open' => 'Boas-vindas',
            'eye' => 'Visão / Líder',
            'gift' => 'Generosidade',
            'hand-holding-dollar' => 'Dízimo / Doação',
            'hand-holding-heart' => 'Serviço',
            'handshake-angle' => 'Voluntário',
            'hands-praying' => 'Oração / Intercessor',
            'heart' => 'Amor / Fidelidade',
            'id-card' => 'Cadastro completo',
            'landmark' => 'Coluna financeira',
            'medal' => 'Medalha',
            'school' => 'EBD / Estudos',
            'star' => 'Estrela',
            'ticket' => 'Presença / Evento',
            'trophy' => 'Troféu',
            'user' => 'Usuário',
            'user-check' => 'Cadastro ativo',
            'users' => 'Ministérios / Comunidade',
            'users-rays' => 'Liderança',
            'water' => 'Batismo',
        ];
    }

    /**
     * Retorna tipos de critérios
     */
    private function getCriteriaTypes()
    {
        return [
            'manual' => 'Manual (atribuir manualmente)',
            'auto' => 'Automático (verificar periodicamente)',
            'points' => 'Pontos Mínimos',
            'time_congregating' => 'Tempo Congregando (meses)',
            'is_baptized' => 'É Batizado',
            'profile_complete' => 'Perfil Completo (%)',
            'ministries_count' => 'Número de Ministérios',
            'ministries_joined' => 'Ministérios (ingressou)',
            'bible_favorites' => 'Versículos Favoritos',
            'contributions_made' => 'Contribuições Realizadas',
            'events_attended' => 'Eventos com Presença',
        ];
    }
}
