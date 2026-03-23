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
        Schema::table('igrejas', function (Blueprint $table) {
            if (! Schema::hasColumn('igrejas', 'pastor_titular')) {
                $table->string('pastor_titular')->nullable()->after('nome');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('igrejas', function (Blueprint $table) {
            if (Schema::hasColumn('igrejas', 'pastor_titular')) {
                $table->dropColumn('pastor_titular');
            }
        });
    }
};

