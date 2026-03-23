<?php

namespace Modules\Admin\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FamilyDemographicsExport implements FromArray, WithStyles, WithTitle
{
    public function __construct(
        protected array $report
    ) {}

    public function title(): string
    {
        return 'Demográfico Familiar';
    }

    public function array(): array
    {
        $comp = $this->report['composition'] ?? [];
        $byNeighborhood = $this->report['by_neighborhood'] ?? [];
        $highlights = $this->report['pastoral_highlights'] ?? [];
        $churchName = \App\Models\Settings::get('site_name', 'Igreja Batista Avenida');

        $rows = [
            ['Relatório Demográfico Familiar - ' . $churchName . ' - ' . now()->format('d/m/Y')],
            [],
            ['Composição dos Núcleos', '', ''],
            ['Famílias completas (casal + filhos)', $comp['complete_families'] ?? 0, ($comp['pct_complete'] ?? 0) . '%'],
            ['Famílias monoparentais', $comp['monoparental'] ?? 0, ($comp['pct_monoparental'] ?? 0) . '%'],
            ['Casais (sem filhos no cadastro)', $comp['couples'] ?? 0, ($comp['pct_couples'] ?? 0) . '%'],
            ['Membros individuais', $comp['individuals'] ?? 0, ($comp['pct_individuals'] ?? 0) . '%'],
            ['Total de núcleos', $comp['total_nuclei'] ?? 0, '100%'],
            [],
            ['Famílias por região (bairro/cidade)', 'Quantidade', ''],
        ];

        foreach ($byNeighborhood as $area => $count) {
            $rows[] = [$area, $count, ''];
        }

        $rows[] = [];
        $rows[] = ['Destaques pastorais', '', ''];
        foreach ($highlights as $h) {
            $rows[] = [$h, '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
