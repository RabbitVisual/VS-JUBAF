<?php

namespace Modules\Projection\App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Models\WorshipSong;
use Modules\Worship\App\Models\WorshipMediaAsset;
use Modules\Worship\App\Models\WorshipCustomSlide;
use Modules\Worship\App\Services\LyricsService;
use Modules\Worship\App\Models\WorshipSetlistItem;
use Modules\Projection\App\Models\ProjectionTheme;
use Modules\Projection\App\Models\ProjectionCardTemplate;
use Illuminate\Support\Facades\DB;

/**
 * Serviço central da API de projeção (v1).
 * Toda a lógica de state, assets, slides, timeline e lyrics passa por aqui.
 * Respostas preparadas para formato { data }.
 *
 * State inclui campos para remote (schedule live/preview): current_setlist_id,
 * current_item_id, current_slide_index, current_item_title.
 */
class ProjectionApiService
{
    public const CACHE_KEY_STATE = 'projection_state';

    /** Default state structure (for getState and remote/schedule) */
    public static function defaultState(): array
    {
        return [
            'type' => 'clear',
            'content' => '',
            'footer' => '',
            'theme' => 'black',
            'theme_id' => null,
            'isBlackout' => false,
            'isClear' => false,
            'bgUrl' => '',
            'bgType' => 'image',
            'bgOpacity' => 1.0,
            'bgBlur' => 0,
            'alertMessage' => '',
            'transition' => 'fade',
            'logoOption' => 'verse',
            'logoMessage' => '',
            'worshipFx' => false,
            'url' => '',
            'current_setlist_id' => null,
            'current_item_id' => null,
            'current_slide_index' => 0,
            'current_item_title' => null,
            'animation_style' => 'none', // none, line, stanza
            'stage_content' => '',
            'stage_alert' => '',
            'countdown_seconds' => 0,
            'countdown_ends_at' => null, // Unix timestamp (seconds) — todas as telas usam para ficar sincronizadas
            'card_title' => '',
            'card_subtitle' => '',
            'card_description' => '',
            'card_title_size' => 'md',
            'card_subtitle_size' => 'md',
            'card_description_size' => 'md',
        ];
    }

    /** Keys allowed in updateState payload (state shape + countdown). */
    private static function allowedStateKeys(): array
    {
        return array_keys(self::defaultState());
    }

