<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique(); // Human readable inventory code (e.g. PAT-001)
            $table->string('name');
            $table->text('description')->nullable();

            $table->foreignId('category_id')->constrained('asset_categories');
            $table->foreignId('location_id')->constrained('asset_locations');

            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_value', 10, 2)->nullable();
            $table->string('invoice_number')->nullable();

            $table->string('status')->default('available'); // available, borrowed, maintenance, lost, disposed
            $table->string('condition')->nullable(); // new, good, fair, poor, unusable

            $table->string('photo_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
