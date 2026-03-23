<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CepRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando importação de faixas de CEP...');

        // Caminho do arquivo CSV
        $csvPath = database_path('seeders/data/ceps.csv');

        if (! File::exists($csvPath)) {
            $this->command->warn("Arquivo CSV não encontrado em: {$csvPath}");
            $this->command->info('Por favor, baixe o arquivo CSV de: https://gist.github.com/tamnil/792a6a66f6df9fc028041587cfca0c3d');
            $this->command->info('E salve em: database/seeders/data/ceps.csv');

            return;
        }

        // Evita apagar dados existentes (conforme regra de segurança de seeders)
        if (DB::table('cep_ranges')->count() > 0) {
            $this->command->info('Faixas de CEP já existem no banco. Pulando importação para preservar dados.');
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // Pula o cabeçalho
        $count = 0;
        $batch = [];

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 4) {
                continue;
            }

            $uf = strtoupper(trim($row[0] ?? ''));
            $cidade = trim($row[1] ?? '');
            $cepDe = preg_replace('/\D/', '', trim($row[2] ?? ''));
            $cepAte = preg_replace('/\D/', '', trim($row[3] ?? ''));

            // Validações básicas
            if (empty($uf) || empty($cidade) || empty($cepDe)) {
                continue;
            }

            // Se CEP ATÉ estiver vazio, usa o CEP DE
            if (empty($cepAte)) {
                $cepAte = $cepDe;
            }

            // Determina tipo baseado na cidade
            $tipo = null;
            if (Str::contains(strtolower($cidade), 'total')) {
                $tipo = 'total';
            } elseif (Str::contains(strtolower($cidade), 'urbano') || Str::contains(strtolower($cidade), 'sede')) {
                $tipo = 'urbano';
            } elseif (Str::contains(strtolower($cidade), 'rural')) {
                $tipo = 'rural';
            }

            $batch[] = [
                'uf' => $uf,
                'cidade' => $cidade,
                'cep_de' => str_pad($cepDe, 8, '0', STR_PAD_LEFT),
                'cep_ate' => str_pad($cepAte, 8, '0', STR_PAD_LEFT),
                'tipo' => $tipo,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insere em lotes de 1000
            if (count($batch) >= 1000) {
                DB::table('cep_ranges')->insert($batch);
                $count += count($batch);
                $this->command->info("Importados {$count} registros...");
                $batch = [];
            }
        }

        // Insere o restante
        if (! empty($batch)) {
            DB::table('cep_ranges')->insert($batch);
            $count += count($batch);
        }

        fclose($file);

        $this->command->info("Importação concluída! Total de {$count} faixas de CEP importadas.");
    }
}
