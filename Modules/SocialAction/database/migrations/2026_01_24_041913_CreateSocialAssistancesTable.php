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
        Schema::create('social_assistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_beneficiary_id')->constrained('social_beneficiaries')->onDelete('cascade');
            $table->foreignId('social_pantry_item_id')->constrained('social_pantry_items')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->dateTime('registered_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_assistances');
    }
};
