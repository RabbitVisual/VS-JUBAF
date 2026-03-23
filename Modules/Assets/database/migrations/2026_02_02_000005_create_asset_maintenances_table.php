<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();

            // Optional integration, we'll store name/ID in a flexible way or just string if no module exists
            $table->string('supplier_name')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();

            $table->text('description');
            $table->decimal('cost', 10, 2)->nullable();

            $table->date('start_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();

            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
