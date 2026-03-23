<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Versioning for meeting minutes (ata).
     */
    public function up(): void
    {
        Schema::create('meeting_minutes_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reuniao_id')->constrained('reunioes')->onDelete('cascade');
            $table->unsignedInteger('version')->default(1);
            $table->longText('content');
            $table->enum('state', ['draft', 'diretoria_approved', 'assembly_approved'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['reuniao_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes_versions');
    }
};
