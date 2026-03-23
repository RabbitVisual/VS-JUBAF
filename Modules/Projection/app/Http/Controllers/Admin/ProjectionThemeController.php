<?php

namespace Modules\Projection\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Projection\App\Models\ProjectionTheme;
use Modules\Projection\App\Services\ProjectionApiService;

class ProjectionThemeController extends Controller
{
    public function __construct(
        private ProjectionApiService $api
    ) {}

    public function index()
    {
        $themes = $this->api->getThemes();
        return view('projection::admin.themes.index', compact('themes'));
    }

    public function create()
    {
        return view('projection::admin.themes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'background_type' => 'nullable|in:solid,gradient,image,video',
            'background_value' => 'nullable|string',
            'font_family' => 'nullable|string|max:128',
            'font_size_base' => 'nullable|string|max:32',
            'text_color' => 'nullable|string|max:32',
            'alignment' => 'nullable|string|max:16',
            'padding' => 'nullable|integer|min:0|max:255',
            'is_default' => 'nullable|boolean',
        ]);
        if ($request->input('background_type') === 'image' && $this->isUnsplashPageUrl($request->input('background_value'))) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Use a URL direta da imagem, não o link da página. No Unsplash: abra a foto → botão direito na imagem → "Copiar endereço da imagem". A URL deve apontar para a imagem (ex.: images.unsplash.com/...).');
        }
        $this->api->createTheme($request->all());
        return redirect()->route('admin.projection.themes.index')
            ->with('success', 'Tema criado com sucesso.');
    }

    private function isUnsplashPageUrl(?string $url): bool
    {
        if (! $url || ! str_contains(strtolower($url), 'unsplash.com')) {
            return false;
        }
        return ! str_contains(strtolower($url), 'images.unsplash.com');
    }

    public function edit(int $id)
    {
        $theme = $this->api->getTheme($id);
        if (! $theme) {
            abort(404);
        }
        return view('projection::admin.themes.edit', compact('theme'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255',
            'background_type' => 'nullable|in:solid,gradient,image,video',
            'background_value' => 'nullable|string',
            'font_family' => 'nullable|string|max:128',
            'font_size_base' => 'nullable|string|max:32',
            'text_color' => 'nullable|string|max:32',
            'alignment' => 'nullable|string|max:16',
            'padding' => 'nullable|integer|min:0|max:255',
            'is_default' => 'nullable|boolean',
        ]);
        if ($request->input('background_type') === 'image' && $this->isUnsplashPageUrl($request->input('background_value'))) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Use a URL direta da imagem, não o link da página. No Unsplash: abra a foto → botão direito na imagem → "Copiar endereço da imagem".');
        }
        $this->api->updateTheme($id, $request->all());
        return redirect()->route('admin.projection.themes.index')
            ->with('success', 'Tema atualizado.');
    }

    public function destroy(int $id)
    {
        $this->api->deleteTheme($id);
        return redirect()->route('admin.projection.themes.index')
            ->with('success', 'Tema excluído.');
    }

    public function setDefault(int $id)
    {
        $this->api->setThemeDefault($id);
        return redirect()->route('admin.projection.themes.index')
            ->with('success', 'Tema definido como padrão.');
    }
}
