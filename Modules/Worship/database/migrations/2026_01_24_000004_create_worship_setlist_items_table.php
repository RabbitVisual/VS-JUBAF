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
        Schema::create('worship_setlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setlist_id')->constrained('worship_setlists')->onDelete('cascade');
            $table->foreignId('song_id')->constrained('worship_songs')->onDelete('cascade');
            $table->string('override_key')->nullable();
            $table->text('arrangement_note')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_setlist_items');
    }
};
