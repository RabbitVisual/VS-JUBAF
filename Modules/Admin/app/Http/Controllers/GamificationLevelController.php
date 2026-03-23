<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Gamification\App\Models\GamificationLevel;

class GamificationLevelController extends Controller
{
    /**
     * Display a listing of gamification levels.
     */
    public function index()
    {
        $levels = GamificationLevel::ordered()->get();

        return view('admin::gamification-levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new level.
     */
    public function create()
    {
        $icons = $this->getAvailableIcons();

        return view('admin::gamification-levels.create', compact('icons'));
    }

    /**
     * Store a newly created level.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'points_min' => 'required|integer|min:0',
            'points_max' => 'nullable|integer|min:0|gt:points_min',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        GamificationLevel::create($validated);

        return redirect()->route('admin.gamification-levels.index')
            ->with('success', 'Nível criado com sucesso!');
    }

    /**
     * Show the form for editing the specified level.
     */
    public function edit(GamificationLevel $gamificationLevel)
    {
        $icons = $this->getAvailableIcons();

        return view('admin::gamification-levels.edit', compact('gamificationLevel', 'icons'));
    }

    /**
     * Update the specified level.
     */
    public function update(Request $request, GamificationLevel $gamificationLevel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'points_min' => 'required|integer|min:0',
            'points_max' => 'nullable|integer|min:0|gt:points_min',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $gamificationLevel->update($validated);

        return redirect()->route('admin.gamification-levels.index')
            ->with('success', 'Nível atualizado com sucesso!');
    }

    /**
     * Remove the specified level.
     */
    public function destroy(GamificationLevel $gamificationLevel)
    {
        $gamificationLevel->delete();

        return redirect()->route('admin.gamification-levels.index')
            ->with('success', 'Nível removido com sucesso!');
    }

    /**
     * Retorna ícones disponíveis
     */
    private function getAvailableIcons()
    {
        return [
            'baptism' => 'Batismo',
            'trophy' => 'Troféu',
            'star' => 'Estrela',
            'achievement' => 'Conquista',
            'cake' => 'Bolo/Aniversário',
            'celebration' => 'Celebração',
            'active' => 'Ativo',
            'check' => 'Check',
            'check-circle' => 'Check Círculo',
            'target' => 'Alvo',
            'default' => 'Padrão',
        ];
    }
}
