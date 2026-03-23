<?php

namespace Modules\Bible\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;

/**
 * Bíblia pública – acesso sem autenticação, totalmente responsiva.
 * Em modo manutenção, as views usam hideNavFooter para não exibir navbar/footer (só Bíblia).
 */
class PublicBibleController extends Controller
{
    /** Dados compartilhados para views públicas (ex.: esconder navbar em manutenção). */
    protected function publicViewData(array $data = []): array
    {
        $base = defined('LARAVEL_MAINTENANCE_SECRET') ? ['hideNavFooter' => true] : [];

        return array_merge($base, $data);
    }

    /**
     * Página inicial: redireciona para a primeira versão ou exibe seletor de versão.
     */
    public function index(): View|RedirectResponse
    {
        $versions = BibleVersion::active()->orderBy('name')->get();
        if ($versions->isEmpty()) {
            return view('bible::public.no-version', $this->publicViewData());
        }

        return view('bible::public.index', $this->publicViewData(['versions' => $versions]));
    }

    /**
     * Página de busca: campo de busca e resultados via API (Alpine).
     */
    public function search(): View
    {
        $versions = BibleVersion::active()->orderBy('name')->get();

        return view('bible::public.search', $this->publicViewData([
            'versions' => $versions,
            'apiBase' => url('api/v1/bible'),
        ]));
    }

    /**
     * Lista de livros (VT + NT) para uma versão.
     */
    public function read(?string $versionAbbr = null): View|RedirectResponse
    {
        if (! $versionAbbr) {
            return redirect()->route('bible.public.index');
        }

        $version = BibleVersion::where('abbreviation', $versionAbbr)->first();
        if (! $version) {
            return redirect()->route('bible.public.index')->with('error', 'Versão não encontrada.');
        }

        $versions = BibleVersion::active()->orderBy('name')->get();
        $books = $version->books()->ordered()->get();
        $oldTestament = $books->where('testament', 'old');
        $newTestament = $books->where('testament', 'new');

        return view('bible::public.read', $this->publicViewData(compact('version', 'versions', 'books', 'oldTestament', 'newTestament')));
    }

    /**
     * Capítulos de um livro.
     */
    public function book(string $versionAbbr, int $bookNumber): View|RedirectResponse
    {
        $version = BibleVersion::where('abbreviation', $versionAbbr)->first();
        if (! $version) {
            return redirect()->route('bible.public.index')->with('error', 'Versão não encontrada.');
        }

        $book = $version->books()->where('book_number', $bookNumber)->first();
        if (! $book) {
            return redirect()->route('bible.public.read', $versionAbbr)->with('error', 'Livro não encontrado.');
        }

        $chapters = $book->chapters()->orderBy('chapter_number')->get();
        $versions = BibleVersion::active()->orderBy('name')->get();

        return view('bible::public.book', $this->publicViewData(compact('version', 'versions', 'book', 'chapters')));
    }

    /**
     * Versículos de um capítulo.
     */
    public function chapter(string $versionAbbr, int $bookNumber, int $chapterNumber): View|RedirectResponse
    {
        $version = BibleVersion::where('abbreviation', $versionAbbr)->first();
        if (! $version) {
            return redirect()->route('bible.public.index')->with('error', 'Versão não encontrada.');
        }

        $book = $version->books()->where('book_number', $bookNumber)->first();
        if (! $book) {
            return redirect()->route('bible.public.read', $versionAbbr)->with('error', 'Livro não encontrado.');
        }

        $chapter = $book->chapters()->where('chapter_number', $chapterNumber)->first();
        if (! $chapter) {
            return redirect()->route('bible.public.book', [$versionAbbr, $bookNumber])->with('error', 'Capítulo não encontrado.');
        }

        $verses = $chapter->verses()->orderBy('verse_number')->get();
        $versions = BibleVersion::active()->orderBy('name')->get();

        $previousChapter = $book->chapters()
            ->with('book')
            ->where('chapter_number', '<', $chapterNumber)
            ->orderBy('chapter_number', 'desc')
            ->first();

        $nextChapter = $book->chapters()
            ->with('book')
            ->where('chapter_number', '>', $chapterNumber)
            ->orderBy('chapter_number')
            ->first();

        if (! $nextChapter) {
            $nextBook = $version->books()->where('book_number', '>', $book->book_number)->orderBy('book_number')->first();
            if ($nextBook) {
                $nextChapter = $nextBook->chapters()->with('book')->orderBy('chapter_number')->first();
            }
        }

        $totalChapters = $book->chapters()->count();
        $books = $version->books()->ordered()->get();
        $oldTestament = $books->where('testament', 'old');
        $newTestament = $books->where('testament', 'new');

        $chapterAudioUrl = $version->getChapterAudioUrl($book->book_number, $chapter->chapter_number);

        return view('bible::public.chapter', $this->publicViewData(compact(
            'version', 'versions', 'book', 'chapter', 'verses',
            'previousChapter', 'nextChapter', 'totalChapters',
            'oldTestament', 'newTestament', 'chapterAudioUrl'
        )));
    }
}
