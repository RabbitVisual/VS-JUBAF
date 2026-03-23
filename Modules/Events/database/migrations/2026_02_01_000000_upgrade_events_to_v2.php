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
        // 1. Update events table
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('events', 'ticket_template_id')) {
                $table->unsignedBigInteger('ticket_template_id')->nullable()->after('location_data');
            }
        });

        // 2. Create event_batches table
        if (!Schema::hasTable('event_batches')) {
            Schema::create('event_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
                $table->string('name'); // e.g., "Lote 1"
                $table->decimal('price', 10, 2)->default(0);
                $table->integer('quantity_available')->default(0);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->unsignedBigInteger('auto_switch_to_batch_id')->nullable(); // Self-reference, constrained later or manually
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Rename registrations to event_registrations if needed
        if (Schema::hasTable('registrations') && !Schema::hasTable('event_registrations')) {
            Schema::rename('registrations', 'event_registrations');
        }

        // 4. Update event_registrations table
        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('event_registrations', 'uuid')) {
                    $table->uuid('uuid')->unique()->nullable()->after('id');
                }
                if (!Schema::hasColumn('event_registrations', 'batch_id')) {
                    $table->foreignId('batch_id')->nullable()->after('event_id')->constrained('event_batches')->onDelete('set null');
                }
                if (!Schema::hasColumn('event_registrations', 'ticket_hash')) {
                    $table->string('ticket_hash')->nullable()->index()->after('payment_reference');
                }
                if (!Schema::hasColumn('event_registrations', 'checked_in_at')) {
                    $table->timestamp('checked_in_at')->nullable()->after('status');
                }
            });
        }

        // 5. Create event_certificates table
        if (!Schema::hasTable('event_certificates')) {
            Schema::create('event_certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
                $table->longText('template_html')->nullable();
                $table->dateTime('release_after')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 5. Drop event_certificates
        Schema::dropIfExists('event_certificates');

        // 4. Revert event_registrations columns
        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropColumn(['uuid', 'batch_id', 'ticket_hash', 'checked_in_at']);
            });
        }

        // 3. Rename back
        if (Schema::hasTable('event_registrations') && !Schema::hasTable('registrations')) {
            Schema::rename('event_registrations', 'registrations');
        }

        // 2. Drop event_batches
        Schema::dropIfExists('event_batches');

        // 1. Revert events columns
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn(['is_featured', 'ticket_template_id']);
            });
        }
    }
};
