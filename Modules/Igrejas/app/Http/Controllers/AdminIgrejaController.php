<?php

namespace Modules\Igrejas\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Igrejas\App\Http\Requests\StoreIgrejaRequest;
use Modules\Igrejas\App\Http\Requests\UpdateIgrejaRequest;
use Modules\Igrejas\Models\Igreja;

class AdminIgrejaController extends Controller
{
    public function index()
    {
        $igrejas = Igreja::query()
            ->orderBy('nome')
            ->paginate(15);

        return view('igrejas::admin.index', compact('igrejas'));
    }

    public function create()
    {
        $isLideranca = request()->routeIs('lideranca.igrejas.*');
        $igreja = new Igreja();
        $formAction = $isLideranca ? route('lideranca.igrejas.store') : route('admin.igrejas.store');
        $formMethod = 'POST';
        $layout = $isLideranca ? 'liderancapanel::components.layouts.master' : 'admin::components.layouts.master';
        $pageTitle = 'Nova Igreja';

        return view('igrejas::admin.create', compact('igreja', 'formAction', 'formMethod', 'layout', 'pageTitle'));
    }

    public function store(StoreIgrejaRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $request->file('logo_path')->store('igrejas', 'public');
        }

        Igreja::create($data);

        $route = request()->routeIs('lideranca.igrejas.*') ? 'lideranca.igrejas.index' : 'admin.igrejas.index';

        return redirect()
            ->route($route)
            ->with('success', 'Igreja cadastrada com sucesso.');
    }

    public function edit(Igreja $igreja)
    {
        $isLideranca = request()->routeIs('lideranca.igrejas.*');
        $formAction = $isLideranca ? route('lideranca.igrejas.update', $igreja) : route('admin.igrejas.update', $igreja);
        $formMethod = 'PUT';
        $layout = $isLideranca ? 'liderancapanel::components.layouts.master' : 'admin::components.layouts.master';
        $pageTitle = 'Editar Igreja';

        return view('igrejas::admin.edit', compact('igreja', 'formAction', 'formMethod', 'layout', 'pageTitle'));
    }

    public function update(UpdateIgrejaRequest $request, Igreja $igreja)
    {
        $data = $request->validated();

        if ($request->hasFile('logo_path')) {
            if ($igreja->logo_path) {
                Storage::disk('public')->delete($igreja->logo_path);
            }
            $data['logo_path'] = $request->file('logo_path')->store('igrejas', 'public');
        }

        $igreja->update($data);

        $route = request()->routeIs('lideranca.igrejas.*') ? 'lideranca.igrejas.index' : 'admin.igrejas.index';

        return redirect()
            ->route($route)
            ->with('success', 'Igreja atualizada com sucesso.');
    }

    public function destroy(Igreja $igreja)
    {
        if ($igreja->logo_path) {
            Storage::disk('public')->delete($igreja->logo_path);
        }

        $igreja->delete();

        $route = request()->routeIs('lideranca.igrejas.*') ? 'lideranca.igrejas.index' : 'admin.igrejas.index';

        return redirect()
            ->route($route)
            ->with('success', 'Igreja removida com sucesso.');
    }
}

