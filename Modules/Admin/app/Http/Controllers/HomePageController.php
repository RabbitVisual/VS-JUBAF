<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Modules\HomePage\App\Models\CarouselSlide;

class HomePageController extends Controller
{
    /**
     * Display the homepage settings page.
     */
    public function index()
    {
        $carouselSlides = CarouselSlide::ordered()->get();
        $homepageSettings = $this->getHomepageSettings();

        return view('homepage::admin.homepage.index', compact('carouselSlides', 'homepageSettings'));
    }

    /**
     * Get all homepage settings
     */
    private function getHomepageSettings()
    {
        return [
            // Hero Section
            'hero_title' => Settings::get('homepage_hero_title', 'Bem-vindo à Igreja Batista Avenida'),
            'hero_subtitle' => Settings::get('homepage_hero_subtitle', 'Uma comunidade de fé comprometida com o Evangelho de Jesus Cristo e o serviço ao próximo em Coração de Maria - BA'),
            'hero_button_1_text' => Settings::get('homepage_hero_button_1_text', 'Conheça Nossa História'),
            'hero_button_1_link' => Settings::get('homepage_hero_button_1_link', '#sobre'),
            'hero_button_2_text' => Settings::get('homepage_hero_button_2_text', 'Próximos Eventos'),
            'hero_button_2_link' => Settings::get('homepage_hero_button_2_link', '#eventos'),

            // About Section
            'about_title' => Settings::get('homepage_about_title', 'Sobre Nossa Igreja'),
            'about_description' => Settings::get('homepage_about_description', ''),

            // Section Visibility
            'show_ministries' => Settings::get('homepage_show_ministries', true),
            'show_events' => Settings::get('homepage_show_events', true),
            'show_campaigns' => Settings::get('homepage_show_campaigns', true),
            'show_testimonials' => Settings::get('homepage_show_testimonials', true),
            'show_gallery' => Settings::get('homepage_show_gallery', true),
            'show_daily_verse' => Settings::get('homepage_show_daily_verse', true),
            'show_statistics' => Settings::get('homepage_show_statistics', true),
            'show_newsletter' => Settings::get('homepage_show_newsletter', true),

            // Section Titles
            'ministries_title' => Settings::get('homepage_ministries_title', 'Nossos Ministérios'),
            'events_title' => Settings::get('homepage_events_title', 'Próximos Eventos'),
            'campaigns_title' => Settings::get('homepage_campaigns_title', 'Campanhas em Andamento'),
            'testimonials_title' => Settings::get('homepage_testimonials_title', 'Testemunhos'),
            'gallery_title' => Settings::get('homepage_gallery_title', 'Galeria de Fotos'),
            'daily_verse_title' => Settings::get('homepage_daily_verse_title', 'Versículo do Dia'),
            'statistics_title' => Settings::get('homepage_statistics_title', 'Nossa Comunidade'),
            'newsletter_title' => Settings::get('homepage_newsletter_title', 'Mantenha-se Conectado'),

            // Carousel Settings
            'carousel_enabled' => Settings::get('homepage_carousel_enabled', false),
            'carousel_interval' => Settings::get('homepage_carousel_interval', 5000),
            'carousel_autoplay' => Settings::get('homepage_carousel_autoplay', true),
            'carousel_height' => Settings::get('homepage_carousel_height', 'h-96'),
            'carousel_indicators' => Settings::get('homepage_carousel_indicators', true),
            'carousel_controls' => Settings::get('homepage_carousel_controls', true),
            'carousel_transition' => Settings::get('homepage_carousel_transition', 'fade'),

            // Scroll Navigation Buttons
            'show_scroll_to_top' => Settings::get('homepage_show_scroll_to_top', true),
            'show_scroll_to_bottom' => Settings::get('homepage_show_scroll_to_bottom', true),
            'scroll_button_position' => Settings::get('homepage_scroll_button_position', 'bottom-right'),
            'scroll_button_size' => Settings::get('homepage_scroll_button_size', 'medium'),
            'scroll_animation_type' => Settings::get('homepage_scroll_animation_type', 'smooth'),

            // Contact Info
            'contact_address' => Settings::get('homepage_contact_address', 'Avenida, Coração de Maria - BA'),
            'contact_phone' => Settings::get('homepage_contact_phone', '(75) 0000-0000'),
            'contact_email' => Settings::get('homepage_contact_email', 'contato@igrejabatistaavenida.com.br'),

            // Social Media
            'social_facebook' => Settings::get('homepage_social_facebook', ''),
            'social_instagram' => Settings::get('homepage_social_instagram', ''),
            'social_youtube' => Settings::get('homepage_social_youtube', ''),

            // Statistics
            'stats_members' => Settings::get('homepage_stats_members', 150),
            'stats_years' => Settings::get('homepage_stats_years', 50),
            'ministries_count' => Settings::get('homepage_ministries_count', 4),
            'campaigns_count' => Settings::get('homepage_campaigns_count', 4),
        ];
    }

