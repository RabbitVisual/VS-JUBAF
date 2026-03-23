<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();

            // Who performed the action
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Who is receiving responsibility (if applicable)
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('previous_location_id')->nullable()->constrained('asset_locations');
            $table->foreignId('new_location_id')->constrained('asset_locations');

            $table->string('type'); // transfer, loan, return, maintenance_out, maintenance_return, disposal
            $table->text('notes')->nullable();
            $table->dateTime('date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
