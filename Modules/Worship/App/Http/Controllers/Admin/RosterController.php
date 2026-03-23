<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyProgress;
use Modules\Worship\App\Models\WorshipInstrument;
use Modules\Worship\App\Models\WorshipRoster;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Services\SetlistManagerService;

class RosterController extends Controller
{
    public function index()
    {
        $setlists = WorshipSetlist::with(['roster.user', 'roster.instrument'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        return view('worship::admin.rosters.index', compact('setlists'));
    }

    public function store(Request $request, WorshipSetlist $setlist)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'instrument_id' => 'required|exists:worship_instruments,id',
            'worship_team_role_id' => 'nullable|exists:worship_team_roles,id',
        ]);

        // Qualification Check (Academy v2)
        $course = AcademyCourse::where('instrument_id', $request->instrument_id)->first();

        if ($course) {
            $lessonIds = $course->lessons()->pluck('worship_academy_lessons.id');
            $totalLessons = $lessonIds->count();
            $completedLessons = $totalLessons > 0
                ? AcademyProgress::where('user_id', $request->user_id)->whereIn('lesson_id', $lessonIds)->count()
                : 0;

            if ($totalLessons > 0 && $completedLessons < $totalLessons) {
                return redirect()->back()->with('error', 'Este músico ainda não concluiu o curso obrigatório para este instrumento.');
            }
        }

        $roster = $setlist->roster()->create([
            'user_id' => $request->user_id,
            'instrument_id' => $request->instrument_id,
            'worship_team_role_id' => $request->worship_team_role_id,
            'status' => \Modules\Worship\App\Enums\RosterStatus::PENDING->value,
        ]);

        if (class_exists(\Modules\Worship\App\Events\RosterCreated::class)) {
            event(new \Modules\Worship\App\Events\RosterCreated($roster));
        }

        return redirect()->back()->with('success', 'Músico escalado com sucesso!');
    }

    public function print(WorshipSetlist $setlist)
    {
        $setlist->load(['roster.user', 'roster.instrument', 'items.song']);

        return view('worship::admin.rosters.print-scale', compact('setlist'));
    }

    public function destroy(WorshipRoster $roster)
    {
        $roster->delete();

        return redirect()->back()->with('success', 'Músico removido da escala!');
    }
}