    public function getState(): array
    {
        $state = Cache::get(self::CACHE_KEY_STATE, []);
        $state = array_merge(self::defaultState(), $state);
        // Garantir que alertas nunca fiquem como null/undefined (evita alerta permanente ao atualizar)
        $state['alertMessage'] = is_string($state['alertMessage'] ?? '') ? trim($state['alertMessage']) : '';
        // Unificar stage_alert (canvas usa snake_case); aceitar stageAlert vindo do cache/payload antigo
        $state['stage_alert'] = is_string($state['stage_alert'] ?? $state['stageAlert'] ?? '') ? trim($state['stage_alert'] ?? $state['stageAlert'] ?? '') : '';
        unset($state['stageAlert']);
        // Garantir que campos string nunca retornem null (resposta consistente para a tela)
        $state['content'] = is_string($state['content'] ?? '') ? $state['content'] : '';
        $state['footer'] = is_string($state['footer'] ?? '') ? trim($state['footer']) : '';
        $state['card_title'] = is_string($state['card_title'] ?? '') ? trim($state['card_title']) : '';
        $state['card_subtitle'] = is_string($state['card_subtitle'] ?? '') ? trim($state['card_subtitle']) : '';
        $state['card_description'] = is_string($state['card_description'] ?? '') ? trim($state['card_description']) : '';
        $state['stage_content'] = is_string($state['stage_content'] ?? '') ? trim($state['stage_content']) : '';
        if (! empty($state['url']) && str_starts_with((string) $state['url'], '/storage/projection_assets/')) {
            $state['url'] = url('/api/v1/projection/assets/serve/' . basename($state['url']));
        }
        // Resolve theme for screen WITHOUT querying DB on every poll (Zero-Delay Optimization)
        if (! empty($state['theme_id']) || ! empty($state['theme'])) {
            $themeCacheKey = 'projection_theme_config_' . ($state['theme_id'] ?? $state['theme']);
            $themeConfig = Cache::remember($themeCacheKey, now()->addHours(24), function () use ($state) {
                $themeModel = null;
                if (! empty($state['theme_id']) && is_numeric($state['theme_id'])) {
                    $themeModel = ProjectionTheme::find((int) $state['theme_id']);
                }
                if (! $themeModel && ! empty($state['theme']) && is_string($state['theme'])) {
                    $themeModel = ProjectionTheme::where('slug', $state['theme'])->first();
                }

                if ($themeModel) {
                    return [
                        'background_type' => $themeModel->background_type ?? 'solid',
                        'background_value' => $themeModel->background_value ?? '#000000',
                        'font_family' => $themeModel->font_family ?? null,
                        'font_size_base' => $themeModel->font_size_base ?? null,
                        'text_color' => $themeModel->text_color ?? null,
                        'text_shadow' => $themeModel->text_shadow ?? null,
                        'alignment' => $themeModel->alignment ?? null,
                        'padding' => $themeModel->padding ?? null,
                    ];
                }
                return null;
            });
            $state['theme_config'] = $themeConfig;
        } else {
            $state['theme_config'] = null;
        }
        // Countdown: garantir countdown_ends_at em toda resposta para todas as telas ficarem sincronizadas
        if (isset($state['type']) && $state['type'] === 'countdown') {
            $secs = (int) ($state['countdown_seconds'] ?? 0);
            $endsAt = isset($state['countdown_ends_at']) && is_numeric($state['countdown_ends_at']) ? (int) $state['countdown_ends_at'] : null;
            if ($secs > 0 && $endsAt === null) {
                $state['countdown_ends_at'] = time() + $secs;
                Cache::put(self::CACHE_KEY_STATE, $state, now()->addDay());
            }
            $endsAt = $endsAt ?? (int) ($state['countdown_ends_at'] ?? 0);
            // Countdown expirado: trocar para logo com mensagem de boas-vindas
            if ($endsAt > 0 && time() >= $endsAt) {
                $state['type'] = 'logo';
                $state['logoOption'] = $state['logoOption'] ?? 'message';
                $state['logoMessage'] = ! empty($state['logoMessage']) ? trim($state['logoMessage']) : 'Sejam Bem Vindos!';
                $state['isClear'] = false;
                $state['isBlackout'] = false;
                $state['countdown_seconds'] = 0;
                $state['countdown_ends_at'] = null;
                Cache::put(self::CACHE_KEY_STATE, $state, now()->addDay());
            }
        }
        return $state;
    }

    public function updateState(array $payload): array
    {
        $allowed = array_fill_keys(self::allowedStateKeys(), true);
        $payload = array_intersect_key($payload, $allowed);
        if (! empty($payload['url']) && str_starts_with((string) $payload['url'], '/storage/projection_assets/')) {
            $payload['url'] = url('/api/v1/projection/assets/serve/' . basename($payload['url']));
        }
        $current = $this->getState();
        $newState = array_merge($current, $payload);
        // Normalizar alertas: null ou vazio limpa no servidor para não reaparecer ao atualizar a página
        if (array_key_exists('alertMessage', $payload)) {
            $v = $payload['alertMessage'];
            $newState['alertMessage'] = (is_string($v) && $v !== '') ? trim($v) : '';
            $newState['stage_alert'] = $newState['alertMessage'];
        }
        if (array_key_exists('stage_alert', $payload)) {
            $v = $payload['stage_alert'];
            $newState['stage_alert'] = (is_string($v) && $v !== '') ? trim($v) : '';
            if (($newState['stage_alert'] ?? '') === '') {
                $newState['alertMessage'] = '';
            }
        }
        // Countdown: definir countdown_ends_at para todas as telas ficarem sincronizadas (mesmo tempo restante)
        if (isset($newState['type']) && $newState['type'] === 'countdown') {
            $secs = (int) ($newState['countdown_seconds'] ?? 0);
            if ($secs > 0 && empty($newState['countdown_ends_at'])) {
                $newState['countdown_ends_at'] = time() + $secs;
            }
        }
        Cache::put(self::CACHE_KEY_STATE, $newState, now()->addDay());

        if (Schema::hasTable('projection_state_log')) {
            try {
                DB::table('projection_state_log')->insert(['state' => json_encode($newState)]);
            } catch (\Throwable $e) {
                // ignore log failures
            }
        }
        return $newState;
    }

    public function getAssets(): array
    {
        $assets = WorshipMediaAsset::latest()->get();
        $base = url('/api/v1/projection/assets/serve');
        return $assets->map(function ($a) use ($base) {
            $arr = $a->toArray();
            $filename = basename($a->file_path ?? '');
            $arr['url'] = $filename ? rtrim($base, '/') . '/' . $filename : ($a->file_path ?? '');
            return $arr;
        })->toArray();
    }

