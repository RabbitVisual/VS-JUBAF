<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\HomePage\App\Models\CarouselSlide;

class CarouselController extends Controller
{
    /**
     * Display a listing of the slides.
     */
    public function index()
    {
        $slides = CarouselSlide::ordered()->get();

        return view('homepage::admin.homepage.carousel.index', compact('slides'));
    }

    /**
     * Show the form for creating a new slide.
     */
    public function create()
    {
        return view('homepage::admin.homepage.carousel.form');
    }

    /**
     * Store a newly created slide in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateSlide($request, true);

        // Process image
        if ($request->hasFile('image')) {
            $validated['image'] = $this->processImage($request->file('image'));
        }

        // Process logo
        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $this->processImage($request->file('logo'), 'logo_');
        }

        // Set defaults
        $validated['order'] = (CarouselSlide::max('order') ?? 0) + 1;
        $validated['is_active'] = $validated['is_active'] ?? true;
        // Alt text default
        if (empty($validated['alt_text']) && ! empty($validated['title'])) {
            $validated['alt_text'] = $validated['title'];
        }

        CarouselSlide::create($validated);

        return redirect()->route('admin.homepage.carousel.index')
            ->with('success', 'Slide criado com sucesso!');
    }

    /**
     * Show the form for editing the specified slide.
     */
    public function edit(CarouselSlide $slide)
    {
        return view('homepage::admin.homepage.carousel.form', compact('slide'));
    }

    /**
     * Update the specified slide in storage.
     */
    public function update(Request $request, CarouselSlide $slide)
    {
        $validated = $this->validateSlide($request, false);

        // Process image
        if ($request->hasFile('image')) {
            // Delete old
            if ($slide->image && Storage::disk('public')->exists($slide->image)) {
                Storage::disk('public')->delete($slide->image);
            }
            $validated['image'] = $this->processImage($request->file('image'));
        } else {
            unset($validated['image']); // Keep existing
        }

        // Process logo
        if ($request->hasFile('logo')) {
            // Delete old
            if ($slide->logo_path && Storage::disk('public')->exists($slide->logo_path)) {
                Storage::disk('public')->delete($slide->logo_path);
            }
            $validated['logo_path'] = $this->processImage($request->file('logo'), 'logo_');
        } else {
            unset($validated['logo_path']); // Keep existing
        }

        // Auto alt_text
        if (empty($validated['alt_text']) && ! empty($validated['title'])) {
            $validated['alt_text'] = $validated['title'];
        }

        $slide->update($validated);

        return redirect()->route('admin.homepage.carousel.index')
            ->with('success', 'Slide atualizado com sucesso!');
    }

    /**
     * Remove the specified slide from storage.
     */
    public function destroy(CarouselSlide $slide)
    {
        if ($slide->image && Storage::disk('public')->exists($slide->image)) {
            Storage::disk('public')->delete($slide->image);
        }

        $slide->delete();

        return redirect()->route('admin.homepage.carousel.index')
            ->with('success', 'Slide excluído com sucesso!');
    }

    /**
     * Duplicate a slide.
     */
    public function duplicate(CarouselSlide $slide)
    {
        $newOrder = (CarouselSlide::max('order') ?? 0) + 1;
        $attrs = $slide->only([
            'title', 'description', 'alt_text', 'link', 'link_text', 'text_position', 'text_alignment',
            'logo_position', 'logo_scale', 'overlay_opacity', 'overlay_color', 'text_color', 'button_style',
            'transition_type', 'transition_duration', 'show_indicators', 'show_controls', 'starts_at', 'ends_at',
        ]);
        $attrs['is_active'] = false;
        $attrs['order'] = $newOrder;
        $attrs['title'] = ($slide->title ?? 'Slide') . ' (cópia)';
        $attrs['image'] = $slide->image;
        $attrs['logo_path'] = $slide->logo_path;

        CarouselSlide::create($attrs);

        return redirect()->route('admin.homepage.carousel.index')
            ->with('success', 'Slide duplicado. Edite o novo slide para alterar imagem se necessário.');
    }

    /**
     * Update the order of slides.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'slides' => 'required|array',
            'slides.*.id' => 'required|exists:carousel_slides,id',
            'slides.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->slides as $slideData) {
            CarouselSlide::where('id', $slideData['id'])
                ->update(['order' => $slideData['order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(CarouselSlide $slide)
    {
        $slide->update(['is_active' => ! $slide->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $slide->is_active,
            'message' => $slide->is_active ? 'Slide ativado!' : 'Slide desativado!',
        ]);
    }

    /**
     * Validate request data.
     */
    private function validateSlide(Request $request, $requireImage = false)
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'alt_text' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'link_text' => 'nullable|string|max:100',
            'text_position' => 'nullable|string|in:center,left,right,top,bottom',
            'text_alignment' => 'nullable|string|in:left,center,right',
            'logo_position' => 'nullable|string|in:top_center,top_left,top_right,bottom_center,bottom_left,bottom_right,center,custom',
            'logo_scale' => 'nullable|integer|min:10|max:200',
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
            'overlay_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'button_style' => 'nullable|string|in:primary,secondary,outline',
            'is_active' => 'boolean',
            'transition_type' => 'nullable|string|in:fade,slide,zoom',
            'transition_duration' => 'nullable|integer|min:100|max:5000',
            'show_indicators' => 'boolean',
            'show_controls' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ];

        if ($requireImage) {
            $rules['image'] = 'required|image|mimes:jpeg,jpg,png,webp|max:10240';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240';
        }

        $rules['logo'] = 'nullable|image|mimes:png,svg,webp,jpeg,jpg|max:5120'; // 5MB

        return $request->validate($rules);
    }

    /**
     * Process image upload.
     */
    private function processImage($file, $prefix = 'carousel_')
    {
        $filename = $prefix.Str::random(20).'_'.time().'.'.$file->getClientOriginalExtension();

        return $file->storeAs('homepage/carousel', $filename, 'public');
    }
}
