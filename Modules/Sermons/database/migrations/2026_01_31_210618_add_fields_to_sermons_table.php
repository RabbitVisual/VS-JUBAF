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
        Schema::table('sermons', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('cover_image');
            $table->foreignId('worship_suggestion_id')->nullable()->constrained('worship_songs')->nullOnDelete()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sermons', function (Blueprint $table) {
            $table->dropColumn('attachments');
            $table->dropForeign(['worship_suggestion_id']);
            $table->dropColumn('worship_suggestion_id');
        });
    }
};
