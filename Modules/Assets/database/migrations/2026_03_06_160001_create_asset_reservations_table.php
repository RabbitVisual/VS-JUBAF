<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Reservations of assets by ministries (and optionally linked to an event).
     */
    public function up(): void
    {
        Schema::create('asset_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->enum('status', ['requested', 'approved', 'denied', 'completed'])->default('requested');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['ministry_id', 'status']);
            $table->index(['start_at', 'end_at']);
        });

        if (Schema::hasTable('events')) {
            Schema::table('asset_reservations', function (Blueprint $table) {
                $table->foreign('event_id')->references('id')->on('events')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_reservations');
    }
};
