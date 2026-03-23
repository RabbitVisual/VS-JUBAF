<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update polymorphic types in payments table if exists
        if (Schema::hasTable('payments')) {
             DB::table('payments')
                ->where('payable_type', 'Modules\Events\App\Models\Registration')
                ->update(['payable_type' => 'Modules\Events\App\Models\EventRegistration']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments')) {
             DB::table('payments')
                ->where('payable_type', 'Modules\Events\App\Models\EventRegistration')
                ->update(['payable_type' => 'Modules\Events\App\Models\Registration']);
        }
    }
};
