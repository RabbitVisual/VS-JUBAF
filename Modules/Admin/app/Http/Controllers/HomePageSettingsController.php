<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class HomePageSettingsController extends Controller
{
    /**
     * Display the homepage settings page.
     */
    public function index()
    {
        $homepageSettings = $this->getHomepageSettings();

        return view('homepage::admin.homepage.settings', compact('homepageSettings'));
    }

    /**
     * Update homepage settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Hero
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_button_1_text' => 'nullable|string|max:100',
            'hero_button_1_link' => 'nullable|string|max:255',
            'hero_button_2_text' => 'nullable|string|max:100',
            'hero_button_2_link' => 'nullable|string|max:255',
            'hero_bg_image' => 'nullable|image|max:5120',

            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',

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
            'show_radio' => 'boolean',
            'show_marketplace' => 'boolean',
            'marketplace_title' => 'nullable|string|max:255',
            'marketplace_maintenance' => 'boolean',
            'marketplace_showcase_only' => 'boolean',
            'marketplace_policy_delivery' => 'nullable|string',
            'marketplace_policy_returns' => 'nullable|string',
            'marketplace_policy_terms' => 'nullable|string',

            // Section Titles
            'ministries_title' => 'nullable|string|max:255',
            'events_title' => 'nullable|string|max:255',
            'campaigns_title' => 'nullable|string|max:255',
            'testimonials_title' => 'nullable|string|max:255',
            'gallery_title' => 'nullable|string|max:255',
            'daily_verse_title' => 'nullable|string|max:255',
            'statistics_title' => 'nullable|string|max:255',
            'newsletter_title' => 'nullable|string|max:255',

            // Carousel Settings (Global)
            'carousel_enabled' => 'boolean',
            'carousel_interval' => 'nullable|integer|min:1000|max:30000',
            'carousel_autoplay' => 'boolean',
            'carousel_height' => 'nullable|string|in:h-56,h-64,h-72,h-80,h-96,h-screen',
            'carousel_indicators' => 'boolean',
            'carousel_controls' => 'boolean',
            'carousel_transition' => 'nullable|string|in:fade,slide,zoom',

            // Social Media
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'radio_embed_url' => 'nullable|url|max:1024',

            // Scroll Navigation Buttons
            'show_scroll_to_top' => 'boolean',
            'show_scroll_to_bottom' => 'boolean',
            'scroll_button_position' => 'nullable|string|in:bottom-right,bottom-left',
            'scroll_button_size' => 'nullable|string|in:small,medium,large',
            'scroll_animation_type' => 'nullable|string|in:smooth,instant',
            'scroll_down_position' => 'nullable|string|in:center,left,right',
            'scroll_down_size' => 'nullable|string|in:small,medium,large',

            // Statistics
            'stats_members' => 'nullable|integer|min:0',
            'stats_years' => 'nullable|integer|min:0',
            'ministries_count' => 'nullable|integer|min:0',
            'campaigns_count' => 'nullable|integer|min:0',
        ]);

        // Save settings map
        $settingsMap = [
            'hero_title' => ['homepage_hero_title', 'string', 'Título do Hero'],
            'hero_subtitle' => ['homepage_hero_subtitle', 'text', 'Subtítulo do Hero'],
            'hero_button_1_text' => ['homepage_hero_button_1_text', 'string', 'Texto do Botão 1'],
            'hero_button_1_link' => ['homepage_hero_button_1_link', 'string', 'Link do Botão 1'],
            'hero_button_2_text' => ['homepage_hero_button_2_text', 'string', 'Texto do Botão 2'],
            'hero_button_2_link' => ['homepage_hero_button_2_link', 'string', 'Link do Botão 2'],

            // SEO
            'meta_title' => ['homepage_meta_title', 'string', 'Meta Title'],
            'meta_description' => ['homepage_meta_description', 'string', 'Meta Description'],
            'meta_keywords' => ['homepage_meta_keywords', 'string', 'Meta Keywords'],

            'about_title' => ['homepage_about_title', 'string', 'Título da Seção Sobre'],
            'about_description' => ['homepage_about_description', 'text', 'Descrição da Seção Sobre'],

            'show_ministries' => ['homepage_show_ministries', 'boolean', 'Mostrar Ministérios'],
            'show_events' => ['homepage_show_events', 'boolean', 'Mostrar Eventos'],
            'show_campaigns' => ['homepage_show_campaigns', 'boolean', 'Mostrar Campanhas'],
            'show_testimonials' => ['homepage_show_testimonials', 'boolean', 'Mostrar Testemunhos'],
            'show_gallery' => ['homepage_show_gallery', 'boolean', 'Mostrar Galeria'],
            'show_daily_verse' => ['homepage_show_daily_verse', 'boolean', 'Mostrar Versículo do Dia'],
            'show_statistics' => ['homepage_show_statistics', 'boolean', 'Mostrar Estatísticas'],
            'show_newsletter' => ['homepage_show_newsletter', 'boolean', 'Mostrar Newsletter'],
            'show_radio' => ['homepage_show_radio', 'boolean', 'Mostrar Rádio 3:16'],
            'show_marketplace' => ['homepage_show_marketplace', 'boolean', 'Loja Missionária disponível'],
            'marketplace_title' => ['homepage_marketplace_title', 'string', 'Título da Loja na Home'],
            'marketplace_maintenance' => ['marketplace_maintenance', 'boolean', 'Modo Manutenção (loja fechada)'],
            'marketplace_showcase_only' => ['marketplace_showcase_only', 'boolean', 'Modo Vitrine (só visualização, sem compra)'],
            'marketplace_policy_delivery' => ['marketplace_policy_delivery', 'string', 'Política de Entrega e Prazos'],
            'marketplace_policy_returns' => ['marketplace_policy_returns', 'string', 'Política de Trocas e Devoluções'],
            'marketplace_policy_terms' => ['marketplace_policy_terms', 'string', 'Termos de Uso e Privacidade da Loja'],

            'ministries_title' => ['homepage_ministries_title', 'string', 'Título da Seção Ministérios'],
            'events_title' => ['homepage_events_title', 'string', 'Título da Seção Eventos'],
            'campaigns_title' => ['homepage_campaigns_title', 'string', 'Título da Seção Campanhas'],
            'testimonials_title' => ['homepage_testimonials_title', 'string', 'Título da Seção Testemunhos'],
            'gallery_title' => ['homepage_gallery_title', 'string', 'Título da Seção Galeria'],
            'daily_verse_title' => ['homepage_daily_verse_title', 'string', 'Título da Seção Versículo'],
            'statistics_title' => ['homepage_statistics_title', 'string', 'Título da Seção Estatísticas'],
            'newsletter_title' => ['homepage_newsletter_title', 'string', 'Título da Seção Newsletter'],

            'carousel_enabled' => ['homepage_carousel_enabled', 'boolean', 'Carousel Habilitado'],
            'carousel_interval' => ['homepage_carousel_interval', 'integer', 'Intervalo do Carousel'],
            'carousel_autoplay' => ['homepage_carousel_autoplay', 'boolean', 'Autoplay do Carousel'],
            'carousel_height' => ['homepage_carousel_height', 'string', 'Altura do Carousel'],
            'carousel_indicators' => ['homepage_carousel_indicators', 'boolean', 'Indicadores do Carousel'],
            'carousel_controls' => ['homepage_carousel_controls', 'boolean', 'Controles do Carousel'],
            'carousel_transition' => ['homepage_carousel_transition', 'string', 'Transição do Carousel'],

            'social_facebook' => ['homepage_social_facebook', 'string', 'Facebook'],
            'social_instagram' => ['homepage_social_instagram', 'string', 'Instagram'],
            'social_youtube' => ['homepage_social_youtube', 'string', 'YouTube'],
            'radio_embed_url' => ['homepage_radio_embed_url', 'string', 'URL do Player Rádio 3:16'],

            'show_scroll_to_top' => ['homepage_show_scroll_to_top', 'boolean', 'Mostrar Botão Voltar ao Topo'],
            'show_scroll_to_bottom' => ['homepage_show_scroll_to_bottom', 'boolean', 'Mostrar Botão Rolar para Baixo'],
            'scroll_button_position' => ['homepage_scroll_button_position', 'string', 'Posição dos Botões'],
            'scroll_button_size' => ['homepage_scroll_button_size', 'string', 'Tamanho dos Botões'],
            'scroll_animation_type' => ['homepage_scroll_animation_type', 'string', 'Tipo de Animação'],
            'scroll_down_position' => ['homepage_scroll_down_position', 'string', 'Posição do Botão Rolar para Baixo'],
            'scroll_down_size' => ['homepage_scroll_down_size', 'string', 'Tamanho do Botão Rolar para Baixo'],

            'stats_members' => ['homepage_stats_members', 'integer', 'Número de Membros'],
            'stats_years' => ['homepage_stats_years', 'integer', 'Anos de História'],
            'ministries_count' => ['homepage_ministries_count', 'integer', 'Contagem de Ministérios'],
            'campaigns_count' => ['homepage_campaigns_count', 'integer', 'Contagem de Campanhas'],
        ];

        // Handle File Removal or Upload
        if ($request->boolean('remove_hero_bg_image')) {
            $oldImage = Settings::get('homepage_hero_bg_image');
            if ($oldImage) {
                $storagePath = str_replace('storage/', '', $oldImage);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
                }
                Settings::set('homepage_hero_bg_image', '', 'string', 'homepage', 'Imagem de Fundo do Hero');
            }
        } elseif ($request->hasFile('hero_bg_image')) {
            $path = $request->file('hero_bg_image')->store('homepage', 'public');
            Settings::set('homepage_hero_bg_image', 'storage/'.$path, 'string', 'homepage', 'Imagem de Fundo do Hero');
        }

        foreach ($settingsMap as $key => $config) {
            $value = $validated[$key] ?? ($config[1] === 'boolean' ? false : '');
            Settings::set($config[0], $value, $config[1], 'homepage', $config[2]);
        }

        Settings::clearCache();
        $marketplacePolicyService = \Modules\Marketplace\Services\MarketplacePolicyService::class;
        if (class_exists($marketplacePolicyService)) {
            \Illuminate\Support\Facades\Cache::forget($marketplacePolicyService::CACHE_KEY);
        }

        return redirect()->route('admin.homepage.settings.index', ['tab' => $request->input('active_tab', 'geral')])
            ->with('success', 'Configurações da HomePage atualizadas com sucesso!');
    }

    private function getHomepageSettings()
    {
        return [
            'hero_title' => Settings::get('homepage_hero_title', 'Bem-vindo à Igreja Batista Avenida'),
            'hero_subtitle' => Settings::get('homepage_hero_subtitle', 'Uma comunidade de fé comprometida com o Evangelho...'),
            'hero_button_1_text' => Settings::get('homepage_hero_button_1_text', 'Conheça Nossa História'),
            'hero_button_1_link' => Settings::get('homepage_hero_button_1_link', '#sobre'),
            'hero_button_2_text' => Settings::get('homepage_hero_button_2_text', 'Próximos Eventos'),
            'hero_button_2_link' => Settings::get('homepage_hero_button_2_link', '#eventos'),
            'hero_bg_image' => Settings::get('homepage_hero_bg_image', ''),

            // SEO
            'meta_title' => Settings::get('homepage_meta_title', config('app.name')),
            'meta_description' => Settings::get('homepage_meta_description', ''),
            'meta_keywords' => Settings::get('homepage_meta_keywords', ''),

            'about_title' => Settings::get('homepage_about_title', 'Sobre Nossa Igreja'),
            'about_description' => Settings::get('homepage_about_description', ''),

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
            'marketplace_title' => Settings::get('homepage_marketplace_title', 'Loja Missionária'),
            'marketplace_maintenance' => Settings::get('marketplace_maintenance', false),
            'marketplace_showcase_only' => Settings::get('marketplace_showcase_only', false),
            'marketplace_policy_delivery' => Settings::get('marketplace_policy_delivery', ''),
            'marketplace_policy_returns' => Settings::get('marketplace_policy_returns', ''),
            'marketplace_policy_terms' => Settings::get('marketplace_policy_terms', ''),

            'ministries_title' => Settings::get('homepage_ministries_title', 'Nossos Ministérios'),
            'events_title' => Settings::get('homepage_events_title', 'Próximos Eventos'),
            'campaigns_title' => Settings::get('homepage_campaigns_title', 'Campanhas em Andamento'),
            'testimonials_title' => Settings::get('homepage_testimonials_title', 'Testemunhos'),
            'gallery_title' => Settings::get('homepage_gallery_title', 'Galeria de Fotos'),
            'daily_verse_title' => Settings::get('homepage_daily_verse_title', 'Versículo do Dia'),
            'statistics_title' => Settings::get('homepage_statistics_title', 'Nossa Comunidade'),
            'newsletter_title' => Settings::get('homepage_newsletter_title', 'Mantenha-se Conectado'),

            'carousel_enabled' => Settings::get('homepage_carousel_enabled', false),
            'carousel_interval' => Settings::get('homepage_carousel_interval', 5000),
            'carousel_autoplay' => Settings::get('homepage_carousel_autoplay', true),
            'carousel_height' => Settings::get('homepage_carousel_height', 'h-96'),
            'carousel_indicators' => Settings::get('homepage_carousel_indicators', true),
            'carousel_controls' => Settings::get('homepage_carousel_controls', true),
            'carousel_transition' => Settings::get('homepage_carousel_transition', 'fade'),

            'show_scroll_to_top' => Settings::get('homepage_show_scroll_to_top', true),
            'show_scroll_to_bottom' => Settings::get('homepage_show_scroll_to_bottom', true),
            'scroll_button_position' => Settings::get('homepage_scroll_button_position', 'bottom-right'),
            'scroll_button_size' => Settings::get('homepage_scroll_button_size', 'medium'),
            'scroll_animation_type' => Settings::get('homepage_scroll_animation_type', 'smooth'),
            'scroll_down_position' => Settings::get('homepage_scroll_down_position', 'center'),
            'scroll_down_size' => Settings::get('homepage_scroll_down_size', 'medium'),

            'social_facebook' => Settings::get('homepage_social_facebook', ''),
            'social_instagram' => Settings::get('homepage_social_instagram', ''),
            'social_youtube' => Settings::get('homepage_social_youtube', ''),
            'radio_embed_url' => Settings::get(
                'homepage_radio_embed_url',
                'https://public-player-widget.webradiosite.com/?source=widget_embeded&locale=pt-br&info=https%3A%2F%2Fpublic-player-widget.webradiosite.com%2Fapp%2Fplayer%2Finfo%2F171445%3Fhash%3Df5672a87e8d4d6364055e1b586ab6ae9f84f43c6&theme=light&color=3&cover=0&current_track=0&schedules=1&link=0&share=1&popup=0&embed=1&auto_play=0'
            ),

            'stats_members' => Settings::get('homepage_stats_members', 150),
            'stats_years' => Settings::get('homepage_stats_years', 50),
            'ministries_count' => Settings::get('homepage_ministries_count', 4),
            'campaigns_count' => Settings::get('homepage_campaigns_count', 4),
        ];
    }
}
