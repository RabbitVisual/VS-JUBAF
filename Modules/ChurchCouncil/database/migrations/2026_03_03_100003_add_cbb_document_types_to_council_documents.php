<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add CBB-aligned document types: declaracao_doutrinaria, pacto_igrejas, regimento_interno.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_documents MODIFY document_type ENUM('statute','regiment','minute','resolution','declaracao_doutrinaria','pacto_igrejas','regimento_interno','other') DEFAULT 'other'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_documents MODIFY document_type ENUM('statute','regiment','minute','resolution','other') DEFAULT 'other'");
        }
    }
};
