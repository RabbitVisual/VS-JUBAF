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
        Schema::create('social_kits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('social_kit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_id')->constrained('social_kits')->cascadeOnDelete();
            $table->foreignId('pantry_item_id')->constrained('social_pantry_items')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->timestamps();
        });

        Schema::create('social_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pantry_item_id')->constrained('social_pantry_items')->cascadeOnDelete();
            $table->string('type'); // in, out, adjustment
            $table->decimal('quantity', 10, 2);
            $table->string('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_stock_movements');
        Schema::dropIfExists('social_kit_items');
        Schema::dropIfExists('social_kits');
    }
};
