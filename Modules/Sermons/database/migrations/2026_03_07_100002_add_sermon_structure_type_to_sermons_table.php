<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sermons', function (Blueprint $table) {
            $table->string('sermon_structure_type', 50)->nullable()->after('application'); // expositivo, temático, textual
            $table->json('structure_meta')->nullable()->after('sermon_structure_type');
        });
    }

    public function down(): void
    {
        Schema::table('sermons', function (Blueprint $table) {
            $table->dropColumn(['sermon_structure_type', 'structure_meta']);
        });
    }
};
