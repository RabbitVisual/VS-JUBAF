<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministry_service_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->unsignedInteger('points')->default(0);
            $table->foreignId('ministry_report_id')->nullable()->constrained('ministry_reports')->nullOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->timestamps();

            $table->unique(['user_id', 'ministry_id', 'period_year', 'period_month'], 'ministry_service_points_user_ministry_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministry_service_points');
    }
};
