<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sermon_bible_references', function (Blueprint $table) {
            $table->text('exegesis_notes')->nullable()->after('context');
            $table->foreignId('study_note_id')->nullable()->after('exegesis_notes')->constrained('sermon_study_notes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sermon_bible_references', function (Blueprint $table) {
            $table->dropForeign(['study_note_id']);
            $table->dropColumn(['exegesis_notes', 'study_note_id']);
        });
    }
};
