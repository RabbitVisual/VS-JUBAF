<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaching_rules', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_key')->unique();
            $table->string('condition_type');
            $table->json('condition_params');
            $table->foreignId('insight_id')->nullable()->constrained('insights_bank')->nullOnDelete();
            $table->string('level')->default('info');
            $table->foreignId('medal_id')->nullable()->constrained('medals')->nullOnDelete();
            $table->unsignedSmallInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('message_override')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_rules');
    }
};
