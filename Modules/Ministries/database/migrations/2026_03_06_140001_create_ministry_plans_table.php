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
        Schema::create('ministry_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('period_year');
            $table->enum('period_type', ['annual', 'semiannual', 'quarterly', 'monthly'])->default('annual');
            $table->date('period_start');
            $table->date('period_end');
            $table->text('objectives')->nullable();
            $table->json('goals')->nullable();
            $table->json('activities')->nullable();
            $table->decimal('budget_requested', 14, 2)->nullable();
            $table->text('budget_notes')->nullable();
            $table->enum('status', [
                'draft',
                'under_council_review',
                'approved',
                'in_execution',
                'archived',
            ])->default('draft');
            $table->unsignedBigInteger('council_approval_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ministry_id', 'period_year', 'period_type']);
            $table->index('status');
        });

        Schema::table('ministry_plans', function (Blueprint $table) {
            $table->foreign('council_approval_id')
                ->references('id')
                ->on('council_approvals')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministry_plans');
    }
};
