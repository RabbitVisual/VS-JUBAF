<?php

namespace Modules\Treasury\App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromCollection, ShouldAutoSize, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $entries;

    protected $startDate;

    protected $endDate;

    protected $totalIncome;

    protected $totalExpense;

    protected $balance;

    public function __construct(Collection $entries, $startDate, $endDate, $totalIncome, $totalExpense, $balance)
    {
        $this->entries = $entries;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalIncome = $totalIncome;
        $this->totalExpense = $totalExpense;
        $this->balance = $balance;
    }

    public function collection()
    {
        return $this->entries;
    }

    public function headings(): array
    {
        return [
            'Data',
            'Tipo',
            'Categoria',
            'Título',
            'Descrição',
            'Valor (R$)',
            'Método de Pagamento',
            'Referência',
            'Usuário',
            'Campanha',
            'Ministério',
        ];
    }

    public function map($entry): array
    {
        return [
            $entry->entry_date->format('d/m/Y'),
            $entry->type === 'income' ? 'Entrada' : 'Saída',
            ucfirst(str_replace('_', ' ', $entry->category)),
            $entry->title,
            $entry->description ?? '-',
            number_format($entry->amount, 2, ',', '.'),
            $entry->payment_method ? ucfirst(str_replace('_', ' ', $entry->payment_method)) : '-',
            $entry->reference_number ?? '-',
            $entry->user->name ?? '-',
            $entry->campaign->name ?? '-',
            $entry->ministry->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Relatório Financeiro';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Data
            'B' => 10,  // Tipo
            'C' => 18,  // Categoria
            'D' => 30,  // Título
            'E' => 40,  // Descrição
            'F' => 15,  // Valor
            'G' => 20,  // Método
            'H' => 20,  // Referência
            'I' => 25,  // Usuário
            'J' => 30,  // Campanha
            'K' => 25,  // Ministério
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->entries->count() + 2; // +2 para cabeçalho (1) e linha de totais (1)

        // Estilo do cabeçalho
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'], // Azul
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Altura da linha do cabeçalho
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Estilo das linhas de dados
        $sheet->getStyle('A2:K'.($lastRow - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Linhas alternadas
        for ($i = 2; $i <= $lastRow - 1; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A'.$i.':K'.$i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB'],
                    ],
                ]);
            }
        }

        // Linha de totais
        $sheet->getStyle('A'.$lastRow.':K'.$lastRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB'],
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Adicionar linha de totais
        $totalRow = $this->entries->count() + 2;
        $sheet->setCellValue('D'.$totalRow, 'TOTAIS:');
        $sheet->setCellValue('F'.$totalRow, number_format($this->totalIncome, 2, ',', '.'));
        $sheet->getStyle('D'.$totalRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
        $sheet->getStyle('F'.$totalRow)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Alinhamento de colunas
        $sheet->getStyle('A2:A'.($lastRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B'.($lastRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F'.($lastRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Cor para entradas (verde) e saídas (vermelho)
        foreach ($this->entries as $index => $entry) {
            $row = $index + 2;
            if ($entry->type === 'income') {
                $sheet->getStyle('F'.$row)->getFont()->getColor()->setRGB('16A34A'); // Verde
            } else {
                $sheet->getStyle('F'.$row)->getFont()->getColor()->setRGB('DC2626'); // Vermelho
            }
        }

        return $sheet;
    }
}
