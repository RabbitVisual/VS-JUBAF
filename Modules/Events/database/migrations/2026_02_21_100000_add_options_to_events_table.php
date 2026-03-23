<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('events', 'options')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            $table->json('options')->nullable()->after('ticket_template_id');
        });

        $this->backfillOptionsFromBadgesAndCertificates();
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }

    private function backfillOptionsFromBadgesAndCertificates(): void
    {
        $events = \Illuminate\Support\Facades\DB::table('events')->get(['id']);
        $badgeEventIds = \Illuminate\Support\Facades\DB::table('event_badges')->distinct()->pluck('event_id')->flip();
        $certEventIds = \Illuminate\Support\Facades\DB::table('event_certificates')->distinct()->pluck('event_id')->flip();

        foreach ($events as $event) {
            $options = [
                'has_badge' => $badgeEventIds->has($event->id),
                'has_certificate' => $certEventIds->has($event->id),
                'has_checkin' => true,
                'has_ticket' => true,
                'show_schedule' => true,
                'show_speakers' => true,
            ];
            \Illuminate\Support\Facades\DB::table('events')
                ->where('id', $event->id)
                ->update(['options' => json_encode($options)]);
        }
    }
};
