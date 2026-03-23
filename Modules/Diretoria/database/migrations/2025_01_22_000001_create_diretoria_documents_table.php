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
        Schema::create('atas_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type'); // pdf, docx, etc
            $table->integer('file_size')->nullable(); // in bytes
            $table->enum('document_type', ['statute', 'regiment', 'minute', 'resolution', 'declaracao_doutrinaria', 'pacto_igrejas', 'regimento_interno', 'other'])->default('other');
            $table->date('document_date')->nullable(); // Date of the document content
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('meeting_id')->nullable()->constrained('reunioes')->nullOnDelete(); // If related to a meeting (e.g. signed minutes)
            $table->boolean('is_public')->default(false); // If true, visible to all church members
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['document_type', 'is_active']);
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atas_documentos');
    }
};
