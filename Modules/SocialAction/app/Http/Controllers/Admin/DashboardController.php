<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialAssistance;
use Modules\SocialAction\App\Models\SocialBeneficiary;
use Modules\SocialAction\App\Models\SocialCampaign;
use Modules\SocialAction\App\Models\SocialPantryItem;
use Modules\SocialAction\App\Models\SocialPrayerRequest;
use Modules\SocialAction\App\Models\SocialVolunteer;

class DashboardController extends Controller
{
    public function index(): View
    {
        // KPI Stats
        $stats = [
            'total_beneficiaries'    => SocialBeneficiary::count(),
            'active_beneficiaries'   => SocialBeneficiary::active()->count(),
            'assistances_this_month' => SocialAssistance::whereMonth('registered_at', now()->month)
                                           ->whereYear('registered_at', now()->year)->count(),
            'kits_delivered'         => SocialAssistance::whereNotNull('kit_id')->count(),
            'active_volunteers'      => SocialVolunteer::active()->count(),
            'active_campaigns'       => SocialCampaign::where('status', 'active')->count(),
            'pending_prayers'        => SocialPrayerRequest::pending()->count(),
            'low_stock_items'        => SocialPantryItem::whereColumn('current_quantity', '<=', 'min_quantity')
                                           ->where('current_quantity', '>', 0)->count(),
            'out_of_stock_items'     => SocialPantryItem::where('current_quantity', '<=', 0)->count(),
        ];

        // Últimas assistências (10)
        $recentAssistances = SocialAssistance::with(['beneficiary', 'kit', 'pantryItem', 'volunteer'])
            ->latest('registered_at')
            ->limit(10)
            ->get();

        // Itens com estoque crítico (abaixo do mínimo)
        $criticalStockItems = SocialPantryItem::where('current_quantity', '<=', 0)
            ->orWhereColumn('current_quantity', '<=', 'min_quantity')
            ->orderBy('current_quantity')
            ->limit(6)
            ->get();

        // Campanhas ativas com progresso
        $activeCampaigns = SocialCampaign::where('status', 'active')
            ->with('treasuryCampaign')
            ->orderBy('end_date')
            ->limit(4)
            ->get();

        // Pedidos de oração pendentes
        $pendingPrayers = SocialPrayerRequest::pending()
            ->latest()
            ->limit(5)
            ->get();

        return view('socialaction::admin.dashboard.index', compact(
            'stats',
            'recentAssistances',
            'criticalStockItems',
            'activeCampaigns',
            'pendingPrayers',
        ));
    }
}
