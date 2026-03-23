<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_volunteers', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('role');
            $table->json('skills')->nullable()->after('phone');         // ['food_prep','distribution','transport','administration','pastoral']
            $table->json('availability')->nullable()->after('skills');  // ['monday','tuesday',...,'saturday']
            $table->decimal('total_hours', 8, 2)->default(0)->after('availability');
            $table->text('bio')->nullable()->after('total_hours');
            $table->boolean('is_active')->default(true)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('social_volunteers', function (Blueprint $table) {
            $table->dropColumn(['phone', 'skills', 'availability', 'total_hours', 'bio', 'is_active']);
        });
    }
};
