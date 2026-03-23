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
        // 1. O Cabeçalho do Plano
        Schema::create('bible_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('type', ['sequential', 'chronological', 'thematic', 'manual'])->default('manual');
            $table->integer('duration_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. A estrutura do dia (Dia 1, Dia 2...)
        Schema::create('bible_plan_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('bible_plans')->onDelete('cascade');
            $table->integer('day_number');
            $table->string('title')->nullable(); // Ex: "A queda do homem" ou null para usar "Dia X"
            $table->text('devotional_content')->nullable(); // HTML content for devotional text
            $table->string('video_url')->nullable(); // Optional video for the day
            $table->timestamps();

            $table->unique(['plan_id', 'day_number']);
        });

        // 3. O conteúdo de leitura do dia (quais capítulos ler)
        Schema::create('bible_plan_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_day_id')->constrained('bible_plan_days')->onDelete('cascade');
            // We use 'bible_books' table id usually, assuming Book Model exists
            $table->unsignedBigInteger('book_id');
            $table->integer('start_chapter');
            $table->integer('end_chapter')->nullable(); // If null, read only start_chapter
            // Optional specific verses
            $table->integer('start_verse')->nullable();
            $table->integer('end_verse')->nullable();
            $table->string('description_cache')->nullable(); // Ex: "Gênesis 1-3"
            $table->timestamps();
        });

        // 4. Inscrição do usuário no plano
        Schema::create('bible_plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('bible_plans')->onDelete('cascade');
            $table->date('start_date');
            $table->date('projected_end_date')->nullable();
            $table->integer('current_day_number')->default(1);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->time('notification_time')->nullable();
            $table->timestamps();
        });

        // 5. Progresso diário (Check)
        Schema::create('bible_user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('bible_plan_subscriptions')->onDelete('cascade');
            $table->foreignId('plan_day_id')->constrained('bible_plan_days')->onDelete('cascade');
            $table->timestamp('completed_at')->useCurrent();

            $table->unique(['subscription_id', 'plan_day_id']);
        });

        // 6. Anotações Pessoais
        Schema::create('bible_user_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Can be linked to a specific plan day OR a specific verse/chapter context
            $table->foreignId('plan_day_id')->nullable()->constrained('bible_plan_days')->onDelete('set null');
            $table->unsignedBigInteger('book_id')->nullable();
            $table->integer('chapter')->nullable();
            $table->integer('verse')->nullable();

            $table->text('note_content');
            $table->string('color_code')->default('#ffee00'); // Default highlight yellow
            $table->boolean('is_private')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_plans_tables');
    }
};
