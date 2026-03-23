<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Fixed up function
    public function up(): void
    {
        Schema::table('worship_instruments', function (Blueprint $table) {
             $table->foreignId('category_id')->nullable()->after('icon')->constrained('worship_instrument_categories')->nullOnDelete();
        });

        // Seed Default Categories
        $categories = [
            ['name' => 'Harmonia', 'slug' => 'harmonia', 'color' => 'purple', 'icon' => 'music-note'],
            ['name' => 'Melodia', 'slug' => 'melodia', 'color' => 'blue', 'icon' => 'microphone'],
            ['name' => 'Percussão', 'slug' => 'percussao', 'color' => 'orange', 'icon' => 'sparkles'],
            ['name' => 'Vocal', 'slug' => 'vocal', 'color' => 'pink', 'icon' => 'user-group'],
            ['name' => 'Técnico', 'slug' => 'tecnico', 'color' => 'gray', 'icon' => 'cog'],
        ];

        foreach ($categories as $cat) {
            \DB::table('worship_instrument_categories')->insert(array_merge($cat, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Migrate existing data (assuming 'category' column exists and has values like 'harmonia')
        // We will try to match slug to the category column
        $instruments = \DB::table('worship_instruments')->get();
        foreach ($instruments as $instrument) {
            if (isset($instrument->category)) {
                $categorySlug = \Illuminate\Support\Str::slug($instrument->category);
                // Handle enum values which might be just 'harmonia'
                $catId = \DB::table('worship_instrument_categories')->where('slug', $categorySlug)->value('id');

                if ($catId) {
                    \DB::table('worship_instruments')->where('id', $instrument->id)->update(['category_id' => $catId]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('worship_instruments', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

};
