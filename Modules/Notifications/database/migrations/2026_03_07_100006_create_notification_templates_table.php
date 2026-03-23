<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('key', 128)->unique(); // e.g. worship_roster_published
            $table->string('name');
            $table->string('subject')->nullable(); // for email
            $table->text('body'); // Blade/Markdown
            $table->json('channels')->nullable(); // ["in_app","email","webpush"]
            $table->json('variables')->nullable(); // list of variable names for editor hint
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
