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
        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->boolean('is_testimony_public')->default(false)->after('testimony');
            $table->enum('testimony_status', ['draft', 'pending', 'approved', 'rejected'])->default('draft')->after('is_testimony_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->dropColumn(['is_testimony_public', 'testimony_status']);
        });
    }
};
