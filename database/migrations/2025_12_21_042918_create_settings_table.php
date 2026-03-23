<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Chave da configuração');
            $table->text('value')->nullable()->comment('Valor da configuração');
            $table->string('type')->default('string')->comment('Tipo: string, text, boolean, integer, file');
            $table->string('group')->default('general')->comment('Grupo: general, appearance, email, etc.');
            $table->text('description')->nullable()->comment('Descrição da configuração');
            $table->timestamps();
        });

        // Inserir configurações padrão
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'Igreja Batista Avenida', 'type' => 'string', 'group' => 'general', 'description' => 'Nome do site', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_description', 'value' => 'Uma comunidade de fé comprometida com o Evangelho', 'type' => 'text', 'group' => 'general', 'description' => 'Descrição do site', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_email', 'value' => 'contato@igrejabatista.com.br', 'type' => 'string', 'group' => 'general', 'description' => 'E-mail de contato', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_phone', 'value' => '(75) 0000-0000', 'type' => 'string', 'group' => 'general', 'description' => 'Telefone de contato', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_address', 'value' => 'Coração de Maria - BA', 'type' => 'text', 'group' => 'general', 'description' => 'Endereço da igreja', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'logo_path', 'value' => 'storage/image/logo_oficial.png', 'type' => 'file', 'group' => 'appearance', 'description' => 'Logo oficial do site', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'logo_icon_path', 'value' => 'storage/image/logo_icon.png', 'type' => 'file', 'group' => 'appearance', 'description' => 'Ícone/Favicon do site', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general', 'description' => 'Modo de manutenção', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
