<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CBAV2026: category_id (FK financial_categories), member_id (dízimo identificado).
     */
    public function up(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_entries', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('category')->constrained('financial_categories')->nullOnDelete();
            }
            if (! Schema::hasColumn('financial_entries', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->after('user_id');
                $table->foreign('member_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (Schema::hasColumn('financial_entries', 'category_id')) {
                $table->dropForeign(['category_id']);
            }
            if (Schema::hasColumn('financial_entries', 'member_id')) {
                $table->dropForeign(['member_id']);
            }
        });
    }
};
