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
        Schema::create('event_price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            // event_registration_segments é criado em migration posterior (2026_02_*).
            $table->unsignedBigInteger('registration_segment_id')->nullable();
            $table->string('label'); // Ex: "Crianças", "Adultos", "Idosos"
            $table->string('rule_type')->nullable();
            $table->string('member_status')->nullable();
            $table->string('participant_type')->nullable();
            $table->string('church_membership')->nullable();
            $table->string('gender')->nullable();
            $table->integer('min_age')->nullable(); // null = sem limite mínimo
            $table->integer('max_age')->nullable(); // null = sem limite máximo
            $table->decimal('price', 10, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->dateTime('date_from')->nullable();
            $table->dateTime('date_to')->nullable();
            $table->integer('min_participants')->nullable();
            $table->integer('max_participants')->nullable();
            $table->string('location')->nullable();
            $table->decimal('discount_percentage', 10, 2)->nullable();
            $table->decimal('discount_fixed', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->json('conditions')->nullable();
            $table->integer('order')->default(0); // Ordem de exibição
            $table->timestamps();

            // Índices
            $table->index('event_id');
            $table->index('registration_segment_id');
            $table->index(['event_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_price_rules');
    }
};
