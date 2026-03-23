<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialBeneficiary;

class BeneficiaryController extends Controller
{
    public function index(Request $request): View
    {
        $query = SocialBeneficiary::latest();

        if ($request->filled('search')) {
            // full_name é encriptado, então busca via decrypt em PHP
            $search = strtolower($request->search);
            $query->get()->filter(fn ($b) => str_contains(strtolower($b->full_name ?? ''), $search));
            // Para pagination funcionar vamos buscar paginado e filtrar em memória seria mau de performance
            // Estratégia: buscar todos, filtrar e paginar manualmente
            $all = SocialBeneficiary::latest()->get()->filter(
                fn ($b) => str_contains(strtolower($b->full_name ?? ''), $search)
                    || str_contains(strtolower($b->city ?? ''), $search)
            );
            $beneficiaries = new \Illuminate\Pagination\LengthAwarePaginator(
                $all->forPage($request->page ?? 1, 15),
                $all->count(),
                15,
                $request->page ?? 1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            $beneficiaries = $query->paginate(15);
        }

        $stats = [
            'total'    => SocialBeneficiary::count(),
            'active'   => SocialBeneficiary::active()->count(),
            'inactive' => SocialBeneficiary::where('status', 'inactive')->count(),
            'flagged'  => SocialBeneficiary::where('status', 'flagged')->count(),
        ];

        return view('socialaction::admin.beneficiaries.index', compact('beneficiaries', 'stats'));
    }

    public function create(): View
    {
        return view('socialaction::admin.beneficiaries.create', [
            'needsLabels' => SocialBeneficiary::needsLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'state'        => 'nullable|string|size:2',
            'zip_code'     => 'nullable|string|max:10',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'family_size'  => 'required|integer|min:1|max:30',
            'monthly_income' => 'nullable|numeric|min:0',
            'needs'        => 'nullable|array',
            'needs.*'      => 'string|in:food,clothes,medicine,hygiene,other',
            'pastoral_notes' => 'nullable|string',
            'status'       => 'required|in:active,inactive,flagged',
        ]);

        SocialBeneficiary::create($validated);

        return redirect()->route('socialaction.admin.beneficiaries.index')
            ->with('success', 'Beneficiário cadastrado com sucesso.');
    }

    public function show(int $id): View
    {
        $beneficiary = SocialBeneficiary::with(['assistances.kit', 'assistances.pantryItem'])->findOrFail($id);
        $assistanceCount = $beneficiary->assistances()->count();
        $lastAssistance  = $beneficiary->assistances()->latest('registered_at')->first();

        return view('socialaction::admin.beneficiaries.show', compact('beneficiary', 'assistanceCount', 'lastAssistance'));
    }

    public function edit(int $id): View
    {
        $beneficiary = SocialBeneficiary::findOrFail($id);

        return view('socialaction::admin.beneficiaries.edit', [
            'beneficiary' => $beneficiary,
            'needsLabels' => SocialBeneficiary::needsLabels(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $beneficiary = SocialBeneficiary::findOrFail($id);

        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'state'        => 'nullable|string|size:2',
            'zip_code'     => 'nullable|string|max:10',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'family_size'  => 'required|integer|min:1|max:30',
            'monthly_income' => 'nullable|numeric|min:0',
            'needs'        => 'nullable|array',
            'needs.*'      => 'string',
            'pastoral_notes' => 'nullable|string',
            'status'       => 'required|in:active,inactive,flagged',
        ]);

        $beneficiary->update($validated);

        return redirect()->route('socialaction.admin.beneficiaries.index')
            ->with('success', 'Beneficiário atualizado com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        SocialBeneficiary::findOrFail($id)->delete();

        return redirect()->route('socialaction.admin.beneficiaries.index')
            ->with('success', 'Beneficiário removido com sucesso.');
    }
}
