<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('event_types')) {
            Schema::create('event_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('events', 'event_type_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->unsignedBigInteger('event_type_id')->nullable()->after('form_fields');
                $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'event_type_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign(['event_type_id']);
            });
        }
        Schema::dropIfExists('event_types');
    }
};
