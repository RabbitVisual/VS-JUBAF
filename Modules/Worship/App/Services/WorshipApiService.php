<?php

namespace Modules\Worship\App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyEnrollment;
use Modules\Worship\App\Models\AcademyLesson;
use Modules\Worship\App\Models\AcademyModule;
use Modules\Worship\App\Models\AcademyProgress;
use Modules\Worship\App\Models\WorshipMediaAsset;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Models\WorshipSong;
use Modules\Worship\App\Models\WorshipSetlistItem;
use Modules\Worship\App\Models\WorshipCustomSlide;

/**
 * Serviço central da API v1 de Worship.
 * Única fonte para setlists, músicas (slides ChordPro), busca e Academy.
 */
class WorshipApiService
{
    public function __construct(
        private LyricsService $lyricsService,
        private WorshipCourseService $courseService,
        private WorshipGamificationBridge $gamificationBridge
    ) {}

    /**
     * Lista setlists para projeção/dropdowns.
     *
     * @return \Illuminate\Support\Collection<int, object{id: int, title: string, scheduled_at: string|null, items_count: int}>
     */
    public function getSetlists(int $limit = 10)
    {
        return WorshipSetlist::withCount('items')
            ->orderByDesc('scheduled_at')
            ->take($limit)
            ->get()
            ->map(fn ($s) => (object) [
                'id' => $s->id,
                'title' => $s->title,
                'scheduled_at' => $s->scheduled_at?->toIso8601String(),
                'items_count' => $s->items_count,
            ]);
    }

