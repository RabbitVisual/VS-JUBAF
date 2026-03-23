<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, adicionar coluna slug sem unique se não existir
        if (! Schema::hasColumn('events', 'slug')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });

            // Preencher slugs para registros existentes
            DB::table('events')->whereNull('slug')->orWhere('slug', '')->chunkById(100, function ($events) {
                foreach ($events as $event) {
                    $slug = \Illuminate\Support\Str::slug($event->title);
                    $uniqueSlug = $slug;
                    $counter = 1;

                    // Garantir que o slug seja único
                    while (DB::table('events')->where('slug', $uniqueSlug)->where('id', '!=', $event->id)->exists()) {
                        $uniqueSlug = $slug.'-'.$counter;
                        $counter++;
                    }

                    DB::table('events')->where('id', $event->id)->update(['slug' => $uniqueSlug]);
                }
            });

            // Agora adicionar constraint unique
            Schema::table('events', function (Blueprint $table) {
                $table->string('slug')->unique()->change();
            });
        }

        Schema::table('events', function (Blueprint $table) {

            // Renomear image para banner_path se necessário
            if (Schema::hasColumn('events', 'image') && ! Schema::hasColumn('events', 'banner_path')) {
                $table->renameColumn('image', 'banner_path');
            } elseif (! Schema::hasColumn('events', 'banner_path')) {
                $table->string('banner_path')->nullable()->after('description');
            }

            // Alterar start_date e end_date para datetime se forem date
            // Isso será feito via raw SQL se necessário

            // Adicionar location_data (JSON)
            if (! Schema::hasColumn('events', 'location_data')) {
                $table->json('location_data')->nullable()->after('location');
            }

            // Adicionar capacity
            if (! Schema::hasColumn('events', 'capacity')) {
                $table->integer('capacity')->nullable()->after('location_data');
            }

            // Adicionar status enum se não existir
            if (! Schema::hasColumn('events', 'status')) {
                $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->after('capacity');
            }

            // Adicionar visibility enum
            if (! Schema::hasColumn('events', 'visibility')) {
                $table->enum('visibility', ['public', 'members', 'both'])->default('public')->after('status');
            }

            // Adicionar form_fields (JSON)
            if (! Schema::hasColumn('events', 'form_fields')) {
                $table->json('form_fields')->nullable()->after('visibility');
            }

            // Remover colunas antigas que não são mais necessárias
            if (Schema::hasColumn('events', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('events', 'end_time')) {
                $table->dropColumn('end_time');
            }
            if (Schema::hasColumn('events', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('events', 'order')) {
                $table->dropColumn('order');
            }
        });

        // Alterar start_date e end_date para datetime usando Laravel Schema change()
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('start_date')->nullable(false)->change();
            if (Schema::hasColumn('events', 'end_date')) {
                $table->dateTime('end_date')->nullable()->change();
            }
        });

        // Adicionar índices se não existirem
        Schema::table('events', function (Blueprint $table) {
            if (! $this->hasIndex('events', 'events_slug_index')) {
                $table->index('slug', 'events_slug_index');
            }
            if (! $this->hasIndex('events', 'events_status_index')) {
                $table->index('status', 'events_status_index');
            }
            if (! $this->hasIndex('events', 'events_visibility_index')) {
                $table->index('visibility', 'events_visibility_index');
            }
            if (! $this->hasIndex('events', 'events_start_date_index')) {
                $table->index('start_date', 'events_start_date_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Reverter alterações
            if (Schema::hasColumn('events', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('events', 'banner_path')) {
                $table->dropColumn('banner_path');
            }
            if (Schema::hasColumn('events', 'location_data')) {
                $table->dropColumn('location_data');
            }
            if (Schema::hasColumn('events', 'capacity')) {
                $table->dropColumn('capacity');
            }
            if (Schema::hasColumn('events', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('events', 'visibility')) {
                $table->dropColumn('visibility');
            }
            if (Schema::hasColumn('events', 'form_fields')) {
                $table->dropColumn('form_fields');
            }

            // Restaurar colunas antigas
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
        });
    }

    /**
     * Verifica se um índice existe
     */
    protected function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $databaseName = $connection->getDatabaseName();
            $result = $connection->select(
                'SELECT COUNT(*) as count FROM information_schema.statistics
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$databaseName, $table, $indexName]
            );

            return $result[0]->count > 0;
        }

        if ($driver === 'sqlite') {
            $result = $connection->select("PRAGMA index_list(`$table`)");
            foreach ($result as $index) {
                if ($index->name === $indexName) {
                    return true;
                }
            }

            return false;
        }

        // Fallback or other drivers
        return false;
    }
};
