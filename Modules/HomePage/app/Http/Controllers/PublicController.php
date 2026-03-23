<?php

namespace Modules\HomePage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;
use Modules\HomePage\App\Models\Event;
use Modules\HomePage\App\Models\GalleryImage;
use Modules\HomePage\App\Models\Testimonial;

class PublicController extends Controller
{
    /**
     * Display all events
     */
    public function events(Request $request)
    {
        $query = Event::active()->orderBy('start_date', 'desc');

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(12);

        return view('homepage::public.events', compact('events'));
    }

    /**
     * Display single event
     */
    public function showEvent(Event $event)
    {
        // Check if event is active
        if (! $event->is_active) {
            abort(404);
        }

        return view('homepage::public.event-detail', compact('event'));
    }

    /**
     * Display all testimonials
     */
    public function testimonials(Request $request)
    {
        $query = Testimonial::active()->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('testimonial', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $testimonials = $query->paginate(12);

        return view('homepage::public.testimonials', compact('testimonials'));
    }

    /**
     * Display single testimonial
     */
    public function showTestimonial(Testimonial $testimonial)
    {
        // Check if testimonial is active
        if (! $testimonial->is_active) {
            abort(404);
        }

        return view('homepage::public.testimonial-detail', compact('testimonial'));
    }

    /**
     * Display gallery
     */
    public function gallery(Request $request)
    {
        $query = GalleryImage::active()->orderBy('order', 'asc')->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->has('category') && ! empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $images = $query->paginate(20);

        // Get available categories
        $categories = GalleryImage::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort();

        return view('homepage::public.gallery', compact('images', 'categories'));
    }

    /**
     * Display single gallery image
     */
    public function showGalleryImage(GalleryImage $image)
    {
        // Check if image is active
        if (! $image->is_active) {
            abort(404);
        }

        // Get related images
        $relatedImages = GalleryImage::active()
            ->where('id', '!=', $image->id)
            ->when($image->category, function ($query) use ($image) {
                return $query->where('category', $image->category);
            })
            ->orderBy('order', 'asc')
            ->limit(6)
            ->get();

        return view('homepage::public.gallery-detail', compact('image', 'relatedImages'));
    }

    /**
     * Display verse context
     */
    public function verseContext(Request $request)
    {
        $bookId = $request->get('book_id');
        $chapterNumber = $request->get('chapter');
        $verseNumber = $request->get('verse');

        if (! $bookId || ! $chapterNumber || ! $verseNumber) {
            abort(404);
        }

        // Get the book
        $book = Book::findOrFail($bookId);

        // Get the chapter
        $chapter = Chapter::where('book_id', $book->id)
            ->where('chapter_number', $chapterNumber)
            ->firstOrFail();

        // Get the specific verse
        $verse = Verse::where('chapter_id', $chapter->id)
            ->where('verse_number', $verseNumber)
            ->firstOrFail();

        // Get surrounding verses (context)
        $contextVerses = Verse::where('chapter_id', $chapter->id)
            ->whereBetween('verse_number', [
                max(1, $verseNumber - 3),
                $verseNumber + 3,
            ])
            ->orderBy('verse_number')
            ->get();

        return view('homepage::public.verse-context', compact('verse', 'contextVerses', 'book', 'chapter'));
    }

    /**
     * Display all active ministries
     */
    public function ministries(Request $request)
    {
        $ministries = \Modules\Ministries\App\Models\Ministry::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('homepage::public.ministries', compact('ministries'));
    }
}
