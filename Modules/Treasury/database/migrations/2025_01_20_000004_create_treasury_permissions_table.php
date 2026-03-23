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
        Schema::create('treasury_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('permission_level', ['viewer', 'editor', 'admin']); // Nível de permissão
            $table->boolean('can_view_reports')->default(true);
            $table->boolean('can_create_entries')->default(false);
            $table->boolean('can_edit_entries')->default(false);
            $table->boolean('can_delete_entries')->default(false);
            $table->boolean('can_manage_campaigns')->default(false);
            $table->boolean('can_manage_goals')->default(false);
            $table->boolean('can_export_data')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_permissions');
    }
};