    /**
     * Setlist com items; para cada item tipo song, slides via ChordPro.
     *
     * @return array{id: int, title: string, items: array}
     */
    public function getSetlistWithItems(int $id): array
    {
        $setlist = WorshipSetlist::with(['items.song', 'items.customSlide'])->findOrFail($id);

        $items = $setlist->items->map(function (WorshipSetlistItem $item) {
            $slides = [];
            $displayTitle = $item->title;

            if ($item->type === 'song' && $item->song) {
                $slides = $this->getSongSlides($item->song);
                $displayTitle = $displayTitle ?: $item->song->title;
            } elseif ($item->type === 'bible' && isset($item->content['slides'])) {
                $slides = $item->content['slides'];
                $displayTitle = $displayTitle ?: ($item->content['reference'] ?? '');
            } elseif ($item->type === 'card') {
                $cardTitle = $item->title ?: 'Aviso';
                $rawText = $item->content['text'] ?? '';
                $lines = array_map('trim', explode("\n", $rawText));
                $lines = array_values(array_filter($lines, fn ($l) => $l !== ''));
                $subtitle = $lines[0] ?? '';
                $description = count($lines) > 1 ? implode("\n", array_slice($lines, 1)) : '';
                $sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
                $titleSize = $item->content['title_size'] ?? 'md';
                $subtitleSize = $item->content['subtitle_size'] ?? 'md';
                $descSize = $item->content['description_size'] ?? 'md';
                if (! in_array($titleSize, $sizes, true)) {
                    $titleSize = 'md';
                }
                if (! in_array($subtitleSize, $sizes, true)) {
                    $subtitleSize = 'md';
                }
                if (! in_array($descSize, $sizes, true)) {
                    $descSize = 'md';
                }
                $html = '<div class="projection-card">';
                $html .= '<h2 class="projection-card-title projection-card-title--' . $titleSize . '">' . e($cardTitle) . '</h2>';
                if ($subtitle !== '') {
                    $html .= '<p class="projection-card-subtitle projection-card-subtitle--' . $subtitleSize . '">' . e($subtitle) . '</p>';
                }
                if ($description !== '') {
                    $html .= '<div class="projection-card-description projection-card-description--' . $descSize . '">' . nl2br(e($description)) . '</div>';
                }
                $html .= '</div>';
                $slides = [['label' => $cardTitle, 'html' => $html]];
                $displayTitle = $displayTitle ?: $cardTitle;
            } elseif (in_array($item->type, ['video', 'audio', 'image'], true)) {
                $url = $item->content['url'] ?? null;
                if (empty($url) && ! empty($item->content['asset_id'])) {
                    $asset = WorshipMediaAsset::find($item->content['asset_id']);
                    $url = $asset ? $asset->file_path : '';
                }
                $slides = [['label' => $displayTitle ?: ucfirst($item->type), 'html' => '', 'media_type' => $item->type, 'url' => $url]];
            } elseif ($item->type === 'countdown') {
                $duration = (int) ($item->content['duration_seconds'] ?? 300);
                $label = $item->content['label'] ?? '';
                $slides = [['label' => $label ?: 'Contagem', 'html' => '', 'countdown_seconds' => $duration]];
            } elseif ($item->type === 'section_header') {
                $title = $item->content['title'] ?? $item->title ?? 'Seção';
                $slides = [['label' => $title, 'html' => '<p class="text-2xl font-black uppercase">' . e($title) . '</p>']];
                $displayTitle = $title;
            } elseif ($item->type === 'event_spotlight' && ! empty($item->content['event_id'])) {
                $eventData = $this->getEventPayload((int) $item->content['event_id']);
                $slides = $eventData ? [['label' => $eventData['title'] ?? 'Evento', 'html' => $eventData['html'] ?? '', 'event' => $eventData]] : [['label' => 'Evento', 'html' => '']];
                $displayTitle = $eventData['title'] ?? $displayTitle ?: 'Evento';
            } elseif ($item->type === 'announcement') {
                $text = $item->content['text'] ?? $item->title ?? 'Aviso';
                $slides = [['label' => 'Aviso', 'html' => '<p class="text-xl font-bold">' . e($text) . '</p>']];
                $displayTitle = $displayTitle ?: 'Aviso';
            } elseif ($item->type === 'custom_slide' && $item->customSlide) {
                $cs = $item->customSlide;
                $html = (str_contains($cs->content ?? '', '<')) ? $cs->content : '<p>' . nl2br(e($cs->content ?? '')) . '</p>';
                $slides = [['label' => $cs->title ?? 'Slide', 'html' => $html]];
                $displayTitle = $displayTitle ?: ($cs->title ?? 'Slide');
            }

            return [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $displayTitle,
                'slides' => $slides,
                'order' => $item->order,
                'content' => $item->content,
            ];
        })->values()->all();

        return [
            'id' => $setlist->id,
            'title' => $setlist->title,
            'items' => $items,
        ];
    }

    public function getSong(int $id): WorshipSong
    {
        return WorshipSong::findOrFail($id);
    }

    /**
     * Converte ChordPro/lyrics em slides para projeção (lógica única, usada por API e Projection).
     *
     * @return array<int, array{label: string, html: string}>
     */
    public function getSongSlides(WorshipSong $song): array
    {
        $raw = $song->content_chordpro ?? '';
        $lyrics = preg_replace('/\{[^\}]+\}/', '', $raw);
        $lyrics = preg_replace('/\[[^\]]+\]/', '', $lyrics);

        if (trim($lyrics) === '' && $song->lyrics_only) {
            $lyrics = $song->lyrics_only;
        }

        $lyrics = str_replace(["\r\n", "\r"], "\n", trim($lyrics ?? ''));
        $parts = preg_split('/\n\s*\n/', $lyrics);
        $stanzas = [];

        foreach ($parts as $part) {
            $partText = trim($part);
            if (empty($partText)) {
                continue;
            }

            $subParts = preg_split('/\n\s*---\s*\n|\n---\n|---/', $partText);

            foreach ($subParts as $subIndex => $subPartText) {
                $lines = explode("\n", trim($subPartText));
                if (empty($lines)) {
                    continue;
                }

                $label = '';
                if (preg_match('/^\[(.*)\]$/', trim($lines[0]), $matches)) {
                    $label = $matches[1];
                    array_shift($lines);
                } elseif (count($lines) > 0 && str_ends_with(trim($lines[0]), ':')) {
                    $label = rtrim(trim($lines[0]), ':');
                    array_shift($lines);
                }

                $lines = array_values(array_filter(array_map('trim', $lines)));
                if (empty($lines)) {
                    continue;
                }

                $chunkSize = 2;
                if (count($lines) > $chunkSize) {
                    $chunks = array_chunk($lines, $chunkSize);
                    foreach ($chunks as $cIdx => $chunk) {
                        $stanzas[] = [
                            'label' => ($label ?: 'Slide') . ' (' . ($cIdx + 1) . '/' . count($chunks) . ')',
                            'html' => implode('<br>', $chunk),
                        ];
                    }
                } else {
                    $displayLabel = $label;
                    if (count($subParts) > 1) {
                        $displayLabel = ($label ?: 'Part') . ' ' . ($subIndex + 1);
                    }
                    $stanzas[] = [
                        'label' => $displayLabel ?: 'Slide',
                        'html' => implode('<br>', $lines),
                    ];
                }
            }
        }

        if (empty($stanzas) && $lyrics !== '') {
            $stanzas[] = [
                'label' => 'Lyrics',
                'html' => nl2br($lyrics),
            ];
        }

        return $stanzas;
    }

