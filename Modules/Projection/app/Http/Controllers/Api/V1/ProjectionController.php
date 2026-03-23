<?php

namespace Modules\Projection\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Modules\Projection\App\Http\Controllers\Admin\ProjectionSettingsController;
use Modules\Projection\App\Services\ProjectionApiService;
use Modules\Worship\App\Services\LyricsService;

/**
 * API central de projeção (v1).
 * Respostas com { data }. Única entrada para clientes (console, screen, remote).
 */
class ProjectionController extends Controller
{
    public function __construct(
        private ProjectionApiService $api,
        private LyricsService $lyricsService
    ) {}

    public function getState(): JsonResponse
    {
        $data = $this->api->getState();
        return response()->json(['data' => $data]);
    }

    /**
     * Public state endpoint for screen display when viewer token is enabled and token matches.
     * Token is configured in Admin > Projeção > Configurações (or via PROJECTION_VIEWER_TOKEN in .env).
     * GET /api/v1/projection/viewer/state?viewer_token=xxx (no auth required).
     */
    public function getStateForViewer(Request $request): JsonResponse
    {
        if (! $this->validateViewerToken($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $data = $this->api->getState();
        return response()->json(['data' => $data]);
    }

    /**
     * Sync bundle for desktop/offline projector. Requires viewer_token or auth.
     * GET /api/v1/projection/sync/bundle?viewer_token=xxx&setlist_ids[]=1&setlist_ids[]=2
     */
    public function getSyncBundle(Request $request): JsonResponse
    {
        if (! $request->user()) {
            if (! $this->validateViewerToken($request)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }
        $setlistIds = $request->input('setlist_ids', []);
        $setlistIds = is_array($setlistIds) ? array_map('intval', array_filter($setlistIds)) : [];
        $bundle = $this->api->getSyncBundle($setlistIds);
        return response()->json(['data' => $bundle]);
    }

    private function validateViewerToken(Request $request): bool
    {
        $enabled = (bool) Settings::get(ProjectionSettingsController::KEY_VIEWER_ENABLED, false);
        if (! $enabled) {
            return false;
        }
        $token = (string) Settings::get(ProjectionSettingsController::KEY_VIEWER_TOKEN, '');
        if ($token === '') {
            $token = (string) config('projection.viewer_token', '');
        }
        if ($token === '' || $request->input('viewer_token') !== $token) {
            return false;
        }
        return true;
    }

    public function updateState(Request $request): JsonResponse
    {
        $data = $this->api->updateState($request->all());
        return response()->json(['data' => $data]);
    }

    public function getAssets(): JsonResponse
    {
        $data = $this->api->getAssets();
        return response()->json(['data' => $data]);
    }

    public function uploadAsset(Request $request): JsonResponse
    {
        $data = $this->api->uploadAsset($request);
        return response()->json(['data' => $data]);
    }

    public function deleteAsset(int $id): JsonResponse
    {
        $this->api->deleteAsset($id);
        return response()->json(['data' => ['message' => 'ok']]);
    }

    public function getSlides(): JsonResponse
    {
        $data = $this->api->getCustomSlides();
        return response()->json(['data' => $data]);
    }

    public function storeSlide(Request $request): JsonResponse
    {
        $data = $this->api->storeCustomSlide($request);
        return response()->json(['data' => $data]);
    }

    public function updateSlide(Request $request, int $id): JsonResponse
    {
        $data = $this->api->updateCustomSlide($request, $id);
        return response()->json(['data' => $data]);
    }

    public function deleteSlide(int $id): JsonResponse
    {
        $this->api->deleteCustomSlide($id);
        return response()->json(['data' => ['message' => 'ok']]);
    }

    public function searchLyrics(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $data = $this->api->searchLyrics($q, $this->lyricsService);
        return response()->json(['data' => $data]);
    }

    public function addTimelineItem(Request $request): JsonResponse
    {
        $data = $this->api->addTimelineItem($request);
        return response()->json(['data' => $data]);
    }

    public function deleteTimelineItem(Request $request, int $id): JsonResponse
    {
        $this->api->deleteTimelineItem($id);
        return response()->json(['data' => ['message' => 'ok']]);
    }

    public function reorderTimeline(Request $request): JsonResponse
    {
        $request->validate(['items' => 'required|array', 'items.*.id' => 'required', 'items.*.order' => 'required|integer']);
        $this->api->reorderTimeline($request->input('items'));
        return response()->json(['data' => ['message' => 'ok']]);
    }

    public function getUpcomingEvents(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->input('limit', 20), 1), 50);
        $data = $this->api->getUpcomingEvents($limit);
        return response()->json(['data' => $data]);
    }

    public function getThemes(): JsonResponse
    {
        $data = $this->api->getThemes();
        return response()->json(['data' => $data]);
    }

    public function getTheme(int $id): JsonResponse
    {
        $data = $this->api->getTheme($id);
        if ($data === null) {
            return response()->json(['message' => 'Theme not found'], 404);
        }
        return response()->json(['data' => $data]);
    }

    public function storeTheme(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'background_type' => 'nullable|in:solid,gradient,image,video',
            'background_value' => 'nullable|string',
            'font_family' => 'nullable|string|max:128',
            'font_size_base' => 'nullable|string|max:32',
            'text_color' => 'nullable|string|max:32',
            'text_shadow' => 'nullable|string|max:128',
            'alignment' => 'nullable|string|max:16',
            'padding' => 'nullable|integer|min:0|max:255',
            'is_default' => 'nullable|boolean',
        ]);
        $data = $this->api->createTheme($request->all());
        return response()->json(['data' => $data], 201);
    }

    public function updateTheme(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255',
            'background_type' => 'nullable|in:solid,gradient,image,video',
            'background_value' => 'nullable|string',
            'font_family' => 'nullable|string|max:128',
            'font_size_base' => 'nullable|string|max:32',
            'text_color' => 'nullable|string|max:32',
            'text_shadow' => 'nullable|string|max:128',
            'alignment' => 'nullable|string|max:16',
            'padding' => 'nullable|integer|min:0|max:255',
            'is_default' => 'nullable|boolean',
        ]);
        $data = $this->api->updateTheme($id, $request->all());
        return response()->json(['data' => $data]);
    }

    public function deleteTheme(int $id): JsonResponse
    {
        $this->api->deleteTheme($id);
        return response()->json(['data' => ['message' => 'ok']]);
    }

    public function setThemeDefault(int $id): JsonResponse
    {
        $data = $this->api->setThemeDefault($id);
        return response()->json(['data' => $data]);
    }

    /** Next slide (or first slide of next item). For remote. */
    public function nextSlide(): JsonResponse
    {
        $data = $this->api->applyNextSlide();
        return response()->json(['data' => $data]);
    }

    /** Previous slide (or last slide of previous item). For remote. */
    public function prevSlide(): JsonResponse
    {
        $data = $this->api->applyPrevSlide();
        return response()->json(['data' => $data]);
    }

    public function getCardTemplates(): JsonResponse
    {
        $data = $this->api->getCardTemplates();
        return response()->json(['data' => $data]);
    }

    public function getCardTemplate(int $id): JsonResponse
    {
        $data = $this->api->getCardTemplate($id);
        if ($data === null) {
            return response()->json(['message' => 'Card template not found'], 404);
        }
        return response()->json(['data' => $data]);
    }

    /**
     * Serve projection asset file (image/video) so the screen can load it without 403.
     * Public route (throttle only) so the projector tab/device can request the file.
     * Tries exact filename first, then without extension (Laravel hashName() has no extension).
     */
    public function serveAsset(string $filename): Response|JsonResponse
    {
        if (! preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
            return response()->json(['message' => 'Invalid filename'], 400);
        }
        $path = 'projection_assets/' . $filename;
        if (! Storage::disk('public')->exists($path)) {
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            if ($nameWithoutExt !== '' && $nameWithoutExt !== $filename) {
                $pathAlt = 'projection_assets/' . $nameWithoutExt;
                if (Storage::disk('public')->exists($pathAlt)) {
                    $path = $pathAlt;
                }
            }
        }
        if (! Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $requestedExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = match ($requestedExt ?: pathinfo($path, PATHINFO_EXTENSION)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            default => 'application/octet-stream',
        };
        try {
            $content = Storage::disk('public')->get($path);
            return response($content, 200, [
                'Content-Type' => $mime,
                'Content-Length' => (string) strlen($content),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error serving file'], 500);
        }
    }
}