    /**
     * Update homepage settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            // Hero
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_button_1_text' => 'nullable|string|max:100',
            'hero_button_1_link' => 'nullable|string|max:255',
            'hero_button_2_text' => 'nullable|string|max:100',
            'hero_button_2_link' => 'nullable|string|max:255',

            // About
            'about_title' => 'nullable|string|max:255',
            'about_description' => 'nullable|string',

            // Section Visibility
            'show_ministries' => 'boolean',
            'show_events' => 'boolean',
            'show_campaigns' => 'boolean',
            'show_testimonials' => 'boolean',
            'show_gallery' => 'boolean',
            'show_daily_verse' => 'boolean',
            'show_statistics' => 'boolean',
            'show_newsletter' => 'boolean',

            // Section Titles
            'ministries_title' => 'nullable|string|max:255',
            'events_title' => 'nullable|string|max:255',
            'campaigns_title' => 'nullable|string|max:255',
            'testimonials_title' => 'nullable|string|max:255',
            'gallery_title' => 'nullable|string|max:255',
            'daily_verse_title' => 'nullable|string|max:255',
            'statistics_title' => 'nullable|string|max:255',
            'newsletter_title' => 'nullable|string|max:255',

            // Carousel
            'carousel_enabled' => 'boolean',
            'carousel_interval' => 'nullable|integer|min:1000|max:30000',
            'carousel_autoplay' => 'boolean',
            'carousel_height' => 'nullable|string|in:h-56,h-64,h-72,h-80,h-96,h-screen',
            'carousel_indicators' => 'boolean',
            'carousel_controls' => 'boolean',
            'carousel_transition' => 'nullable|string|in:fade,slide,zoom',

            // Contact Info
            'contact_address' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',

            // Social Media
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',

            // Scroll Navigation Buttons
            'show_scroll_to_top' => 'boolean',
            'show_scroll_to_bottom' => 'boolean',
            'scroll_button_position' => 'nullable|string|in:bottom-right,bottom-left',
            'scroll_button_size' => 'nullable|string|in:small,medium,large',
            'scroll_animation_type' => 'nullable|string|in:smooth,instant',

            // Statistics
            'stats_members' => 'nullable|integer|min:0',
            'stats_years' => 'nullable|integer|min:0',
            'ministries_count' => 'nullable|integer|min:0',
            'campaigns_count' => 'nullable|integer|min:0',
        ]);

        // Save settings
        $settingsMap = [
            // Hero
            'hero_title' => ['homepage_hero_title', 'string', 'Título do Hero'],
            'hero_subtitle' => ['homepage_hero_subtitle', 'text', 'Subtítulo do Hero'],
            'hero_button_1_text' => ['homepage_hero_button_1_text', 'string', 'Texto do Botão 1'],
            'hero_button_1_link' => ['homepage_hero_button_1_link', 'string', 'Link do Botão 1'],
            'hero_button_2_text' => ['homepage_hero_button_2_text', 'string', 'Texto do Botão 2'],
            'hero_button_2_link' => ['homepage_hero_button_2_link', 'string', 'Link do Botão 2'],

            // About
            'about_title' => ['homepage_about_title', 'string', 'Título da Seção Sobre'],
            'about_description' => ['homepage_about_description', 'text', 'Descrição da Seção Sobre'],

            // Section Visibility
            'show_ministries' => ['homepage_show_ministries', 'boolean', 'Mostrar Ministérios'],
            'show_events' => ['homepage_show_events', 'boolean', 'Mostrar Eventos'],
            'show_campaigns' => ['homepage_show_campaigns', 'boolean', 'Mostrar Campanhas'],
            'show_testimonials' => ['homepage_show_testimonials', 'boolean', 'Mostrar Testemunhos'],
            'show_gallery' => ['homepage_show_gallery', 'boolean', 'Mostrar Galeria'],
            'show_daily_verse' => ['homepage_show_daily_verse', 'boolean', 'Mostrar Versículo do Dia'],
            'show_statistics' => ['homepage_show_statistics', 'boolean', 'Mostrar Estatísticas'],
            'show_newsletter' => ['homepage_show_newsletter', 'boolean', 'Mostrar Newsletter'],

            // Section Titles
            'ministries_title' => ['homepage_ministries_title', 'string', 'Título da Seção Ministérios'],
            'events_title' => ['homepage_events_title', 'string', 'Título da Seção Eventos'],
            'campaigns_title' => ['homepage_campaigns_title', 'string', 'Título da Seção Campanhas'],
            'testimonials_title' => ['homepage_testimonials_title', 'string', 'Título da Seção Testemunhos'],
            'gallery_title' => ['homepage_gallery_title', 'string', 'Título da Seção Galeria'],
            'daily_verse_title' => ['homepage_daily_verse_title', 'string', 'Título da Seção Versículo'],
            'statistics_title' => ['homepage_statistics_title', 'string', 'Título da Seção Estatísticas'],
            'newsletter_title' => ['homepage_newsletter_title', 'string', 'Título da Seção Newsletter'],

            // Carousel
            'carousel_enabled' => ['homepage_carousel_enabled', 'boolean', 'Carousel Habilitado'],
            'carousel_interval' => ['homepage_carousel_interval', 'integer', 'Intervalo do Carousel (ms)'],
            'carousel_autoplay' => ['homepage_carousel_autoplay', 'boolean', 'Autoplay do Carousel'],
            'carousel_height' => ['homepage_carousel_height', 'string', 'Altura do Carousel'],
            'carousel_indicators' => ['homepage_carousel_indicators', 'boolean', 'Indicadores do Carousel'],
            'carousel_controls' => ['homepage_carousel_controls', 'boolean', 'Controles do Carousel'],
            'carousel_transition' => ['homepage_carousel_transition', 'string', 'Transição do Carousel'],

            // Contact Info
            'contact_address' => ['homepage_contact_address', 'string', 'Endereço de Contato'],
            'contact_phone' => ['homepage_contact_phone', 'string', 'Telefone de Contato'],
            'contact_email' => ['homepage_contact_email', 'string', 'E-mail de Contato'],

            // Social Media
            'social_facebook' => ['homepage_social_facebook', 'string', 'Facebook'],
            'social_instagram' => ['homepage_social_instagram', 'string', 'Instagram'],
            'social_youtube' => ['homepage_social_youtube', 'string', 'YouTube'],

            // Scroll Navigation Buttons
            'show_scroll_to_top' => ['homepage_show_scroll_to_top', 'boolean', 'Mostrar Botão Voltar ao Topo'],
            'show_scroll_to_bottom' => ['homepage_show_scroll_to_bottom', 'boolean', 'Mostrar Botão Rolar para Baixo'],
            'scroll_button_position' => ['homepage_scroll_button_position', 'string', 'Posição dos Botões'],
            'scroll_button_size' => ['homepage_scroll_button_size', 'string', 'Tamanho dos Botões'],
            'scroll_animation_type' => ['homepage_scroll_animation_type', 'string', 'Tipo de Animação'],

            // Statistics
            'stats_members' => ['homepage_stats_members', 'integer', 'Número de Membros'],
            'stats_years' => ['homepage_stats_years', 'integer', 'Anos de História'],
            'ministries_count' => ['homepage_ministries_count', 'integer', 'Contagem de Ministérios'],
            'campaigns_count' => ['homepage_campaigns_count', 'integer', 'Contagem de Campanhas'],
        ];

        $settingsToUpdate = [];
        foreach ($settingsMap as $key => $config) {
            $value = $validated[$key] ?? ($config[1] === 'boolean' ? false : '');
            $settingsToUpdate[] = [
                'key' => $config[0],
                'value' => $value,
                'type' => $config[1],
                'group' => 'homepage',
                'description' => $config[2],
            ];
        }
        Settings::setMany($settingsToUpdate);

        Settings::clearCache();

        return redirect()->route('admin.homepage.index')
            ->with('success', 'Configurações da HomePage atualizadas com sucesso!');
    }

    /**
     * Store a new carousel slide.
     */
    public function storeSlide(Request $request)
    {
        $validated = $this->validateSlide($request, true);

        // Process and upload image
        if ($request->hasFile('image')) {
            $validated['image'] = $this->processImage($request->file('image'));
        }

        // Set defaults
        $validated['order'] = $validated['order'] ?? (CarouselSlide::max('order') ?? 0) + 1;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['alt_text'] = $validated['alt_text'] ?? $validated['title'] ?? 'Carousel slide';

        CarouselSlide::create($validated);

        return redirect()->route('admin.homepage.index')
            ->with('success', 'Slide do carousel criado com sucesso!');
    }

