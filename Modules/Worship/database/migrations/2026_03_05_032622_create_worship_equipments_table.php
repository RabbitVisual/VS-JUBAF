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
        Schema::create('worship_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Microfone Shure SM58
            $table->foreignId('worship_team_role_id')->nullable()->constrained('worship_team_roles')->nullOnDelete();
            $table->string('status')->default('active'); // active, maintenance, broken
            $table->string('serial_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_equipments');
    }
};
