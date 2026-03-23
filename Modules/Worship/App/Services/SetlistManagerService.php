<?php

namespace Modules\Worship\App\Services;

use Carbon\Carbon;
use Modules\Worship\App\Models\WorshipSetlist;

class SetlistManagerService
{
    public function duplicateSetlist(WorshipSetlist $sourceSetlist, string $newDate): WorshipSetlist
    {
        $newSetlist = WorshipSetlist::create([
            'title' => $sourceSetlist->title.' (Cópia)',
            'scheduled_at' => \Carbon\Carbon::parse($newDate),
            'leader_id' => $sourceSetlist->leader_id,
            'description' => $sourceSetlist->description,
            'status' => \Modules\Worship\App\Enums\SetlistStatus::DRAFT->value,
        ]);

        foreach ($sourceSetlist->items as $item) {
            $newSetlist->items()->create([
                'song_id' => $item->song_id,
                'override_key' => $item->override_key,
                'order' => $item->order,
            ]);
        }

        return $newSetlist;
    }

    /**
     * Reorder items in a setlist.
     */
    public function reorderItems(WorshipSetlist $setlist, array $itemIdsWithOrder): void
    {
        foreach ($itemIdsWithOrder as $itemId => $order) {
            $setlist->items()->where('id', $itemId)->update(['order' => $order]);
        }
    }

    /**
     * Publish a setlist and notify musicians.
     */
    public function publish(WorshipSetlist $setlist): void
    {
        $setlist->update(['status' => \Modules\Worship\App\Enums\SetlistStatus::REHEARSAL->value]);

        // Trigger NotifyMusicians command or job
        \Illuminate\Support\Facades\Artisan::call('worship:notify-musicians');
    }
}