    /**
     * Update a carousel slide.
     */
    public function updateSlide(Request $request, CarouselSlide $slide)
    {
        $validated = $this->validateSlide($request, false);

        // Process new image if provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }
            $validated['image'] = $this->processImage($request->file('image'));
        } else {
            unset($validated['image']);
        }

        // Update alt_text if title changed
        if (isset($validated['title']) && empty($validated['alt_text'])) {
            $validated['alt_text'] = $validated['title'];
        }

        $slide->update($validated);

        return redirect()->route('admin.homepage.index')
            ->with('success', 'Slide do carousel atualizado com sucesso!');
    }

    /**
     * Update slide order (for drag & drop)
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

        return response()->json(['success' => true, 'message' => 'Ordem dos slides atualizada com sucesso!']);
    }

    /**
     * Toggle slide active status
     */
    public function toggleActive(CarouselSlide $slide)
    {
        $slide->update(['is_active' => ! $slide->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $slide->is_active,
            'message' => $slide->is_active ? 'Slide ativado com sucesso!' : 'Slide desativado com sucesso!',
        ]);
    }

    /**
     * Delete a carousel slide.
     */
    public function destroySlide(CarouselSlide $slide)
    {
        // Delete image
        if ($slide->image) {
            Storage::disk('public')->delete($slide->image);
        }

        $slide->delete();

        return redirect()->route('admin.homepage.index')
            ->with('success', 'Slide do carousel excluído com sucesso!');
    }

    /**
     * Validate slide data
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
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
            'overlay_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'button_style' => 'nullable|string|in:primary,secondary,outline',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'transition_type' => 'nullable|string|in:fade,slide,zoom',
            'transition_duration' => 'nullable|integer|min:100|max:2000',
            'show_indicators' => 'boolean',
            'show_controls' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ];

        if ($requireImage) {
            $rules['image'] = 'required|image|mimes:jpeg,jpg,png,webp|max:10240|dimensions:min_width=800,min_height=400';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240|dimensions:min_width=800,min_height=400';
        }

        return $request->validate($rules);
    }

    /**
     * Process and save image
     */
    private function processImage($file)
    {
        // Determine extension and encoding
        $originalExtension = strtolower($file->getClientOriginalExtension());
        $extension = 'jpg';

        if ($originalExtension === 'png') {
            $extension = 'png';
        } elseif ($originalExtension === 'webp') {
            $extension = 'webp';
        }

        // Generate unique filename with CORRECT extension
        $filename = 'carousel_'.Str::random(20).'_'.time().'.'.$extension;
        $path = 'homepage/carousel/'.$filename;

        // Create directory if it doesn't exist
        Storage::disk('public')->makeDirectory('homepage/carousel');

        // Process image: Resize and Encode
        $image = Image::read($file);

        // Resize if width is larger than 1920px (Full HD)
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }

        // Encode based on target extension
        if ($extension === 'png') {
            $encoded = $image->toPng();
        } elseif ($extension === 'webp') {
            $encoded = $image->toWebp(quality: 80);
        } else {
            $encoded = $image->toJpeg(quality: 80);
        }

        // Store the optimized file
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
