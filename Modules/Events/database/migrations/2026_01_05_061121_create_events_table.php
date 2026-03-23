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
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('banner_path')->nullable();

                // Datas e Localização
                $table->dateTime('start_date');
                $table->dateTime('end_date')->nullable();
                $table->string('location')->nullable(); // String ou JSON para coordenadas
                $table->json('location_data')->nullable(); // Coordenadas, endereço completo, etc.

                // Capacidade e Status
                $table->integer('capacity')->nullable(); // null = ilimitado
                $table->enum('status', ['draft', 'published', 'closed', 'waiting_approval'])->default('draft');
                $table->enum('visibility', ['public', 'members', 'both'])->default('public');
                $table->boolean('is_featured')->default(false);

                // Formulário customizado
                $table->json('form_fields')->nullable(); // Campos extras do formulário
                $table->json('schedule')->nullable();
                $table->json('options')->nullable();
                $table->json('theme_config')->nullable();
                $table->unsignedBigInteger('event_type_id')->nullable();
                $table->unsignedBigInteger('treasury_campaign_id')->nullable();
                $table->unsignedBigInteger('ministry_plan_id')->nullable();
                $table->boolean('requires_council_approval')->default(false);
                $table->unsignedBigInteger('ticket_template_id')->nullable();
                $table->string('logo_path')->nullable();

                // Extended event fields
                $table->json('target_audience')->nullable();
                $table->tinyInteger('min_age_restriction')->unsigned()->nullable();
                $table->tinyInteger('max_age_restriction')->unsigned()->nullable();
                $table->string('dress_code', 100)->nullable();
                $table->dateTime('registration_deadline')->nullable();
                $table->tinyInteger('max_per_registration')->unsigned()->default(10);
                $table->string('contact_name', 150)->nullable();
                $table->string('contact_email', 150)->nullable();
                $table->string('contact_phone', 30)->nullable();
                $table->string('contact_whatsapp', 30)->nullable();
                $table->enum('recurrence_type', ['weekly', 'monthly', 'yearly'])->nullable();
                $table->json('default_required_fields')->nullable();

                // Metadados
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();

                // Índices
                $table->index('slug');
                $table->index('status');
                $table->index('visibility');
                $table->index('start_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
