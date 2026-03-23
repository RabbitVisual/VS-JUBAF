<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_responsibility_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('type'); // loan, permanent_assignment
            $table->dateTime('generated_at');
            $table->dateTime('signed_at')->nullable();

            $table->string('file_path')->nullable(); // Path to stored PDF

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_responsibility_terms');
    }
};
