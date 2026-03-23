<?php

namespace Modules\Sermons\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Sermons\App\Models\BibleSeries;
use Modules\Sermons\App\Models\BibleStudy;
use Modules\Sermons\App\Models\SermonCategory;

class BibleStudyController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibleStudy::where('status', 'published')
            ->with(['series', 'category']);

        // Filter by series
        if ($request->has('series_id') && ! empty($request->series_id)) {
            $query->where('series_id', $request->series_id);
        }

        // Filter by category
        if ($request->has('category_id') && ! empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        $studies = $query->orderBy('published_at', 'desc')->paginate(12);
        $series = BibleSeries::where('status', 'published')->get();
        $categories = SermonCategory::active()->get();

        return view('sermons::memberpanel.studies.index', compact('studies', 'series', 'categories'));
    }

    public function show(BibleStudy $study): View
    {
        if ($study->status !== 'published') {
            abort(404);
        }

        $study->load(['series', 'category', 'user']);
        $study->increment('views');

        return view('sermons::memberpanel.studies.show', compact('study'));
    }
}
