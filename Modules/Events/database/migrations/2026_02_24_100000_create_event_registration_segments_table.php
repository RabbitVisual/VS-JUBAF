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
        Schema::create('event_registration_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('label');
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->json('form_fields')->nullable();
            $table->json('documents_requested')->nullable();
            $table->boolean('ask_phone')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registration_segments');
    }
};
