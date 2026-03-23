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
        Schema::table('social_assistances', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('quantity');
            $table->string('type')->nullable()->after('status');
            $table->text('description')->nullable()->after('type');
            $table->timestamp('delivered_at')->nullable()->after('registered_at');
            $table->foreignId('volunteer_id')->nullable()->constrained('users')->nullOnDelete()->after('kit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_assistances', function (Blueprint $table) {
            $table->dropForeign(['volunteer_id']);
            $table->dropColumn(['status', 'type', 'description', 'delivered_at', 'volunteer_id']);
        });
    }
};
