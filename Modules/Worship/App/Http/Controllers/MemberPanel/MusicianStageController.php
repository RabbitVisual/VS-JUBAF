<?php

namespace Modules\Worship\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Modules\Worship\App\Models\WorshipSetlist;

class MusicianStageController extends Controller
{
    public function view(WorshipSetlist $setlist)
    {
        $setlist->load(['items.song', 'roster.user', 'roster.instrument']);

        return view('worship::memberpanel.stage.viewer', compact('setlist'));
    }
}
