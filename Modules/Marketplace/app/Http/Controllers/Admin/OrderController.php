<?php

namespace Modules\Marketplace\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Marketplace\Models\Order;
use Modules\Treasury\App\Models\Campaign;
use Modules\Notifications\App\Services\InAppNotificationService;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        protected InAppNotificationService $notifications
    ) {}

    public function index(Request $request): View
    {
        $query = Order::with(['items', 'campaign', 'user', 'pickupLocation'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        $orders = $query->paginate(15);
        $campaigns = Campaign::orderBy('name')->get();

        return view('marketplace::admin.orders.index', compact('orders', 'campaigns'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'campaign', 'user', 'pickupLocation']);

        return view('marketplace::admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:preparing,shipped_ready_for_pickup,completed,cancelled',
            'tracking_code' => 'nullable|string|max:100',
        ]);

        $oldStatus = $order->status;
        $order->status = $validated['status'];
        if (! empty($validated['tracking_code'])) {
            $order->tracking_code = $validated['tracking_code'];
        }
        if ($validated['status'] === Order::STATUS_SHIPPED_READY_FOR_PICKUP) {
            $order->shipped_at = $order->shipped_at ?? now();
        }
        if ($validated['status'] === Order::STATUS_COMPLETED) {
            $order->completed_at = $order->completed_at ?? now();
        }
        $order->save();

        $user = $order->user;
        if ($user) {
            if ($validated['status'] === Order::STATUS_SHIPPED_READY_FOR_PICKUP) {
                $message = $order->tracking_code
                    ? 'Seu pedido foi enviado! Rastreio: ' . $order->tracking_code
                    : 'Seu pedido está pronto para retirada!';
                $this->notifications->sendToUser($user, 'Pedido enviado / Pronto para retirada', $message, [
                    'type' => 'success',
                    'action_url' => route('memberpanel.marketplace.orders.show', $order->uuid),
                    'action_text' => 'Ver pedido',
                ]);
            } elseif ($validated['status'] === Order::STATUS_COMPLETED) {
                $this->notifications->sendToUser($user, 'Pedido finalizado', 'Seu pedido #' . $order->uuid . ' foi concluído.', [
                    'type' => 'success',
                    'action_url' => route('memberpanel.marketplace.orders.show', $order->uuid),
                    'action_text' => 'Ver pedido',
                ]);
            }
        }

        return back()->with('success', 'Status atualizado.');
    }

    public function label(Order $order): Response
    {
        $order->load(['items', 'pickupLocation']);

        return response()->view('marketplace::admin.orders.label', compact('order'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