    public function uploadAsset(Request $request): array
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,webm|max:51200',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $type = str_starts_with($mime, 'video') ? 'video' : 'image';

        $path = $file->storeAs('projection_assets', $file->hashName(), 'public');

        $asset = WorshipMediaAsset::create([
            'title' => pathinfo($originalName, PATHINFO_FILENAME),
            'type' => $type,
            'file_path' => '/storage/' . $path,
            'thumbnail_path' => $type === 'image' ? '/storage/' . $path : null,
        ]);

        $arr = $asset->toArray();
        $arr['url'] = url('/api/v1/projection/assets/serve/' . basename($path));
        return $arr;
    }

    public function deleteAsset(int $id): void
    {
        $asset = WorshipMediaAsset::findOrFail($id);
        $filePath = str_replace('/storage/', '', $asset->file_path);
        Storage::disk('public')->delete($filePath);
        $asset->delete();
    }

    public function getCustomSlides(): array
    {
        return WorshipCustomSlide::latest()->get()->toArray();
    }

    public function storeCustomSlide(Request $request): array
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $slide = WorshipCustomSlide::create($request->all());
        return $slide->toArray();
    }

    public function updateCustomSlide(Request $request, int $id): array
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ]);

        $slide = WorshipCustomSlide::findOrFail($id);
        $slide->update($request->only(['title', 'content']));
        return $slide->fresh()->toArray();
    }

    public function deleteCustomSlide(int $id): void
    {
        $slide = WorshipCustomSlide::findOrFail($id);
        $slide->delete();
    }

    public function searchLyrics(string $query, LyricsService $lyricsService): array
    {
        if (strlen($query) < 1) {
            return [];
        }
        return $lyricsService->search($query);
    }

    public function addTimelineItem(Request $request): array
    {
        $rules = [
            'setlist_id' => 'required|exists:worship_setlists,id',
            'type' => 'required|in:song,bible,card,video,audio,image,countdown,announcement,section_header,event_spotlight,custom_slide',
        ];
        if ($request->input('type') === 'custom_slide') {
            $rules['custom_slide_id'] = 'required|exists:worship_custom_slides,id';
        }
        $request->validate($rules);

        $setlist = WorshipSetlist::findOrFail($request->input('setlist_id'));
        $maxOrder = $setlist->items()->max('order') ?? 0;

        $itemData = [
            'setlist_id' => $setlist->id,
            'type' => $request->input('type'),
            'order' => $maxOrder + 1,
            'title' => $request->input('title', 'Item'),
        ];

        if ($request->input('type') === 'song') {
            if ($request->input('song_id')) {
                $itemData['song_id'] = $request->input('song_id');
            } elseif ($request->input('content') || $request->input('online_content')) {
                $content = $request->input('online_content') ?? $request->input('content');
                $song = WorshipSong::create([
                    'title' => $request->input('title', 'Nova música'),
                    'artist' => $request->input('artist', 'Unknown'),
                    'content_chordpro' => $content,
                    'lyrics_only' => $content,
                    'original_key' => 'C',
                ]);
                $itemData['song_id'] = $song->id;
            }
        } elseif ($request->input('type') === 'bible') {
            $itemData['content'] = [
                'reference' => $request->input('reference'),
                'slides' => $request->input('slides', []),
            ];
            $itemData['title'] = $request->input('reference', 'Versículo');
            if ($request->input('bible_data')) {
                $itemData['metadata'] = $request->input('bible_data');
            }
        } elseif ($request->input('type') === 'card') {
            $sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
            $itemData['content'] = [
                'text' => $request->input('text', ''),
                'title_size' => in_array($request->input('title_size'), $sizes, true) ? $request->input('title_size') : 'md',
                'subtitle_size' => in_array($request->input('subtitle_size'), $sizes, true) ? $request->input('subtitle_size') : 'md',
                'description_size' => in_array($request->input('description_size'), $sizes, true) ? $request->input('description_size') : 'md',
                'card_template_id' => $request->input('card_template_id') ? (int) $request->input('card_template_id') : null,
            ];
        } elseif (in_array($request->input('type'), ['video', 'audio', 'image'], true)) {
            $itemData['content'] = [
                'asset_id' => $request->input('asset_id'),
                'url' => $request->input('url'),
            ];
        } elseif ($request->input('type') === 'countdown') {
            $itemData['content'] = [
                'duration_seconds' => (int) $request->input('duration_seconds', 300),
                'label' => $request->input('label', ''),
            ];
        } elseif ($request->input('type') === 'section_header') {
            $itemData['content'] = ['title' => $request->input('title', '')];
        } elseif ($request->input('type') === 'announcement') {
            $itemData['content'] = ['text' => $request->input('text', $request->input('title', 'Aviso'))];
            $itemData['title'] = $request->input('title', 'Aviso');
        } elseif ($request->input('type') === 'event_spotlight') {
            $itemData['content'] = [
                'event_id' => $request->input('event_id'),
            ];
        } elseif ($request->input('type') === 'custom_slide') {
            $customSlideId = (int) $request->input('custom_slide_id');
            $customSlide = WorshipCustomSlide::findOrFail($customSlideId);
            $itemData['custom_slide_id'] = $customSlide->id;
            $itemData['title'] = $customSlide->title;
        }

        $item = WorshipSetlistItem::create($itemData);
        return ['id' => $item->id, 'message' => 'Item added'];
    }

    public function deleteTimelineItem(int $id): void
    {
        $item = WorshipSetlistItem::findOrFail($id);
        $item->delete();
    }

    public function reorderTimeline(array $items): void
    {
        foreach ($items as $i) {
            WorshipSetlistItem::where('id', $i['id'])->update(['order' => (int) $i['order']]);
        }
    }

    /**
     * List upcoming events for projection (event_spotlight).
     * Consumes Events module when available.
     */
    public function getUpcomingEvents(int $limit = 20): array
    {
        if (! class_exists(\Modules\Events\App\Models\Event::class)) {
            return [];
        }
        $model = \Modules\Events\App\Models\Event::class;
        return $model::where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getThemes(): array
    {
        return ProjectionTheme::orderBy('name')->get()->toArray();
    }

    public function getTheme(int $id): ?array
    {
        $theme = ProjectionTheme::find($id);
        return $theme ? $theme->toArray() : null;
    }

    public function createTheme(array $data): array
    {
        if (! empty($data['is_default']) && $data['is_default']) {
            ProjectionTheme::query()->update(['is_default' => false]);
        }
        $theme = ProjectionTheme::create($data);
        return $theme->toArray();
    }

    public function updateTheme(int $id, array $data): array
    {
        $theme = ProjectionTheme::findOrFail($id);
        if (! empty($data['is_default']) && $data['is_default']) {
            ProjectionTheme::where('id', '!=', $id)->update(['is_default' => false]);
        }
        $theme->update($data);
        return $theme->fresh()->toArray();
    }

    public function deleteTheme(int $id): void
    {
        $theme = ProjectionTheme::findOrFail($id);
        $theme->delete();
    }

    public function setThemeDefault(int $id): array
    {
        ProjectionTheme::query()->update(['is_default' => false]);
        $theme = ProjectionTheme::findOrFail($id);
        $theme->update(['is_default' => true]);
        return $theme->fresh()->toArray();
    }

    public function getCardTemplates(): array
    {
        return ProjectionCardTemplate::orderBy('name')->get()->toArray();
    }

    public function getCardTemplate(int $id): ?array
    {
        $t = ProjectionCardTemplate::find($id);
        return $t ? $t->toArray() : null;
    }

    public function createCardTemplate(array $data): array
    {
        if (! empty($data['is_default']) && $data['is_default']) {
            ProjectionCardTemplate::query()->update(['is_default' => false]);
        }
        $t = ProjectionCardTemplate::create($data);
        return $t->toArray();
    }

    public function updateCardTemplate(int $id, array $data): array
    {
        $t = ProjectionCardTemplate::findOrFail($id);
        if (! empty($data['is_default']) && $data['is_default']) {
            ProjectionCardTemplate::where('id', '!=', $id)->update(['is_default' => false]);
        }
        $t->update($data);
        return $t->fresh()->toArray();
    }

    public function deleteCardTemplate(int $id): void
    {
        ProjectionCardTemplate::findOrFail($id)->delete();
    }

    /**
     * Build state patch for a given setlist item + slide index (for remote next/prev).
     */
    public function buildStatePatchForSlide(array $setlistData, int $itemIndex, int $slideIndex): ?array
    {
        $items = $setlistData['items'] ?? [];
        if ($itemIndex < 0 || $itemIndex >= count($items)) {
            return null;
        }
        $item = $items[$itemIndex];
        $slides = $item['slides'] ?? [];
        $slideCount = count($slides);
        if ($slideIndex < 0) {
            return null;
        }
        if ($slideCount === 0) {
            $stanza = ['label' => $item['title'] ?? 'Item', 'html' => '<p>' . e($item['title'] ?? '') . '</p>'];
        } elseif ($slideIndex >= $slideCount) {
            return null;
        } else {
            $stanza = $slides[$slideIndex];
        }
        $payload = [
            'current_setlist_id' => (int) ($setlistData['id'] ?? 0),
            'current_item_id' => (int) $item['id'],
            'current_slide_index' => $slideIndex,
            'current_item_title' => $item['title'] ?? '',
            'isClear' => false,
            'isBlackout' => false,
        ];
        if (! empty($stanza['media_type']) && ! empty($stanza['url'])) {
            $payload['type'] = $stanza['media_type'];
            $payload['content'] = '';
            $payload['url'] = $stanza['url'];
            $payload['footer'] = $item['title'] ?? null;
        } elseif (isset($stanza['countdown_seconds'])) {
            $payload['type'] = 'countdown';
            $payload['content'] = '';
            $payload['countdown_seconds'] = (int) $stanza['countdown_seconds'];
            $payload['footer'] = $stanza['label'] ?? $item['title'] ?? null;
        } else {
            $payload['type'] = 'slide';
            $payload['content'] = $stanza['html'] ?? '';
            $payload['footer'] = ($item['type'] ?? '') === 'bible' ? ($item['title'] ?? null) : null;
        }
        return $payload;
    }

    /**
     * Advance to next slide (or first slide of next item). For remote.
     */
    public function applyNextSlide(): array
    {
        $state = $this->getState();
        $setlistId = $state['current_setlist_id'] ?? null;
        if (! $setlistId) {
            return $state;
        }
        $worship = app(\Modules\Worship\App\Services\WorshipApiService::class);
        $setlist = $worship->getSetlistWithItems($setlistId);
        $items = $setlist['items'] ?? [];
        $itemId = $state['current_item_id'] ?? null;
        $slideIndex = (int) ($state['current_slide_index'] ?? 0);
        $itemIndex = -1;
        foreach ($items as $i => $it) {
            if ((int) $it['id'] === (int) $itemId) {
                $itemIndex = $i;
                break;
            }
        }
        if ($itemIndex < 0) {
            $itemIndex = 0;
            $slideIndex = 0;
        }
        $slides = $items[$itemIndex]['slides'] ?? [];
        if ($slideIndex < count($slides) - 1) {
            $slideIndex++;
        } else {
            if ($itemIndex < count($items) - 1) {
                $itemIndex++;
                $slideIndex = 0;
            }
        }
        $patch = $this->buildStatePatchForSlide($setlist, $itemIndex, $slideIndex);
        if ($patch) {
            return $this->updateState($patch);
        }
        return $state;
    }

    /**
     * Go to previous slide (or last slide of previous item). For remote.
     */
    public function applyPrevSlide(): array
    {
        $state = $this->getState();
        $setlistId = $state['current_setlist_id'] ?? null;
        if (! $setlistId) {
            return $state;
        }
        $worship = app(\Modules\Worship\App\Services\WorshipApiService::class);
        $setlist = $worship->getSetlistWithItems($setlistId);
        $items = $setlist['items'] ?? [];
        $itemId = $state['current_item_id'] ?? null;
        $slideIndex = (int) ($state['current_slide_index'] ?? 0);
        $itemIndex = -1;
        foreach ($items as $i => $it) {
            if ((int) $it['id'] === (int) $itemId) {
                $itemIndex = $i;
                break;
            }
        }
        if ($itemIndex < 0) {
            $itemIndex = 0;
            $slideIndex = 0;
        }
        if ($slideIndex > 0) {
            $slideIndex--;
        } else {
            if ($itemIndex > 0) {
                $itemIndex--;
                $slides = $items[$itemIndex]['slides'] ?? [];
                $slideIndex = max(0, count($slides) - 1);
            }
        }
        $patch = $this->buildStatePatchForSlide($setlist, $itemIndex, $slideIndex);
        if ($patch) {
            return $this->updateState($patch);
        }
        return $state;
    }

    /**
     * Build sync bundle for desktop/offline projector.
     * Returns state, setlists (with slides), themes, assets (with download url), custom_slides, logo_verses.
     * Asset URLs in state and setlist slides are normalized to filename only so the desktop app can map to local file://.
     *
     * @param  array<int>  $setlistIds  Optional. If empty, uses current_setlist_id from state and recent setlists.
     */
    public function getSyncBundle(array $setlistIds = []): array
    {
        $state = $this->getState();
        $state = $this->normalizeStateUrlsToFilename($state);

        $worship = app(\Modules\Worship\App\Services\WorshipApiService::class);
        if ($setlistIds === []) {
            $currentId = $state['current_setlist_id'] ?? null;
            $setlistIds = $currentId ? [$currentId] : [];
            $recent = WorshipSetlist::orderByDesc('scheduled_at')->take(5)->pluck('id')->toArray();
            $setlistIds = array_values(array_unique(array_merge($setlistIds, $recent)));
        }

        $setlists = [];
        foreach ($setlistIds as $id) {
            try {
                $setlist = $worship->getSetlistWithItems((int) $id);
                $setlist = $this->normalizeSetlistUrlsToFilename($setlist);
                $setlists[] = $setlist;
            } catch (\Throwable $e) {
                // skip missing setlist
            }
        }

        $themes = $this->getThemes();
        $assetsRaw = $this->getAssets();
        $assets = array_map(function ($a) {
            $filename = isset($a['file_path']) ? basename($a['file_path']) : '';
            if (! $filename && isset($a['url']) && preg_match('#/serve/([a-zA-Z0-9._-]+)$#', (string) $a['url'], $m)) {
                $filename = $m[1];
            }
            return [
                'id' => $a['id'] ?? null,
                'filename' => $filename ?: ($a['title'] ?? ''),
                'url' => $a['url'] ?? '',
            ];
        }, $assetsRaw);

        $custom_slides = $this->getCustomSlides();

        $logo_verses = [];
        if (class_exists(\Modules\Bible\App\Services\BibleApiService::class)) {
            $bibleApi = app(\Modules\Bible\App\Services\BibleApiService::class);
            for ($i = 0; $i < 8; $i++) {
                $verse = $bibleApi->getRandomVerse(null);
                if (! $verse) {
                    continue;
                }
                $chapter = $verse->chapter;
                $book = $chapter ? $chapter->book : null;
                $logo_verses[] = [
                    'id' => $verse->id,
                    'verse_number' => $verse->verse_number,
                    'text' => $verse->text,
                    'chapter' => $chapter ? ['id' => $chapter->id, 'chapter_number' => $chapter->chapter_number] : null,
                    'book' => $book ? ['id' => $book->id, 'name' => $book->name, 'abbreviation' => $book->abbreviation ?? null, 'book_number' => $book->book_number ?? null] : null,
                ];
            }
        }

        return [
            'state' => $state,
            'setlists' => $setlists,
            'themes' => $themes,
            'assets' => $assets,
            'custom_slides' => $custom_slides,
            'logo_verses' => $logo_verses,
        ];
    }

    /**
     * Normalize state['url'] from full serve URL to filename only (for desktop local file mapping).
     */
    private function normalizeStateUrlsToFilename(array $state): array
    {
        if (! empty($state['url']) && is_string($state['url'])) {
            if (preg_match('#/serve/([a-zA-Z0-9._-]+)$#', $state['url'], $m)) {
                $state['url'] = $m[1];
            } elseif (str_contains($state['url'], 'projection_assets/')) {
                $state['url'] = basename($state['url']);
            }
        }
        return $state;
    }

    /**
     * Normalize any asset URLs in setlist items (slides with url) to filename only.
     */
    private function normalizeSetlistUrlsToFilename(array $setlist): array
    {
        $items = $setlist['items'] ?? [];
        foreach ($items as $idx => $item) {
            $slides = $item['slides'] ?? [];
            foreach ($slides as $sIdx => $slide) {
                if (! empty($slide['url']) && is_string($slide['url'])) {
                    if (preg_match('#/serve/([a-zA-Z0-9._-]+)$#', $slide['url'], $m)) {
                        $slides[$sIdx]['url'] = $m[1];
                    } elseif (str_contains($slide['url'], 'projection_assets/') || str_contains($slide['url'], '/storage/')) {
                        $slides[$sIdx]['url'] = basename($slide['url']);
                    }
                }
            }
            $items[$idx]['slides'] = $slides;
        }
        $setlist['items'] = $items;
        return $setlist;
    }
}
