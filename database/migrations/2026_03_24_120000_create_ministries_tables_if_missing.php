<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabelas mínimas para vínculos user ↔ ministério (JUBAF / compatibilidade sem módulo completo).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ministries')) {
            Schema::create('ministries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ministry_members')) {
            Schema::create('ministry_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
                $table->string('role')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('joined_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ministry_members')) {
            Schema::dropIfExists('ministry_members');
        }
        if (Schema::hasTable('ministries')) {
            Schema::dropIfExists('ministries');
        }
    }
};
