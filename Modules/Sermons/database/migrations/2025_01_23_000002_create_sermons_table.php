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
        Schema::create('sermons', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Título do sermão
            $table->string('slug')->unique(); // Slug para URLs
            $table->text('subtitle')->nullable(); // Subtítulo
            $table->text('description')->nullable(); // Descrição/Resumo
            $table->longText('introduction')->nullable(); // Introdução
            $table->longText('development')->nullable(); // Desenvolvimento
            $table->longText('conclusion')->nullable(); // Conclusão
            $table->longText('application')->nullable(); // Aplicação prática
            $table->string('sermon_structure_type', 50)->nullable(); // expositivo, temático, textual
            $table->json('structure_meta')->nullable();
            $table->longText('full_content')->nullable(); // Conteúdo completo (quando disponível)
            $table->string('cover_image')->nullable();

            // Categoria
            $table->foreignId('category_id')->nullable()->constrained('sermon_categories')->nullOnDelete();
            // bible_series é criado em migration posterior (2026_*), então evitamos FK antecipada.
            $table->unsignedBigInteger('series_id')->nullable();

            // Autor/Criador
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Status e visibilidade
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'members', 'private'])->default('members');
            $table->boolean('is_collaborative')->default(false); // Permite colaboração
            $table->boolean('is_featured')->default(false); // Sermão em destaque

            // Metadados
            $table->integer('views')->default(0); // Visualizações
            $table->integer('likes')->default(0); // Curtidas
            $table->integer('downloads')->default(0); // Downloads
            $table->timestamp('published_at')->nullable(); // Data de publicação
            $table->timestamp('sermon_date')->nullable(); // Data em que foi pregado

            // Versão e colaboração
            $table->integer('version')->default(1); // Versão do sermão (para histórico)
            $table->foreignId('parent_id')->nullable()->constrained('sermons')->nullOnDelete(); // Sermão original (se for uma cópia/fork)

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'visibility', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index(['user_id', 'status']);

            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'subtitle', 'description', 'full_content']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermons');
    }
};
