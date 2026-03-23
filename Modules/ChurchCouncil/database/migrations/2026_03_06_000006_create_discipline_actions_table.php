<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Timeline of discipline actions taken in each case.
     */
    public function up(): void
    {
        Schema::create('discipline_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discipline_case_id')->constrained('discipline_cases')->onDelete('cascade');
            $table->string('stage');
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('performed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipline_actions');
    }
};

