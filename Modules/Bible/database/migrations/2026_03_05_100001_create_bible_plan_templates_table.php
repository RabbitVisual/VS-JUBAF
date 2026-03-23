<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Template definitions: iniciante, standard, exegetical, chronological, doctrinal (Baptist).
     */
    public function up(): void
    {
        Schema::create('bible_plan_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('complexity', ['iniciante', 'standard', 'exegetical'])->default('standard');
            $table->enum('order_type', ['canonical', 'chronological', 'doctrinal', 'christ_centered', 'nt_psalms'])->default('canonical');
            $table->json('options')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_plan_templates');
    }
};
