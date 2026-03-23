<?php

namespace Modules\Marketplace\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Marketplace\Services\MarketplaceEliasService;
use Modules\Notifications\App\Services\InAppNotificationService;

class EliasController extends Controller
{
    public function __construct(
        protected MarketplaceEliasService $eliasService,
        protected InAppNotificationService $notifications
    ) {}

    public function notifyLowStock(): RedirectResponse
    {
        $products = $this->eliasService->getLowStockSuggestions();
        if ($products->isEmpty()) {
            return redirect()->route('admin.marketplace.dashboard')->with('info', 'Nenhum produto com estoque baixo no momento.');
        }

        $titles = $products->pluck('title')->take(3)->implode(', ');
        if ($products->count() > 3) {
            $titles .= ' e mais ' . ($products->count() - 3) . '.';
        }

        $this->notifications->sendToRole('member', 'Loja Missionária – Estoque baixo', 'Alguns produtos estão com estoque limitado: ' . $titles . ' Aproveite!', [
            'type' => 'info',
            'action_url' => route('marketplace.storefront.index'),
            'action_text' => 'Ver loja',
        ]);

        return redirect()->route('admin.marketplace.dashboard')->with('success', 'Notificação enviada à igreja.');
    }
}
