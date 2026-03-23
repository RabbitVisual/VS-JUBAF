<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_beneficiaries', function (Blueprint $table) {
            // Contact structured
            $table->string('phone')->nullable()->after('contact_info');

            // Address fields (encrypted at model level)
            $table->string('address')->nullable()->after('phone');
            $table->string('neighborhood')->nullable()->after('address');
            $table->string('city')->nullable()->after('neighborhood');
            $table->string('state', 2)->nullable()->after('city');
            $table->string('zip_code', 10)->nullable()->after('state');

            // Geolocation
            $table->decimal('latitude', 10, 7)->nullable()->after('zip_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            // Social profile
            $table->unsignedTinyInteger('family_size')->default(1)->after('longitude');
            $table->decimal('monthly_income', 10, 2)->nullable()->after('family_size');
            $table->json('needs')->nullable()->after('monthly_income'); // ['food','clothes','medicine','hygiene','other']

            // Status
            $table->enum('status', ['active', 'inactive', 'flagged'])->default('active')->after('needs');
        });
    }

    public function down(): void
    {
        Schema::table('social_beneficiaries', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'neighborhood', 'city', 'state', 'zip_code',
                'latitude', 'longitude', 'family_size', 'monthly_income', 'needs', 'status',
            ]);
        });
    }
};
