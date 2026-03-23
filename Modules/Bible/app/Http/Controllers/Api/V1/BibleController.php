<?php

namespace Modules\Bible\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Bible\App\Http\Requests\Api\FindByReferenceRequest;
use Modules\Bible\App\Http\Requests\Api\IndexBooksRequest;
use Modules\Bible\App\Http\Requests\Api\IndexChaptersRequest;
use Modules\Bible\App\Http\Requests\Api\IndexVersesRequest;
use Modules\Bible\App\Http\Requests\Api\SearchBibleRequest;
use Modules\Bible\App\Http\Resources\BibleVersionResource;
use Modules\Bible\App\Http\Resources\BookResource;
use Modules\Bible\App\Http\Resources\ChapterResource;
use Modules\Bible\App\Http\Resources\FindByReferenceResource;
use Modules\Bible\App\Http\Resources\RandomVerseResource;
use Modules\Bible\App\Http\Resources\VerseResource;
use Modules\Bible\App\Services\BibleApiService;

class BibleController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    /**
     * GET /versions
     */
    public function versions(): JsonResponse
    {
        $versions = $this->bibleApi->getVersions();
        if ($versions->isEmpty()) {
            return response()->json(['data' => [], 'message' => 'Nenhuma versão ativa encontrada.'], 404);
        }

        return response()->json([
            'data' => BibleVersionResource::collection($versions),
        ]);
    }

    /**
     * GET /books?version_id=
     */
    public function books(IndexBooksRequest $request): JsonResponse
    {
        $versionId = $request->validated('version_id') ? (int) $request->input('version_id') : null;
        $books = $this->bibleApi->getBooks($versionId);
        if ($books->isEmpty()) {
            return response()->json(['data' => [], 'message' => 'Nenhum livro encontrado para esta versão.'], 404);
        }

        return response()->json([
            'data' => BookResource::collection($books),
        ]);
    }

    /**
     * GET /chapters?book_id= OR book_name= & version_id=
     */
    public function chapters(IndexChaptersRequest $request): JsonResponse
    {
        $bookId = $request->filled('book_id') ? (int) $request->input('book_id') : null;
        $bookName = $request->input('book_name');
        $versionId = $request->filled('version_id') ? (int) $request->input('version_id') : null;

        $chapters = $this->bibleApi->getChapters($bookId, $bookName, $versionId);
        if ($chapters->isEmpty()) {
            return response()->json(['data' => [], 'message' => 'Nenhum capítulo encontrado.'], 404);
        }

        return response()->json([
            'data' => ChapterResource::collection($chapters),
        ]);
    }

    /**
     * GET /verses?chapter_id= OR book_id= & chapter_number= & optional verse_range=
     */
    public function verses(IndexVersesRequest $request): JsonResponse
    {
        $chapterId = $request->filled('chapter_id') ? (int) $request->input('chapter_id') : null;
        $bookId = $request->filled('book_id') ? (int) $request->input('book_id') : null;
        $chapterNumber = $request->filled('chapter_number') ? (int) $request->input('chapter_number') : null;
        $verseRange = $request->input('verse_range');

        $verses = $this->bibleApi->getVerses($chapterId, $bookId, $chapterNumber, $verseRange);
        if ($verses->isEmpty()) {
            return response()->json(['data' => [], 'message' => 'Nenhum versículo encontrado.'], 404);
        }

        return response()->json([
            'data' => VerseResource::collection($verses),
        ]);
    }

    /**
     * GET /find?ref=João 3:16
     */
    public function find(FindByReferenceRequest $request): JsonResponse
    {
        $result = $this->bibleApi->findByReference($request->validated('ref'));
        if ($result === null) {
            return response()->json(['message' => 'Referência não encontrada ou formato inválido.'], 404);
        }

        return response()->json([
            'data' => new FindByReferenceResource($result),
        ]);
    }

    /**
     * GET /search?q=
     */
    public function search(SearchBibleRequest $request): JsonResponse
    {
        $query = $request->validated('q');

        // Try exact reference first
        $findResult = $this->bibleApi->findByReference($query);
        if ($findResult !== null) {
            return response()->json([
                'data' => [
                    'type' => 'exact',
                    'reference' => $findResult['reference'],
                    'book_number' => $findResult['book_number'] ?? null,
                    'chapter_number' => $findResult['chapter'] ?? null,
                    'verses' => VerseResource::collection($findResult['verses']),
                    'full_chapter_url' => $findResult['full_chapter_url'],
                ],
            ]);
        }

        $verses = $this->bibleApi->search($query, 10)->load('chapter.book');
        $items = $verses->map(fn ($v) => [
            'id' => $v->id,
            'reference' => $v->full_reference,
            'text' => $v->text,
            'type' => 'search',
            'book_number' => $v->chapter->book->book_number ?? null,
            'chapter_number' => $v->chapter->chapter_number ?? null,
            'verse_number' => $v->verse_number ?? null,
        ]);

        return response()->json(['data' => $items]);
    }

    /**
     * GET /random?version_id= (optional)
     */
    public function random(Request $request): JsonResponse
    {
        $versionId = $request->filled('version_id') ? (int) $request->input('version_id') : null;
        $verse = $this->bibleApi->getRandomVerse($versionId);
        if (! $verse) {
            return response()->json(['message' => 'Nenhum versículo disponível.'], 404);
        }

        return response()->json([
            'data' => new RandomVerseResource($verse),
        ]);
    }

    /**
     * GET /compare?v1=&v2=&book_number=&chapter=&verse=
     */
    public function compare(Request $request): JsonResponse
    {
        $v1 = $request->input('v1');
        $v2 = $request->input('v2');
        $bookNumber = (int) $request->input('book_number');
        $chapter = (int) $request->input('chapter');
        $verse = $request->filled('verse') ? (int) $request->input('verse') : null;

        $result = $this->bibleApi->compare($v1, $v2, $bookNumber, $chapter, $verse);
        if ($result === null) {
            return response()->json(['message' => 'Versões ou livro não encontrados.'], 404);
        }

        return response()->json([
            'data' => [
                'v1' => [
                    'abbreviation' => $result['v1']['abbreviation'],
                    'name' => $result['v1']['name'],
                    'verses' => VerseResource::collection($result['v1']['verses']),
                ],
                'v2' => [
                    'abbreviation' => $result['v2']['abbreviation'],
                    'name' => $result['v2']['name'],
                    'verses' => VerseResource::collection($result['v2']['verses']),
                ],
            ],
        ]);
    }

    /**
     * GET /audio-url?version_id= OR version= &book_number= &chapter_number=
     * Returns { data: { url: "..." } } or { data: null }
     */
    public function audioUrl(Request $request): JsonResponse
    {
        $versionId = $request->filled('version_id') ? (int) $request->input('version_id') : null;
        $versionAbbr = $request->input('version');
        $bookNumber = $request->filled('book_number') ? (int) $request->input('book_number') : null;
        $chapterNumber = $request->filled('chapter_number') ? (int) $request->input('chapter_number') : null;

        if (($versionId === null && empty($versionAbbr)) || $bookNumber === null || $chapterNumber === null) {
            return response()->json([
                'data' => null,
                'message' => 'Parâmetros version_id ou version, book_number e chapter_number são obrigatórios.',
            ], 400);
        }

        $version = $versionId !== null ? $versionId : $versionAbbr;
        $url = $this->bibleApi->getChapterAudioUrl($version, $bookNumber, $chapterNumber);

        return response()->json([
            'data' => $url !== null ? ['url' => $url] : null,
        ]);
    }

    /**
     * GET /panorama?book_number=1 OR book_id= (resolves to book_number). Optional language=pt.
     * Returns { data: { author, date_written, theme_central, recipients } }
     */
    public function panorama(Request $request): JsonResponse
    {
        $bookNumber = $request->filled('book_number') ? (int) $request->input('book_number') : null;
        $bookId = $request->filled('book_id') ? (int) $request->input('book_id') : null;
        $language = $request->input('language', 'pt');

        if ($bookNumber === null && $bookId !== null) {
            $book = \Modules\Bible\App\Models\Book::find($bookId);
            $bookNumber = $book?->book_number;
        }

        if ($bookNumber === null || $bookNumber < 1 || $bookNumber > 66) {
            return response()->json([
                'data' => null,
                'message' => 'Parâmetro book_number (1-66) ou book_id válido é obrigatório.',
            ], 400);
        }

        $data = $this->bibleApi->getPanoramaByBookNumber($bookNumber, $language);

        return response()->json([
            'data' => $data,
        ]);
    }
}
