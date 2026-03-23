<?php

namespace Modules\Marketplace\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Modules\Marketplace\Models\Product;
use Modules\Marketplace\Services\MarketplacePolicyService;

class StorefrontController extends Controller
{
    private function storeEnabled(): bool
    {
        return (bool) Settings::get('homepage_show_marketplace', false);
    }

    private function maintenanceMode(): bool
    {
        return (bool) Settings::get('marketplace_maintenance', false);
    }

    private function showcaseOnlyMode(): bool
    {
        return (bool) Settings::get('marketplace_showcase_only', false);
    }

    private function canPurchase(): bool
    {
        return $this->storeEnabled() && ! $this->maintenanceMode() && ! $this->showcaseOnlyMode();
    }

    public function index(Request $request)
    {
        if (! $this->storeEnabled()) {
            return response()->view('marketplace::public.closed', [], 503);
        }
        if ($this->maintenanceMode()) {
            return response()->view('marketplace::public.closed', [], 503);
        }

        $query = Product::active()
            ->with(['campaign', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'skus']);

        $availability = $request->input('availability');
        if ($availability === 'in_stock') {
            $query->where(function ($q) {
                $q->where('stock', '>', 0)
                    ->orWhereHas('skus', fn ($sq) => $sq->where('stock', '>', 0));
            });
        } elseif ($availability === 'out_of_stock') {
            $query->where('stock', '<=', 0)
                ->whereDoesntHave('skus', fn ($sq) => $sq->where('stock', '>', 0));
        }
        // else: todos – no availability filter

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        $minPrice = $request->filled('min_price') && is_numeric($request->min_price) ? (float) $request->min_price : null;
        $maxPrice = $request->filled('max_price') && is_numeric($request->max_price) ? (float) $request->max_price : null;
        if ($minPrice !== null || $maxPrice !== null) {
            $query->where(function ($q) use ($minPrice, $maxPrice) {
                $q->whereDoesntHave('skus');
                if ($minPrice !== null) {
                    $q->where('price', '>=', $minPrice);
                }
                if ($maxPrice !== null) {
                    $q->where('price', '<=', $maxPrice);
                }
            });
            $idsWithSkus = \Illuminate\Support\Facades\DB::table('marketplace_product_skus')
                ->selectRaw('product_id, MIN(COALESCE(price_override, (SELECT price FROM marketplace_products p WHERE p.id = marketplace_product_skus.product_id))) as eff_min')
                ->groupBy('product_id');
            if ($minPrice !== null) {
                $idsWithSkus->havingRaw('MIN(COALESCE(price_override, (SELECT price FROM marketplace_products p WHERE p.id = marketplace_product_skus.product_id))) >= ?', [$minPrice]);
            }
            if ($maxPrice !== null) {
                $idsWithSkus->havingRaw('MIN(COALESCE(price_override, (SELECT price FROM marketplace_products p WHERE p.id = marketplace_product_skus.product_id))) <= ?', [$maxPrice]);
            }
            $query->orWhereIn('id', $idsWithSkus->pluck('product_id'));
        }

        $products = $query->orderBy('sort_order')->orderBy('title')->paginate(12)->withQueryString();

        $categories = Product::categories();
        $campaigns = \Modules\Treasury\App\Models\Campaign::where('is_active', true)->orderBy('name')->get();
        $canPurchase = $this->canPurchase();

        return view('marketplace::public.index', compact('products', 'categories', 'campaigns', 'canPurchase'));
    }

    public function show(string $slugOrUuid)
    {
        if (! $this->storeEnabled()) {
            return response()->view('marketplace::public.closed', [], 503);
        }
        if ($this->maintenanceMode()) {
            return response()->view('marketplace::public.closed', [], 503);
        }

        $product = Product::active()
            ->where(function ($q) use ($slugOrUuid) {
                $q->where('slug', $slugOrUuid)->orWhere('uuid', $slugOrUuid);
            })
            ->with([
                'campaign',
                'pickupLocation',
                'images' => fn ($q) => $q->orderBy('sort_order'),
                'options.values',
                'skus',
            ])
            ->firstOrFail();

        $canPurchase = $this->canPurchase();
        $title = $product->title;
        $description = \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 160);

        return view('marketplace::public.show', compact('product', 'canPurchase', 'title', 'description'));
    }

    public function thankYou()
    {
        return view('marketplace::public.thank-you');
    }

    public function policyPage(string $slug)
    {
        if (! $this->storeEnabled()) {
            return response()->view('marketplace::public.closed', [], 503);
        }
        if ($this->maintenanceMode()) {
            return response()->view('marketplace::public.closed', [], 503);
        }

        $map = [
            'politica-entrega' => ['key' => 'delivery', 'title' => __('marketplace::messages.policy_delivery')],
            'trocas' => ['key' => 'returns', 'title' => __('marketplace::messages.policy_returns')],
            'termos' => ['key' => 'terms', 'title' => __('marketplace::messages.policy_terms')],
        ];
        if (! isset($map[$slug])) {
            abort(404);
        }
        $policies = MarketplacePolicyService::getCached();
        $content = $policies[$map[$slug]['key']] ?? '';
        $title = $map[$slug]['title'];

        return view('marketplace::public.policy', compact('title', 'content'));
    }
}
