<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta - Pedido #{{ $order->uuid }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; padding: 16px; max-width: 400px; margin: 0 auto; }
        .label { border: 1px solid #333; padding: 12px; margin-bottom: 16px; }
        .label h2 { margin: 0 0 8px 0; font-size: 14px; }
        .label p { margin: 4px 0; }
        .items { margin-top: 12px; border-top: 1px dashed #999; padding-top: 8px; }
        .total { font-weight: bold; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="label">
        <h2>Pedido #{{ $order->uuid }}</h2>
        <p><strong>{{ $order->payer_name ?? $order->user?->name ?? 'Cliente' }}</strong></p>
        @if($order->delivery_type === 'shipping' && $order->shipping_address)
            <p>{{ $order->shipping_address['street'] ?? '' }}, {{ $order->shipping_address['number'] ?? '' }}</p>
            <p>{{ $order->shipping_address['neighborhood'] ?? '' }} {{ $order->shipping_address['complement'] ?? '' }}</p>
            <p>{{ $order->shipping_address['city'] ?? '' }} - {{ $order->shipping_address['state'] ?? '' }} - CEP {{ $order->shipping_address['zipcode'] ?? '' }}</p>
        @elseif($order->pickupLocation)
            <p>Retirada: {{ $order->pickupLocation->name }}</p>
            <p>{{ $order->pickupLocation->address }}</p>
        @endif
        <div class="items">
            @foreach($order->items as $item)
                <p>{{ $item->quantity }}x {{ $item->title }} - R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</p>
            @endforeach
        </div>
        <p class="total">Total: R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
    </div>
    <p style="text-align: center; color: #666;">Imprima e cole na embalagem.</p>
</body>
</html>
