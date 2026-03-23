<?php

namespace Modules\Projection\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Projection\App\Services\ProjectionApiService;

class ProjectionCardTemplateController extends Controller
{
    public function __construct(
        private ProjectionApiService $api
    ) {}

    public function index()
    {
        $templates = $this->api->getCardTemplates();
        return view('projection::admin.card-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('projection::admin.card-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'title_label' => 'nullable|string|max:128',
            'subtitle_label' => 'nullable|string|max:128',
            'background_type' => 'nullable|in:solid,gradient,image',
            'background_value' => 'nullable|string',
            'font_family' => 'nullable|string|max:128',
            'is_default' => 'nullable|boolean',
        ]);
        $this->api->createCardTemplate($request->all());
        return redirect()->route('admin.projection.card-templates.index')
            ->with('success', 'Template de card criado.');
    }

    public function edit(int $id)
    {
        $template = $this->api->getCardTemplate($id);
        if (! $template) {
            abort(404);
        }
        return view('projection::admin.card-templates.edit', compact('template'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255',
            'title_label' => 'nullable|string|max:128',
            'subtitle_label' => 'nullable|string|max:128',
            'background_type' => 'nullable|in:solid,gradient,image',
            'background_value' => 'nullable|string',
            'font_family' => 'nullable|string|max:128',
            'is_default' => 'nullable|boolean',
        ]);
        $this->api->updateCardTemplate($id, $request->all());
        return redirect()->route('admin.projection.card-templates.index')
            ->with('success', 'Template atualizado.');
    }

    public function destroy(int $id)
    {
        $this->api->deleteCardTemplate($id);
        return redirect()->route('admin.projection.card-templates.index')
            ->with('success', 'Template excluído.');
    }
}
