<?php

namespace Modules\Marketplace\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Marketplace\Models\Order;
use Modules\Marketplace\Models\Product;
use Modules\Marketplace\Services\MarketplaceEliasService;
use Modules\Treasury\App\Models\Campaign;

class DashboardController extends Controller
{
    public function __construct(
        protected MarketplaceEliasService $eliasService
    ) {}

    public function index()
    {
        $ordersByCampaign = Order::query()
            ->whereNotNull('campaign_id')
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PREPARING, Order::STATUS_SHIPPED_READY_FOR_PICKUP, Order::STATUS_COMPLETED])
            ->select('campaign_id', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('campaign_id')
            ->get();

        $campaigns = Campaign::whereIn('id', $ordersByCampaign->pluck('campaign_id'))->get()->keyBy('id');
        $chartData = $ordersByCampaign->map(function ($row) use ($campaigns) {
            return [
                'campaign' => $campaigns->get($row->campaign_id)?->name ?? 'Outros',
                'total' => (float) $row->total,
                'count' => (int) $row->count,
            ];
        });

        $totalRevenue = Order::whereIn('status', [Order::STATUS_PAID, Order::STATUS_PREPARING, Order::STATUS_SHIPPED_READY_FOR_PICKUP, Order::STATUS_COMPLETED])
            ->sum('total_amount');
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();
        $productsCount = Product::count();
        $lowStockCount = Product::where('stock', '>', 0)->where('stock', '<=', config('marketplace.low_stock_threshold', 5))->count();
        $lowStockSuggestions = $this->eliasService->getLowStockSuggestions();
        $pendingShipmentOrders = $this->eliasService->getPendingShipmentOrders();

        return view('marketplace::admin.dashboard', compact(
            'chartData',
            'totalRevenue',
            'pendingOrders',
            'productsCount',
            'lowStockCount',
            'lowStockSuggestions',
            'pendingShipmentOrders'
        ));
    }
}
