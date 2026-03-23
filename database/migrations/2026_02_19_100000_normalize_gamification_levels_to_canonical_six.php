<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CANONICAL_NAMES = [
        'Visitante',
        'Participante',
        'Membro Ativo',
        'Discípulo',
        'Líder',
        'Embaixador',
    ];

    private const CANONICAL_LEVELS = [
        ['name' => 'Visitante', 'description' => 'Início da jornada na comunidade.', 'icon' => 'user', 'color' => 'gray', 'points_min' => 0, 'points_max' => 99, 'order' => 1],
        ['name' => 'Participante', 'description' => 'Começando a se envolver com a igreja.', 'icon' => 'star', 'color' => 'yellow', 'points_min' => 100, 'points_max' => 299, 'order' => 2],
        ['name' => 'Membro Ativo', 'description' => 'Envolvimento constante nas atividades.', 'icon' => 'medal', 'color' => 'green', 'points_min' => 300, 'points_max' => 699, 'order' => 3],
        ['name' => 'Discípulo', 'description' => 'Dedicação ao serviço e crescimento.', 'icon' => 'hand-holding-heart', 'color' => 'blue', 'points_min' => 700, 'points_max' => 1499, 'order' => 4],
        ['name' => 'Líder', 'description' => 'Liderança e exemplo para os irmãos.', 'icon' => 'trophy', 'color' => 'purple', 'points_min' => 1500, 'points_max' => 2999, 'order' => 5],
        ['name' => 'Embaixador', 'description' => 'Pilar fundamental da comunidade.', 'icon' => 'crown', 'color' => 'rose', 'points_min' => 3000, 'points_max' => null, 'order' => 6],
    ];

    /**
     * Run the migrations.
     * Normaliza gamification_levels para exatamente os 6 níveis canônicos.
     */
    public function up(): void
    {
        if (! Schema::hasTable('gamification_levels')) {
            return;
        }

        $table = 'gamification_levels';
        $hasSoftDeletes = Schema::hasColumn($table, 'deleted_at');
        $now = now();

        foreach (self::CANONICAL_LEVELS as $row) {
            $existing = DB::table($table)
                ->where('name', $row['name'])
                ->when($hasSoftDeletes, fn ($q) => $q->whereNull('deleted_at'))
                ->first();

            if ($existing) {
                DB::table($table)->where('id', $existing->id)->update([
                    'description' => $row['description'],
                    'icon' => $row['icon'],
                    'color' => $row['color'],
                    'points_min' => $row['points_min'],
                    'points_max' => $row['points_max'],
                    'order' => $row['order'],
                    'is_active' => true,
                    'updated_at' => $now,
                    ...($hasSoftDeletes ? ['deleted_at' => null] : []),
                ]);
            } else {
                DB::table($table)->insert([
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'icon' => $row['icon'],
                    'color' => $row['color'],
                    'points_min' => $row['points_min'],
                    'points_max' => $row['points_max'],
                    'order' => $row['order'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                    ...($hasSoftDeletes ? ['deleted_at' => null] : []),
                ]);
            }
        }

        $idsToKeep = DB::table($table)
            ->whereIn('name', self::CANONICAL_NAMES)
            ->when($hasSoftDeletes, fn ($q) => $q->whereNull('deleted_at'))
            ->pluck('id');

        $query = DB::table($table)->whereNotIn('id', $idsToKeep);
        if ($hasSoftDeletes) {
            $query->update(['deleted_at' => $now]);
        } else {
            $query->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não reversível: restauração exigiria backup dos dados antigos
    }
};
