<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Colunas esperadas pelo painel Admin/MemberPanel e por User::PROFILE_FIELDS (JUBAF).
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 1)->nullable();
            }
            if (! Schema::hasColumn('users', 'marital_status')) {
                $table->string('marital_status', 30)->nullable();
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }
            if (! Schema::hasColumn('users', 'address_number')) {
                $table->string('address_number', 20)->nullable();
            }
            if (! Schema::hasColumn('users', 'address_complement')) {
                $table->string('address_complement', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'neighborhood')) {
                $table->string('neighborhood', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'city')) {
                $table->string('city', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'state')) {
                $table->string('state', 2)->nullable();
            }
            if (! Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code', 10)->nullable();
            }
            if (! Schema::hasColumn('users', 'membership_date')) {
                $table->date('membership_date')->nullable();
            }
            if (! Schema::hasColumn('users', 'time_congregating_months')) {
                $table->unsignedInteger('time_congregating_months')->nullable();
            }
            if (! Schema::hasColumn('users', 'baptism_date')) {
                $table->date('baptism_date')->nullable();
            }
            if (! Schema::hasColumn('users', 'baptism_place')) {
                $table->string('baptism_place')->nullable();
            }
            if (! Schema::hasColumn('users', 'is_baptized')) {
                $table->boolean('is_baptized')->default(false);
            }
            if (! Schema::hasColumn('users', 'profession')) {
                $table->string('profession', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'education_level')) {
                $table->string('education_level', 50)->nullable();
            }
            if (! Schema::hasColumn('users', 'workplace')) {
                $table->string('workplace')->nullable();
            }
            if (! Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable();
            }
            if (! Schema::hasColumn('users', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship', 50)->nullable();
            }
            if (! Schema::hasColumn('users', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'first_name', 'last_name', 'gender', 'marital_status',
                'address', 'address_number', 'address_complement', 'neighborhood',
                'city', 'state', 'zip_code', 'membership_date', 'time_congregating_months',
                'baptism_date', 'baptism_place', 'is_baptized', 'profession',
                'education_level', 'workplace', 'emergency_contact_name',
                'emergency_contact_phone', 'emergency_contact_relationship', 'notes', 'photo',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
