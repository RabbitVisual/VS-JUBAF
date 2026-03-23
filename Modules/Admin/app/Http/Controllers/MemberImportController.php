<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\App\Imports\MembersImport;

class MemberImportController extends Controller
{
    /**
     * Show the import form.
     */
    public function showImportForm()
    {
        return view('admin::users.import');
    }

    /**
     * Process the import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $import = new MembersImport;

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao importar membros: '.$e->getMessage());
        }

        $failures = $import->getFailures();
        if (count($failures) > 0) {
            return back()
                ->with('importErrors', $failures)
                ->with('warning', 'Importação concluída com erros em algumas linhas. Verifique a lista abaixo.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Membros importados com sucesso!');
    }

    /**
     * Download the template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'nome',
            'nome_abreviado',
            'email',
            'cpf',
            'sexo',
            'estado_civil',
            'telefone',
            'celular',
            'data_nascimento',
        ];

        $filename = 'modelo_importacao_membros.csv';
        $handle = fopen('php://output', 'w');

        // Corrige encoding para Excel Windows
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($handle, $headers, ';');

        // Exemplo
        fputcsv($handle, [
            'Joao Silva',
            'Joao',
            'joao@exemplo.com',
            '000.000.000-00',
            'Masculino',
            'Casado',
            '(00) 0000-0000',
            '(00) 00000-0000',
            '01/01/1980',
        ], ';');

        fclose($handle);

        return Response::make('', 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