    /**
     * Payload para item event_spotlight (consome Events module).
     *
     * @return array{title: string, html: string, start_date: string|null, location: string|null, banner_path: string|null}|null
     */
    public function getEventPayload(int $eventId): ?array
    {
        if (! class_exists(\Modules\Events\App\Models\Event::class)) {
            return null;
        }
        $event = \Modules\Events\App\Models\Event::find($eventId);
        if (! $event) {
            return null;
        }
        $title = $event->title;
        $date = $event->start_date?->format('d/m/Y H:i');
        $location = $event->location ?? '';
        $html = '<div class="event-spotlight"><h2>' . e($title) . '</h2>';
        if ($date) {
            $html .= '<p class="event-date">' . e($date) . '</p>';
        }
        if ($location) {
            $html .= '<p class="event-location">' . e($location) . '</p>';
        }
        $html .= '</div>';
        return [
            'title' => $title,
            'html' => $html,
            'start_date' => $event->start_date?->toIso8601String(),
            'location' => $event->location,
            'banner_path' => $event->banner_path,
            'id' => $event->id,
        ];
    }

    /**
     * Busca músicas por título/artista (local).
     *
     * @return \Illuminate\Support\Collection<int, array{id: int, title: string, artist: string|null, type: string, song_id: int}>
     */
    public function searchSongs(string $query, int $limit = 20)
    {
        $results = $this->lyricsService->search($query);

        return collect($results)->take($limit)->map(fn ($r) => [
            'id' => $r['id'],
            'title' => $r['title'],
            'artist' => $r['artist'] ?? null,
            'type' => $r['type'] ?? 'local',
            'song_id' => $r['song_id'] ?? $r['id'],
        ])->values();
    }

    /**
     * Cursos da Academy (publicados).
     */
    public function getAcademyCourses()
    {
        return AcademyCourse::with(['instrument', 'instructor'])
            ->where('status', 'published')
            ->latest()
            ->get();
    }

    /**
     * Curso com progresso do usuário (para classroom). Delega a WorshipCourseService.
     *
     * @return array{course: AcademyCourse, enrollment: AcademyEnrollment, completed_lessons: array<int>}
     */
    public function getAcademyCourseWithProgress(int $id, User $user): array
    {
        $course = AcademyCourse::with(['modules.lessons.materials', 'instructor'])->findOrFail($id);
        $progress = $this->courseService->getProgressForUser($user, $course);

        return [
            'course' => $course,
            'enrollment' => $progress['enrollment'],
            'completed_lessons' => $progress['completed_lesson_ids'],
        ];
    }

