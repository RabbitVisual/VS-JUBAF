<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bible_versions', function (Blueprint $table) {
            $table->string('audio_url_template', 500)->nullable()->after('imported_at');
        });
    }

    public function down(): void
    {
        Schema::table('bible_versions', function (Blueprint $table) {
            $table->dropColumn('audio_url_template');
        });
    }
};
