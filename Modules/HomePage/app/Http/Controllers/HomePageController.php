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
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\Treasury\App\Models\Campaign;

class HomePageController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        // Carousel
        $carouselEnabled = Settings::get('homepage_carousel_enabled', false);
        $carouselSlides = collect();
        if ($carouselEnabled && Schema::hasTable('carousel_slides')) {
            $carouselSlides = CarouselSlide::currentlyActive()->ordered()->get();
        }

        // Campanhas ativas
        $activeCampaigns = collect();
        try {
            if (class_exists('Modules\Treasury\App\Models\Campaign')) {
                $activeCampaigns = \Modules\Treasury\App\Models\Campaign::active()
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(6)
                    ->get();
            }
        } catch (\Exception $e) {
            // Se o módulo não estiver disponível, usar array vazio
            \Log::warning('Módulo Treasury não disponível: '.$e->getMessage());
        }

        // Gateways de pagamento
        $hasActiveGateways = PaymentGateway::active()
            ->get()
            ->filter(function ($gateway) {
                return $gateway->isConfigured();
            })
            ->isNotEmpty();

        // Ministérios ativos
        $activeMinistries = collect();
        try {
            if (class_exists('Modules\Ministries\App\Models\Ministry')) {
                $activeMinistries = \Modules\Ministries\App\Models\Ministry::where('is_active', true)
                    ->orderBy('name')
                    ->get();
            }
        } catch (\Exception $e) {
            // Se o módulo não estiver disponível, usar array vazio
            \Log::warning('Módulo Ministries não disponível: '.$e->getMessage());
        }

        // Versículo do dia
        $dailyVerse = $this->getDailyVerse();

        // Próximos eventos (visíveis ao público: public ou both; destacados primeiro)
        $upcomingEvents = Event::upcoming()
            ->public()
            ->orderBy('is_featured', 'desc')
            ->orderBy('start_date')
            ->limit(6)
            ->get();

        // Testemunhos ativos (fallback seguro quando a tabela não existe)
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

        // Notificações importantes: só globais para visitantes; globais + do usuário para logados
        $importantNotifications = $this->getImportantNotificationsForHomepage();

        // Estatísticas dinâmicas
        $statistics = $this->getDynamicStatistics();

        // Produtos em destaque (Loja Missionária)
        $featuredProducts = collect();
        if (class_exists(\Modules\Marketplace\Models\Product::class)) {
            try {
                $featuredProducts = \Illuminate\Support\Facades\Cache::remember(
                    'marketplace_featured_products',
                    now()->addMinutes(30),
                    static function () {
                        return \Modules\Marketplace\Models\Product::active()
                            ->inStock()
                            ->whereHas('campaign')
                            ->with(['images' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'campaign'])
                            ->orderBy('sort_order')
                            ->orderBy('title')
                            ->limit(6)
                            ->get();
                    }
                );
            } catch (\Throwable $e) {
                \Log::debug('HomePage featured products: ' . $e->getMessage());
            }
        }

        // Configurações da homepage
        $homepageSettings = $this->getHomepageSettings();

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
            'featuredProducts'
        ))->with([
            'title' => $homepageSettings['meta_title'],
            'description' => $homepageSettings['meta_description'],
            'keywords' => $homepageSettings['meta_keywords'],
        ]);
    }

    /**
     * Página dedicada da Rádio Rede 3.16. O usuário pode abrir em nova aba
     * e manter a reprodução contínua enquanto navega no resto do site.
     */
    public function radio()
    {
        $homepageSettings = $this->getHomepageSettings();
        $embedUrl = $homepageSettings['radio_embed_url'] ?? '';
        $showRadio = (bool) ($homepageSettings['show_radio'] ?? false);

        $embedUrlForPage = '';
        if (! empty($embedUrl)) {
            $embedUrlForPage = str_contains($embedUrl, 'auto_play=0')
                ? str_replace('auto_play=0', 'auto_play=1', $embedUrl)
                : $embedUrl.(str_contains($embedUrl, '?') ? '&' : '?').'auto_play=1';
        }

        $randomVerse = null;
        try {
            if (class_exists(\Modules\Bible\App\Services\BibleApiService::class)) {
                $bibleApi = app(\Modules\Bible\App\Services\BibleApiService::class);
                $verse = $bibleApi->getRandomVerse(null);
                if ($verse && $verse->chapter && $verse->chapter->book) {
                    $randomVerse = [
                        'text' => $verse->text,
                        'reference' => $verse->chapter->book->name.' '.$verse->chapter->chapter_number.':'.$verse->verse_number,
                    ];
                }
            }
        } catch (\Throwable $e) {
            \Log::debug('Radio page: random verse not available - '.$e->getMessage());
        }

        return view('homepage::public.radio', compact('embedUrlForPage', 'homepageSettings', 'showRadio', 'randomVerse'))
            ->with([
                'title' => 'Rádio Rede 3.16 - Ao vivo',
                'description' => 'Ouça a Rádio Rede 3.16 ao vivo. 24 horas compartilhando o amor de Deus.',
                'keywords' => 'rádio 3.16, rádio cristã, ao vivo, rede 316',
            ]);
    }

    /**
     * Notificações para a barra da homepage: visitantes só veem globais;
     * usuários logados veem globais + as direcionadas a eles (ex.: nível EBD).
     */
    private function getImportantNotificationsForHomepage()
    {
        $query = SystemNotification::active()
            ->orderBy('created_at', 'desc')
            ->limit(20);

        $notifications = $query->get()->filter(function ($notification) {
            if ($notification->isGlobal()) {
                return true;
            }
            if (! auth()->check()) {
                return false;
            }

            return $notification->shouldNotifyUser(auth()->user());
        })->take(3)->values();

        return $notifications;
    }

    /**
     * Get daily verse
     */
    private function getDailyVerse()
    {
        // Tentar pegar versículo configurado para hoje
        $todayVerse = Settings::get('homepage_daily_verse_'.date('Y-m-d'));

        if ($todayVerse) {
            return json_decode($todayVerse, true);
        }

        // Buscar um versículo aleatório se não houver configurado
        $bibleVersion = BibleVersion::where('is_default', true)->first();
        if (! $bibleVersion) {
            $bibleVersion = BibleVersion::first();
        }

        if ($bibleVersion) {
            // Buscar versículos através das relações: BibleVersion -> Books -> Chapters -> Verses
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
                    'book' => $verse->chapter->book->name,
                    'chapter' => $verse->chapter->chapter_number,
                    'verse' => $verse->verse_number,
                    'book_id' => $verse->chapter->book->id,
                ];
            }
        }

        return null;
    }

    /**
     * Get dynamic statistics
     */
    private function getDynamicStatistics()
    {
        // Debug: verificar se as tabelas existem e têm dados
        $membersCount = Settings::get('homepage_stats_members', 150);
        $ministriesCount = 0;
        $campaignsCount = 0;
        $yearsCount = Settings::get('homepage_stats_years', 50);

        // Tentar contar ministérios de forma segura
        try {
            if (class_exists('Modules\Ministries\App\Models\Ministry')) {
                $ministriesCount = \Modules\Ministries\App\Models\Ministry::where('is_active', true)->count();
            }
        } catch (\Exception $e) {
            // Se não conseguir contar, usar valor padrão
            $ministriesCount = Settings::get('homepage_ministries_count', 4);
        }

        // Tentar contar campanhas de forma segura
        try {
            if (class_exists('Modules\Treasury\App\Models\Campaign')) {
                $campaignsCount = \Modules\Treasury\App\Models\Campaign::active()->where('is_active', true)->count();
            }
        } catch (\Exception $e) {
            // Se não conseguir contar, usar valor padrão
            $campaignsCount = Settings::get('homepage_campaigns_count', 4);
        }

        return [
            'members' => $membersCount,
            'ministries' => $ministriesCount,
            'campaigns' => $campaignsCount,
            'years' => $yearsCount,
        ];
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
            'hero_bg_image' => Settings::get('homepage_hero_bg_image', ''),

            // About Section
            'about_title' => Settings::get('homepage_about_title', 'Sobre Nossa Igreja'),
            'about_description' => $this->normalizeAboutDescription(Settings::get('homepage_about_description', '')),

            // Section Visibility
            'show_ministries' => Settings::get('homepage_show_ministries', true),
            'show_events' => Settings::get('homepage_show_events', true),
            'show_campaigns' => Settings::get('homepage_show_campaigns', true),
            'show_testimonials' => Settings::get('homepage_show_testimonials', true),
            'show_gallery' => Settings::get('homepage_show_gallery', true),
            'show_daily_verse' => Settings::get('homepage_show_daily_verse', true),
            'show_statistics' => Settings::get('homepage_show_statistics', true),
            'show_newsletter' => Settings::get('homepage_show_newsletter', true),
            'show_radio' => Settings::get('homepage_show_radio', true),
            'show_marketplace' => Settings::get('homepage_show_marketplace', false),

            // Section Titles
            'ministries_title' => Settings::get('homepage_ministries_title', 'Nossos Ministérios'),
            'events_title' => Settings::get('homepage_events_title', 'Próximos Eventos'),
            'campaigns_title' => Settings::get('homepage_campaigns_title', 'Campanhas em Andamento'),
            'testimonials_title' => Settings::get('homepage_testimonials_title', 'Testemunhos'),
            'gallery_title' => Settings::get('homepage_gallery_title', 'Galeria de Fotos'),
            'daily_verse_title' => Settings::get('homepage_daily_verse_title', 'Versículo do Dia'),
            'statistics_title' => Settings::get('homepage_statistics_title', 'Nossa Comunidade'),
            'newsletter_title' => Settings::get('homepage_newsletter_title', 'Mantenha-se Conectado'),
            'marketplace_title' => Settings::get('homepage_marketplace_title', 'Loja Missionária'),

            // Carousel Settings
            'carousel_interval' => Settings::get('homepage_carousel_interval', 5000),
            'carousel_autoplay' => Settings::get('homepage_carousel_autoplay', true),
            'carousel_height' => Settings::get('homepage_carousel_height', 'h-96'),
            'carousel_indicators' => Settings::get('homepage_carousel_indicators', true),
            'carousel_controls' => Settings::get('homepage_carousel_controls', true),
            'carousel_transition' => Settings::get('homepage_carousel_transition', 'fade'),

            // Contact Info (Sourced from Global Site Settings)
            'contact_address' => Settings::get('site_address', 'Avenida, Coração de Maria - BA'),
            'contact_phone' => Settings::get('site_phone', '(75) 0000-0000'),
            'contact_email' => Settings::get('site_email', 'contato@igrejabatistaavenida.com.br'),

            // Social Media
            'social_facebook' => Settings::get('homepage_social_facebook', ''),
            'social_instagram' => Settings::get('homepage_social_instagram', ''),
            'social_youtube' => Settings::get('homepage_social_youtube', ''),

            // Rádio 3:16
            'radio_embed_url' => Settings::get(
                'homepage_radio_embed_url',
                'https://public-player-widget.webradiosite.com/?source=widget_embeded&locale=pt-br&info=https%3A%2F%2Fpublic-player-widget.webradiosite.com%2Fapp%2Fplayer%2Finfo%2F171445%3Fhash%3Df5672a87e8d4d6364055e1b586ab6ae9f84f43c6&theme=light&color=3&cover=0&current_track=0&schedules=1&link=0&share=1&popup=0&embed=1&auto_play=0'
            ),

            // Scroll Navigation Buttons
            'show_scroll_to_top' => Settings::get('homepage_show_scroll_to_top', true),
            'show_scroll_to_bottom' => Settings::get('homepage_show_scroll_to_bottom', true),
            'scroll_button_position' => Settings::get('homepage_scroll_button_position', 'bottom-right'),
            'scroll_button_size' => Settings::get('homepage_scroll_button_size', 'medium'),
            'scroll_animation_type' => Settings::get('homepage_scroll_animation_type', 'smooth'),

            // SEO
            'meta_title' => Settings::get('homepage_meta_title', 'Igreja Batista Avenida - Coração de Maria - BA'),
            'meta_description' => Settings::get('homepage_meta_description', 'Uma comunidade de fé comprometida com o Evangelho e o serviço ao próximo.'),
            'meta_keywords' => Settings::get('homepage_meta_keywords', 'igreja batista, coração de maria, bahia, cristianismo, evangelho'),
        ];
    }

    /**
     * Evita exibir texto de placeholder na descrição da seção Sobre.
     */
    private function normalizeAboutDescription(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '' || strcasecmp($trimmed, 'Teste de texto') === 0) {
            return '';
        }
        return $value;
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $email = $request->email;

        // Verificar se já está inscrito
        if (NewsletterSubscriber::isEmailSubscribed($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Este e-mail já está inscrito em nossa newsletter.',
            ]);
        }

        // Criar nova inscrição
        $subscriber = NewsletterSubscriber::create([
            'email' => $email,
            'name' => $request->name,
            'is_active' => true,
            'subscribed_at' => now(),
            'confirmation_token' => NewsletterSubscriber::generateConfirmationToken(),
            'is_confirmed' => true, // Para simplificar, confirmar automaticamente
        ]);

        // Enviar e-mail de confirmação
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\NewsletterSubscriptionMail($subscriber));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar e-mail de newsletter: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Inscrição realizada com sucesso! Você receberá nossas novidades em breve.',
        ]);
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $subscriber = NewsletterSubscriber::where('email', $request->email)
            ->where('confirmation_token', $request->token)
            ->first();

        if (! $subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Link de cancelamento inválido.',
            ]);
        }

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscrição cancelada com sucesso.',
        ]);
    }

    /**
     * Handle contact form submission
     */
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

            return response()->json([
                'success' => true,
                'message' => 'Sua mensagem foi enviada com sucesso! Em breve entraremos em contato.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar contato: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao enviar sua mensagem. Tente novamente mais tarde.',
            ], 500);
        }
    }
}
