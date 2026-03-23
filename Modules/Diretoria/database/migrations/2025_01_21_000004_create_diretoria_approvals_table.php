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
        Schema::create('diretoria_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type')->nullable(); // App\Models\User, App\Models\Event, etc.
            $table->unsignedBigInteger('approvable_id')->nullable();
            $table->enum('approval_type', [
                'account_activation',
                'ministry_membership',
                'event_creation',
                'financial_request',
                'document_approval',
                'policy_change',
                'membership_transfer_out',
                'ministry_plan',
                'other',
            ]);
            $table->enum('status', ['pending', 'approved', 'rejected', 'requires_revision'])->default('pending');
            $table->text('request_details');
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('diretoria_members')->nullOnDelete();
            $table->datetime('submitted_at');
            $table->datetime('reviewed_at')->nullable();
            $table->date('expires_at')->nullable(); // Para aprovações temporárias
            $table->json('metadata')->nullable(); // Dados adicionais específicos do tipo
            $table->timestamps();

            $table->index(['approvable_type', 'approvable_id']);
            $table->index(['approval_type', 'status']);
            $table->index('submitted_at');
            $table->index('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diretoria_approvals');
    }
};
