<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CepRange;
use App\Services\CepService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CepRangeController extends Controller
{
    protected CepService $cepService;

    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    /**
     * Display a listing of CEP ranges.
     */
    public function index(Request $request)
    {
        $query = CepRange::query();

        // Filtros
        if ($request->filled('uf')) {
            $query->where('uf', $request->uf);
        }

        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', '%'.$request->cidade.'%');
        }

        if ($request->filled('cep')) {
            $cep = preg_replace('/\D/', '', $request->cep);
            $query->where(function ($q) use ($cep) {
                $q->where('cep_de', '<=', $cep)
                    ->where('cep_ate', '>=', $cep);
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Paginação
        $cepRanges = $query->paginate(20)->withQueryString();

        // Estatísticas
        $stats = [
            'total' => CepRange::count(),
            'por_uf' => CepRange::select('uf', DB::raw('count(*) as total'))
                ->groupBy('uf')
                ->orderBy('total', 'desc')
                ->get(),
        ];

        // Lista de UFs para filtro
        $ufs = CepRange::select('uf')
            ->distinct()
            ->orderBy('uf')
            ->pluck('uf');

        return view('admin::cep-ranges.index', compact('cepRanges', 'stats', 'ufs'));
    }

    /**
     * Show the form for creating a new CEP range.
     */
    public function create()
    {
        $ufs = CepRange::select('uf')
            ->distinct()
            ->orderBy('uf')
            ->pluck('uf');

        return view('admin::cep-ranges.create', compact('ufs'));
    }

    /**
     * Store a newly created CEP range.
     */
    public function store(Request $request)
    {
        // Limpa os CEPs antes da validação para evitar erros de tamanho devido à máscara
        if ($request->has('cep_de')) {
            $request->merge(['cep_de' => preg_replace('/\D/', '', $request->cep_de)]);
        }
        if ($request->has('cep_ate')) {
            $request->merge(['cep_ate' => preg_replace('/\D/', '', $request->cep_ate)]);
        }

        $validated = $request->validate([
            'uf' => 'required|string|size:2|uppercase',
            'cidade' => 'required|string|max:255',
            'cep_de' => 'required|string|size:8',
            'cep_ate' => 'required|string|size:8',
            'tipo' => 'nullable|string|max:50',
        ]);

        // Remover pré-sanitização redundante já que mergeamos no request
        // $validated['cep_de'] = preg_replace('/\D/', '', $validated['cep_de']);
        // $validated['cep_ate'] = preg_replace('/\D/', '', $validated['cep_ate']);

        // Valida se CEP ATÉ é maior que CEP DE
        if ($validated['cep_ate'] < $validated['cep_de']) {
            return back()->withErrors(['cep_ate' => 'CEP ATÉ deve ser maior ou igual ao CEP DE'])->withInput();
        }

        // Verifica sobreposição de faixas
        $overlap = CepRange::where('uf', $validated['uf'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($query) use ($validated) {
                    $query->where('cep_de', '<=', $validated['cep_de'])
                        ->where('cep_ate', '>=', $validated['cep_de']);
                })->orWhere(function ($query) use ($validated) {
                    $query->where('cep_de', '<=', $validated['cep_ate'])
                        ->where('cep_ate', '>=', $validated['cep_ate']);
                })->orWhere(function ($query) use ($validated) {
                    $query->where('cep_de', '>=', $validated['cep_de'])
                        ->where('cep_ate', '<=', $validated['cep_ate']);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['cep_de' => 'Esta faixa de CEP já existe ou sobrepõe outra faixa'])->withInput();
        }

        CepRange::create($validated);

        return redirect()->route('admin.cep-ranges.index')
            ->with('success', 'Faixa de CEP criada com sucesso!');
    }

    /**
     * Display the specified CEP range.
     */
    public function show(CepRange $cepRange)
    {
        return view('admin::cep-ranges.show', compact('cepRange'));
    }

    /**
     * Show the form for editing the specified CEP range.
     */
    public function edit(CepRange $cepRange)
    {
        $ufs = CepRange::select('uf')
            ->distinct()
            ->orderBy('uf')
            ->pluck('uf');

        return view('admin::cep-ranges.edit', compact('cepRange', 'ufs'));
    }

    /**
     * Update the specified CEP range.
     */
    public function update(Request $request, CepRange $cepRange)
    {
        // Limpa os CEPs antes da validação para evitar erros de tamanho devido à máscara
        if ($request->has('cep_de')) {
            $request->merge(['cep_de' => preg_replace('/\D/', '', $request->cep_de)]);
        }
        if ($request->has('cep_ate')) {
            $request->merge(['cep_ate' => preg_replace('/\D/', '', $request->cep_ate)]);
        }

        $validated = $request->validate([
            'uf' => 'required|string|size:2|uppercase',
            'cidade' => 'required|string|max:255',
            'cep_de' => 'required|string|size:8',
            'cep_ate' => 'required|string|size:8',
            'tipo' => 'nullable|string|max:50',
        ]);

        // Remover pré-sanitização redundante já que mergeamos no request
        // $validated['cep_de'] = preg_replace('/\D/', '', $validated['cep_de']);
        // $validated['cep_ate'] = preg_replace('/\D/', '', $validated['cep_ate']);

        // Valida se CEP ATÉ é maior que CEP DE
        if ($validated['cep_ate'] < $validated['cep_de']) {
            return back()->withErrors(['cep_ate' => 'CEP ATÉ deve ser maior ou igual ao CEP DE'])->withInput();
        }

        // Verifica sobreposição de faixas (excluindo o registro atual)
        $overlap = CepRange::where('id', '!=', $cepRange->id)
            ->where('uf', $validated['uf'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($query) use ($validated) {
                    $query->where('cep_de', '<=', $validated['cep_de'])
                        ->where('cep_ate', '>=', $validated['cep_de']);
                })->orWhere(function ($query) use ($validated) {
                    $query->where('cep_de', '<=', $validated['cep_ate'])
                        ->where('cep_ate', '>=', $validated['cep_ate']);
                })->orWhere(function ($query) use ($validated) {
                    $query->where('cep_de', '>=', $validated['cep_de'])
                        ->where('cep_ate', '<=', $validated['cep_ate']);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['cep_de' => 'Esta faixa de CEP já existe ou sobrepõe outra faixa'])->withInput();
        }

        $cepRange->update($validated);

        return redirect()->route('admin.cep-ranges.index')
            ->with('success', 'Faixa de CEP atualizada com sucesso!');
    }

    /**
     * Remove the specified CEP range.
     */
    public function destroy(CepRange $cepRange)
    {
        $cepRange->delete();

        return redirect()->route('admin.cep-ranges.index')
            ->with('success', 'Faixa de CEP removida com sucesso!');
    }

    /**
     * Get distinct locations for API consumption.
     */
    public function getLocations()
    {
        try {
            $locations = CepRange::select('uf', 'cidade')
                ->selectRaw("CONCAT(cidade, ' - ', uf) as name")
                ->distinct()
                ->orderBy('uf')
                ->orderBy('cidade')
                ->get()
                ->map(function ($location) {
                    return [
                        'id' => $location->cidade.' - '.$location->uf,
                        'name' => $location->name,
                        'uf' => $location->uf,
                        'cidade' => $location->cidade,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $locations,
                'message' => 'Localidades carregadas com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar localidades: '.$e->getMessage(),
            ], 500);
        }
    }
}
