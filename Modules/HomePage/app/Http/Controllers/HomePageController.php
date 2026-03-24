<?php

namespace Modules\HomePage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Verse;
use Modules\Events\App\Models\Event;
use Modules\HomePage\App\Models\CarouselSlide;
use Modules\HomePage\App\Models\GalleryImage;
use Modules\HomePage\App\Models\NewsletterSubscriber;
use Modules\HomePage\App\Models\Testimonial;
use Modules\Notifications\App\Models\SystemNotification;

class HomePageController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        // Carousel
        $carouselEnabled = Settings::get('homepage_carousel_enabled', true);
        $carouselSlides = collect();
        if ($carouselEnabled && Schema::hasTable('carousel_slides')) {
            $carouselSlides = CarouselSlide::currentlyActive()->ordered()->get();
        }

        // Próximos eventos JUBAF
        $upcomingEvents = collect();
        if (class_exists('Modules\Events\App\Models\Event') && Schema::hasTable('events')) {
            $upcomingEvents = \Modules\Events\App\Models\Event::where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('is_featured', 'desc')
                ->orderBy('start_date', 'asc')
                ->limit(6)
                ->get();
        }

        // Últimas Notícias (Mural JUBAF)
        $ultimasNoticias = collect();
        if (class_exists('Modules\Comunicacao\App\Models\Postagem') && Schema::hasTable('postagens')) {
            $ultimasNoticias = \Modules\Comunicacao\App\Models\Postagem::orderBy('created_at', 'desc')->limit(3)->get();
        }

        // Campanhas ativas (se aplicável à JUBAF)
        $activeCampaigns = collect();
        try {
            if (class_exists('Modules\Treasury\App\Models\Campaign')) {
                $activeCampaigns = \Modules\Treasury\App\Models\Campaign::active()
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(6)
                    ->get();
            }
        } catch (\Exception $e) {}

        // Gateways de pagamento (recurso não vital para vitrine)
        $hasActiveGateways = false;

        // Ministérios ativos (Redes da JUBAF)
        $activeMinistries = collect();

        // Versículo do dia (se Bible instalado)
        $dailyVerse = $this->getDailyVerse();

        // Testemunhos
        $activeTestimonials = collect();
        if (Schema::hasTable('testimonials')) {
            $activeTestimonials = Testimonial::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        // Galeria de fotos
        $galleryImages = collect();
        if (Schema::hasTable('gallery_images')) {
            $galleryImages = GalleryImage::where('is_active', true)
                ->orderBy('order')
                ->limit(8)
                ->get();
        }

        // Notificações importantes
        $importantNotifications = $this->getImportantNotificationsForHomepage();

        // Estatísticas da JUBAF
        $statistics = $this->getDynamicStatistics();
        
        // Produtos em destaque
        $featuredProducts = collect();

        // Configurações
        $homepageSettings = $this->getHomepageSettings();

        // Retorna a nova view que mescla dinâmico + design JUBAF
        return view('homepage::index', compact(
            'carouselEnabled',
            'carouselSlides',
            'homepageSettings',
            'activeCampaigns',
            'hasActiveGateways',
            'activeMinistries',
            'dailyVerse',
            'upcomingEvents',
            'activeTestimonials',
            'galleryImages',
            'importantNotifications',
            'statistics',
            'featuredProducts',
            'ultimasNoticias'
        ))->with([
            'title' => $homepageSettings['meta_title'],
            'description' => $homepageSettings['meta_description'],
            'keywords' => $homepageSettings['meta_keywords'],
        ]);
    }

    private function getImportantNotificationsForHomepage()
    {
        if (!class_exists('Modules\Notifications\App\Models\SystemNotification')) return collect();

        $query = SystemNotification::active()
            ->orderBy('created_at', 'desc')
            ->limit(20);

        return $query->get()->filter(function ($notification) {
            if ($notification->isGlobal()) return true;
            if (! auth()->check()) return false;
            return $notification->shouldNotifyUser(auth()->user());
        })->take(3)->values();
    }

    private function getDailyVerse()
    {
        if (!class_exists('Modules\Bible\App\Models\BibleVersion')) return null;

        $todayVerse = Settings::get('homepage_daily_verse_'.date('Y-m-d'));
        if ($todayVerse) {
            return json_decode($todayVerse, true);
        }

        try {
            $bibleVersion = BibleVersion::where('is_default', true)->first() ?? BibleVersion::first();
            if ($bibleVersion) {
                $verse = Verse::whereHas('chapter.book', function ($query) use ($bibleVersion) {
                    $query->where('bible_version_id', $bibleVersion->id);
                })
                ->with(['chapter.book'])
                ->inRandomOrder()
                ->first();

                if ($verse) {
                    return [
                        'text' => $verse->text,
                        'reference' => $verse->chapter->book->name.' '.$verse->chapter->chapter_number.':'.$verse->verse_number,
                        'book_id' => $verse->chapter->book->id,
                        'chapter' => $verse->chapter->chapter_number,
                        'verse' => $verse->verse_number,
                    ];
                }
            }
        } catch (\Exception $e) {}

        return null;
    }

    private function getDynamicStatistics()
    {
        // Adaptado para mostrar número de Igrejas
        $igrejasCount = 0;
        try {
            if (class_exists('Modules\Igrejas\App\Models\Igreja')) {
                $igrejasCount = \Modules\Igrejas\App\Models\Igreja::count();
            }
        } catch (\Exception $e) {
            $igrejasCount = 15;
        }

        // Jovens filiados
        $membrosCount = 0;
        try {
            if (class_exists('App\Models\User')) {
                $membrosCount = \App\Models\User::count();
            }
        } catch (\Exception $e) {
            $membrosCount = 350;
        }

        return [
            'members' => max($membrosCount, 150),
            'ministries' => max($igrejasCount, 5),
            'campaigns' => 12,
            'years' => date('Y') - 1980,
        ];
    }

    private function getHomepageSettings()
    {
        return [
            // Hero Section (Adaptado para JUBAF)
            'hero_title' => Settings::get('homepage_hero_title', 'Nova JUBAF'),
            'hero_subtitle' => Settings::get('homepage_hero_subtitle', 'Um hub centralizado onde as igrejas locais se conectam e fortalecem o corpo de Cristo.'),
            'hero_button_1_text' => Settings::get('homepage_hero_button_1_text', 'Conheça a Associação'),
            'hero_button_1_link' => Settings::get('homepage_hero_button_1_link', '#sobre'),
            'hero_button_2_text' => Settings::get('homepage_hero_button_2_text', 'Próximos Eventos'),
            'hero_button_2_link' => Settings::get('homepage_hero_button_2_link', '#eventos'),
            'hero_bg_image' => Settings::get('homepage_hero_bg_image', ''),

            // About Section
            'about_title' => Settings::get('homepage_about_title', 'Sobre a JUBAF'),
            'about_description' => $this->normalizeAboutDescription(Settings::get('homepage_about_description', 'A Juventude Batista Feirense existe para integrar, cooperar e promover a expansão do Evangelho entre os jovens.')),

            // Section Visibility overrides for JUBAF
            'show_ministries' => false,
            'show_marketplace' => false,
            'show_radio' => false,
            'show_campaigns' => Settings::get('homepage_show_campaigns', true),
            'show_events' => Settings::get('homepage_show_events', true),
            'show_testimonials' => Settings::get('homepage_show_testimonials', true),
            'show_gallery' => Settings::get('homepage_show_gallery', true),
            'show_daily_verse' => Settings::get('homepage_show_daily_verse', true),
            'show_statistics' => Settings::get('homepage_show_statistics', true),
            'show_newsletter' => Settings::get('homepage_show_newsletter', true),

            // Section Titles
            'events_title' => Settings::get('homepage_events_title', 'Agenda de Eventos da Associação'),
            'campaigns_title' => Settings::get('homepage_campaigns_title', 'Campanhas de Apoio'),
            'testimonials_title' => Settings::get('homepage_testimonials_title', 'Testemunhos'),
            'gallery_title' => Settings::get('homepage_gallery_title', 'Galeria de Fotos'),
            'daily_verse_title' => Settings::get('homepage_daily_verse_title', 'Verdade Bíblica'),
            'statistics_title' => Settings::get('homepage_statistics_title', 'Nossa Força'),
            'newsletter_title' => Settings::get('homepage_newsletter_title', 'Mantenha-se Conectado'),

            // Carousel Settings
            'carousel_interval' => Settings::get('homepage_carousel_interval', 5000),
            'carousel_autoplay' => Settings::get('homepage_carousel_autoplay', true),
            'carousel_height' => Settings::get('homepage_carousel_height', 'h-96 md:h-[600px]'),
            'carousel_indicators' => Settings::get('homepage_carousel_indicators', true),
            'carousel_controls' => Settings::get('homepage_carousel_controls', true),
            'carousel_transition' => Settings::get('homepage_carousel_transition', 'slide'),

            // Contact Info
            'contact_address' => Settings::get('site_address', 'Feira de Santana - BA'),
            'contact_phone' => Settings::get('site_phone', '(75) 90000-0000'),
            'contact_email' => Settings::get('site_email', 'contato@jubaf.com.br'),

            // Social
            'social_facebook' => Settings::get('homepage_social_facebook', ''),
            'social_instagram' => Settings::get('homepage_social_instagram', ''),
            'social_youtube' => Settings::get('homepage_social_youtube', ''),

            // Scroll Options
            'show_scroll_to_top' => Settings::get('homepage_show_scroll_to_top', true),
            'show_scroll_to_bottom' => Settings::get('homepage_show_scroll_to_bottom', true),

            // SEO
            'meta_title' => Settings::get('homepage_meta_title', 'JUBAF - Juventude Batista Feirense'),
            'meta_description' => Settings::get('homepage_meta_description', 'Portal da Associação da Juventude Batista Feirense.'),
            'meta_keywords' => Settings::get('homepage_meta_keywords', 'jubaf, jovens batistas, feira de santana, bahia'),
        ];
    }

    private function normalizeAboutDescription(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '' || strcasecmp($trimmed, 'Teste de texto') === 0) {
            return '';
        }
        return $value;
    }

    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        if (NewsletterSubscriber::isEmailSubscribed($request->email)) {
            return response()->json(['success' => false, 'message' => 'Este e-mail já está inscrito.']);
        }

        NewsletterSubscriber::create([
            'email' => $request->email,
            'name' => $request->name,
            'is_active' => true,
            'subscribed_at' => now(),
            'confirmation_token' => NewsletterSubscriber::generateConfirmationToken(),
            'is_confirmed' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Inscrição realizada!']);
    }

    public function unsubscribeNewsletter(Request $request) { abort(404); }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:5000',
        ]);

        try {
            \Modules\HomePage\App\Models\ContactMessage::create($request->all());
            return response()->json(['success' => true, 'message' => 'Sua mensagem foi enviada!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ocorreu um erro.'], 500);
        }
    }

    public function radio()
    {
        $showRadio = \App\Models\Settings::get('homepage_show_radio', true);
        
        // Se a rádio houver uma URL na base, usa ela. Senão fornece uma de fallback/exemplo.
        $embedUrlForPage = \App\Models\Settings::get('homepage_radio_url', 'https://fastcast4u.com/player/rede316/');
        
        $randomVerse = $this->getDailyVerse();

        return view('homepage::public.radio', compact('showRadio', 'embedUrlForPage', 'randomVerse'));
    }
}
