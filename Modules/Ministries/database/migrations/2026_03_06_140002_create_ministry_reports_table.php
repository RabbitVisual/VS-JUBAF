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
        Schema::create('ministry_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('ministry_plans')->nullOnDelete();
            $table->unsignedSmallInteger('report_year');
            $table->unsignedTinyInteger('report_month');
            $table->date('period_start');
            $table->date('period_end');
            $table->json('quantitative_data')->nullable();
            $table->text('qualitative_summary')->nullable();
            $table->text('prayer_requests')->nullable();
            $table->text('highlights')->nullable();
            $table->text('challenges')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_council_review', 'archived'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('treasury_summary')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->unique(['ministry_id', 'report_year', 'report_month']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministry_reports');
    }
};
