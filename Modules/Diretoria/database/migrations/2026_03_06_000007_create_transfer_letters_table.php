<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membership transfer letters (in/out) linked to users.
     */
    public function up(): void
    {
        Schema::create('transfer_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('direction', ['outgoing', 'incoming']);
            $table->string('from_church')->nullable();
            $table->string('to_church')->nullable();
            $table->enum('status', ['draft', 'pending_diretoria', 'pending_assembly', 'sent', 'acknowledged'])->default('draft');
            $table->date('issued_at')->nullable();
            $table->date('received_at')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_letters');
    }
};
