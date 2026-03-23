<?php

namespace Modules\Sermons\App\Http\Controllers\liderancaal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Sermons\App\Models\BibleSeries;

class BibleSeriesController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibleSeries::withCount(['sermons', 'studies']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $series = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('sermons::liderancapanel.series.index', compact('series'));
    }

    public function create(): View
    {
        return view('sermons::liderancapanel.series.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        if ($request->hasFile('image_file')) {
            $validated['image'] = $request->file('image_file')->store('sermons/series', 'public');
        }
        BibleSeries::create($validated);
        return redirect()->route('lideranca.sermoes.series.index')->with('success', 'Série criada com sucesso!');
    }

    public function edit(BibleSeries $series): View
    {
        return view('sermons::liderancapanel.series.edit', compact('series'));
    }

    public function update(Request $request, BibleSeries $series): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);
        if ($request->has('title') && $request->title !== $series->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        }
        if ($request->hasFile('image_file')) {
            if ($series->image && \Storage::disk('public')->exists($series->image)) {
                \Storage::disk('public')->delete($series->image);
            }
            $validated['image'] = $request->file('image_file')->store('sermons/series', 'public');
        }
        $series->update($validated);
        return redirect()->route('lideranca.sermoes.series.index')->with('success', 'Série atualizada com sucesso!');
    }

    public function destroy(BibleSeries $series): RedirectResponse
    {
        $series->delete();
        return redirect()->route('lideranca.sermoes.series.index')->with('success', 'Série removida com sucesso!');
    }
}
