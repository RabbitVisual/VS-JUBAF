<?php

namespace Modules\Marketplace\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MarketplaceServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Marketplace';

    protected string $nameLower = 'marketplace';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        View::composer('marketplace::layouts.storefront', function ($view) {
            $cart = session('marketplace_cart', []);
            $view->with('cartCount', (int) array_sum(array_column($cart, 'quantity')));
        });
        $cartComposer = function ($view) {
            $cart = session('marketplace_cart', []);
            $cartCount = (int) array_sum(array_column($cart, 'quantity'));
            $cartSummary = [];
            if (! empty($cart)) {
                $productIds = array_unique(array_column($cart, 'product_id'));
                $products = \Modules\Marketplace\Models\Product::with(['skus', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1)])
                    ->whereIn('id', $productIds)->get()->keyBy('id');
                $cartTotal = 0;
                foreach ($cart as $item) {
                    $p = $products->get($item['product_id'] ?? 0);
                    if (! $p) {
                        continue;
                    }
                    $skuId = isset($item['sku_id']) && $item['sku_id'] ? (int) $item['sku_id'] : null;
                    $qty = (int) ($item['quantity'] ?? 1);
                    $price = (float) $p->price;
                    $title = $p->title;
                    if ($skuId && $p->relationLoaded('skus')) {
                        $sku = $p->skus->firstWhere('id', $skuId);
                        if ($sku) {
                            $price = (float) ($sku->price_override ?? $p->price);
                            $title = $p->title . ' - ' . $sku->display_name;
                        }
                    }
                    $subtotal = $price * $qty;
                    $cartTotal += $subtotal;
                    $thumb = $p->images->isNotEmpty()
                        ? $p->images->first()->url
                        : ($p->image_url ?? null);
                    $cartSummary[] = [
                        'product_id' => (int) $p->id,
                        'sku_id' => $skuId,
                        'title' => $title,
                        'price' => $price,
                        'quantity' => $qty,
                        'subtotal' => $subtotal,
                        'thumb' => $thumb,
                    ];
                }
                $view->with('marketplace_cart_total', $cartTotal);
            } else {
                $view->with('marketplace_cart_total', 0);
            }
            $view->with('marketplace_cart_count', $cartCount);
            $view->with('marketplace_cart_summary', $cartSummary);
            $view->with('marketplace_store_available', (bool) \App\Models\Settings::get('homepage_show_marketplace', false) && ! (bool) \App\Models\Settings::get('marketplace_maintenance', false));
        };
        View::composer('homepage::components.navbar', $cartComposer);
        View::composer('admin::components.navbar', $cartComposer);
        View::composer('memberpanel::components.navbar', $cartComposer);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
