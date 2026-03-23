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
        Schema::create('sermon_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Comentário
            $table->text('comment'); // Texto do comentário
            $table->enum('type', ['comment', 'suggestion', 'question', 'feedback'])->default('comment');

            // Respostas (threading)
            $table->foreignId('parent_id')->nullable()->constrained('sermon_comments')->nullOnDelete();

            // Referência específica no sermão
            $table->string('reference_section')->nullable(); // Seção referenciada (ex: "introduction", "development")
            $table->text('reference_text')->nullable(); // Texto específico referenciado

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'resolved'])->default('pending');
            $table->integer('likes')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['sermon_id', 'status']);
            $table->index(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermon_comments');
    }
};
