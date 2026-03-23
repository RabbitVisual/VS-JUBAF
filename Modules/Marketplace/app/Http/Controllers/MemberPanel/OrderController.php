<?php

namespace Modules\Marketplace\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Marketplace\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::forUser(Auth::id())
            ->with(['items', 'campaign', 'pickupLocation'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('marketplace::memberpanel.orders.index', compact('orders'));
    }

    public function show(string $uuid)
    {
        $order = Order::where('uuid', $uuid)
            ->forUser(Auth::id())
            ->with(['items.product', 'campaign', 'pickupLocation'])
            ->firstOrFail();

        return view('marketplace::memberpanel.orders.show', compact('order'));
    }
}
