<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medals', function (Blueprint $table) {
            $table->text('explanation')->nullable()->after('description');
            $table->text('tips')->nullable()->after('explanation');
            $table->text('incentive_message')->nullable()->after('tips');
            $table->boolean('is_pro_only')->default(false)->after('rarity');
        });
    }

    public function down(): void
    {
        Schema::table('medals', function (Blueprint $table) {
            $table->dropColumn(['explanation', 'tips', 'incentive_message', 'is_pro_only']);
        });
    }
};
