<?php

namespace Modules\EBD\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Models\EbdStudentProgress;
use Modules\EBD\App\Services\EbdLessonAvailabilityService;
use Modules\EBD\App\Services\GamificationBridge;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    public function __construct(
        protected EbdLessonAvailabilityService $availabilityService,
        protected GamificationBridge $gamificationBridge
    ) {}

    /**
     * Display the lesson player.
     */
    public function show($lessonId)
    {
        $lesson = EBDLesson::with(['media', 'materials', 'ebdClass', 'course'])->findOrFail($lessonId);
        $user = Auth::user();

        // Ensure the user is an active student of the lesson's class
        $isEnrolled = EBDStudent::where('user_id', $user->id)
            ->where('class_id', $lesson->class_id)
            ->where('is_active', true)
            ->exists();

        if (! $isEnrolled) {
            abort(403, 'Você não está matriculado nesta classe.');
        }

        // Ensure the lesson is already available (date/time <= now)
        $lessonTime = \Carbon\Carbon::parse($lesson->lesson_time);
        $lessonDateTime = $lesson->lesson_date->copy()->setTime($lessonTime->hour, $lessonTime->minute);
        if ($lessonDateTime->gt(now())) {
            return redirect()->route('memberpanel.ebd.student.lessons')
                ->with('error', 'Esta lição ainda não está disponível. Ela estará liberada em '.$lesson->lesson_date->format('d/m/Y').' às '.$lessonTime->format('H:i').'.');
        }

        // Enforce progression: must complete previous lesson before accessing this one
        if (! $this->availabilityService->canAccessLesson($user, $lesson)) {
            $previousLesson = $this->availabilityService->getPreviousLesson($lesson);

            return redirect()->route('memberpanel.ebd.student.lessons')
                ->with('error', 'Conclua a aula anterior para desbloquear esta.');
        }

        // Get progress
        $progress = EbdStudentProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['status' => 'started']
        );

        // Curriculum: course lessons (by order) when course_id set, else class lessons by date
        if ($lesson->course_id) {
            $classLessons = EBDLesson::where('course_id', $lesson->course_id)->orderBy('order')->orderBy('lesson_date')->get();
        } else {
            $classLessons = EBDLesson::where('class_id', $lesson->class_id)
                ->orderBy('lesson_date')
                ->orderBy('lesson_time')
                ->get();
        }

        $lessonStates = $this->availabilityService->forClassLessons($user, $classLessons);
        $lessonStates[$lesson->id]['is_current'] = true;

        $courseProgressPercent = 0;
        if ($lesson->course_id && $classLessons->isNotEmpty()) {
            $completed = EbdStudentProgress::where('user_id', $user->id)
                ->whereIn('lesson_id', $classLessons->pluck('id'))
                ->where('status', 'completed')
                ->count();
            $courseProgressPercent = (int) round(($completed / $classLessons->count()) * 100);
        }

        $minSecondsToComplete = self::MIN_SECONDS_TO_COMPLETE;
        $progressStartedAt = $progress->created_at->toIso8601String();

        return view('ebd::memberpanel.lms.player', compact('lesson', 'progress', 'classLessons', 'lessonStates', 'courseProgressPercent', 'minSecondsToComplete', 'progressStartedAt'));
    }

    /** Tempo mínimo (em segundos) que o aluno deve permanecer na aula antes de poder finalizar. */
    public const MIN_SECONDS_TO_COMPLETE = 90;

    /**
     * Mark a lesson as viewed/completed.
     * Exige tempo mínimo na aula para evitar conclusão sem assistir.
     */
    public function markAsViewed(Request $request, $lessonId)
    {
        $user = Auth::user();
        $lesson = EBDLesson::findOrFail($lessonId);

        $progress = EbdStudentProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['status' => 'started']
        );

        if ($progress->status === 'completed') {
            return response()->json([
                'success' => true,
                'message' => 'Aula já concluída anteriormente.',
            ]);
        }

        $startedAt = $progress->created_at;
        $elapsedSeconds = $startedAt->diffInSeconds(now(), false);
        if ($elapsedSeconds < self::MIN_SECONDS_TO_COMPLETE) {
            $remaining = self::MIN_SECONDS_TO_COMPLETE - $elapsedSeconds;
            $minutes = (int) floor($remaining / 60);
            $secs = $remaining % 60;
            $timeLabel = $minutes > 0
                ? sprintf('%d min e %d s', $minutes, $secs)
                : sprintf('%d segundos', $secs);

            return response()->json([
                'success' => false,
                'message' => 'Assista à aula por pelo menos 1 minuto e 30 segundos antes de finalizar. Faltam ' . $timeLabel . '.',
                'seconds_remaining' => $remaining,
            ]);
        }

        $progress->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $xp = $this->gamificationBridge->awardLessonComplete($user, $lesson);

        return response()->json([
            'success' => true,
            'xp_gained' => $xp,
            'message' => "Aula concluída! +{$xp} XP",
        ]);
    }

    /**
     * Update student notes for a lesson.
     */
    public function updateNotes(Request $request, $lessonId)
    {
        $request->validate(['notes' => 'nullable|string']);
        $user = Auth::user();

        $progress = EbdStudentProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['status' => 'started']
        );

        $progress->update(['notes' => $request->notes]);

        return response()->json(['success' => true]);
    }
}