    /**
     * Marca lição como concluída (delega a WorshipCourseService) e concede XP na primeira conclusão.
     *
     * @return array{message: string, progress: int, xp_awarded: int}
     */
    public function markLessonComplete(int $lessonId, User $user): array
    {
        $result = $this->courseService->markLessonComplete($lessonId, $user);

        $xpAwarded = 0;
        if ($result['was_first_completion']) {
            $lesson = AcademyLesson::find($lessonId);
            if ($lesson) {
                $xpAwarded = $this->gamificationBridge->awardLessonComplete($user, $lesson);
            } else {
                $user->increment('xp', WorshipGamificationBridge::XP_PER_LESSON);
                $xpAwarded = WorshipGamificationBridge::XP_PER_LESSON;
            }
        }

        return [
            'message' => 'Lesson completed',
            'progress' => $result['progress_percent'],
            'xp_awarded' => $xpAwarded,
        ];
    }

    /**
     * Curso com estrutura (modules.lessons) para admin builder.
     */
    public function getAcademyCourseStructure(int $id): AcademyCourse
    {
        return AcademyCourse::with(['modules.lessons'])->findOrFail($id);
    }

    /**
     * Cria curso (admin).
     */
    public function createAcademyCourse(array $validated): AcademyCourse
    {
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['instructor_id'] = auth()->id();

        return AcademyCourse::create($validated);
    }

    /**
     * Atualiza estrutura do curso (modules e lessons).
     *
     * @param  array{modules: array}  $data
     */
    public function updateAcademyCourseStructure(int $courseId, array $data): AcademyCourse
    {
        $course = AcademyCourse::findOrFail($courseId);

        DB::transaction(function () use ($course, $data) {
            $existingModuleIds = [];
            $allActiveLessonIds = [];

            foreach ($data['modules'] as $moduleIndex => $moduleData) {
                if (isset($moduleData['id']) && $moduleData['id']) {
                    $module = AcademyModule::where('course_id', $course->id)->find($moduleData['id']);
                    if ($module) {
                        $module->update(['title' => $moduleData['title'], 'order' => $moduleIndex]);
                    } else {
                        $module = $course->modules()->create(['title' => $moduleData['title'], 'order' => $moduleIndex]);
                    }
                } else {
                    $module = $course->modules()->create(['title' => $moduleData['title'], 'order' => $moduleIndex]);
                }
                $existingModuleIds[] = $module->id;

                foreach ($moduleData['lessons'] ?? [] as $lessonIndex => $lessonData) {
                    $lessonPayload = [
                        'module_id' => $module->id,
                        'title' => $lessonData['title'] ?? 'Nova Aula',
                        'order' => $lessonIndex,
                        'type' => $lessonData['type'] ?? 'video',
                        'content' => $lessonData['content'] ?? null,
                        'video_url' => $lessonData['video_url'] ?? null,
                        'duration_minutes' => isset($lessonData['duration_minutes']) ? (int) $lessonData['duration_minutes'] : null,
                        'teacher_tips' => $lessonData['teacher_tips'] ?? null,
                        'pdf_path' => $lessonData['pdf_path'] ?? null,
                        'sheet_music_pdf' => $lessonData['sheet_music_pdf'] ?? null,
                        'bible_reference' => $lessonData['bible_reference'] ?? null,
                    ];

                    if (isset($lessonData['id']) && $lessonData['id']) {
                        $lesson = AcademyLesson::find($lessonData['id']);
                        if ($lesson) {
                            $lesson->update($lessonPayload);
                            $allActiveLessonIds[] = $lesson->id;
                        }
                    } else {
                        $lessonPayload['slug'] = Str::slug($lessonPayload['title']) . '-' . uniqid();
                        $lesson = $module->lessons()->create($lessonPayload);
                        $allActiveLessonIds[] = $lesson->id;
                    }
                }
            }

            $course->modules()->whereNotIn('id', $existingModuleIds)->delete();
            AcademyLesson::whereIn('module_id', $existingModuleIds)->whereNotIn('id', $allActiveLessonIds)->delete();
        });

        return $course->fresh('modules.lessons');
    }
}
