<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo global de ícones (Font Awesome) para níveis, conquistas e jogos.
     */
    public function up(): void
    {
        Schema::create('global_icons', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('fa_name', 80)->index();
            $table->string('style', 20)->default('duotone'); // duotone, solid, regular
            $table->string('category', 40)->nullable(); // game, badge, level, ui
            $table->string('label', 120)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_icons');
    }
};
