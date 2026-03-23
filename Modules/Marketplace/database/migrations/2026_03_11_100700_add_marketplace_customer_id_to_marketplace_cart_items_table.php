<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('marketplace_cart_items', 'marketplace_customer_id')) {
                $table->foreignId('marketplace_customer_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('marketplace_customers')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('marketplace_cart_items', 'marketplace_customer_id')) {
                $table->dropConstrainedForeignId('marketplace_customer_id');
            }
        });
    }
};

