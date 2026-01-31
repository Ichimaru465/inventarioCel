<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Boleta</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 18px; }
        .meta { width: 100%; margin-bottom: 14px; }
        .meta td { padding: 2px 0; vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .table th, .table td { border: 1px solid #e5e7eb; padding: 6px; }
        .table th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .totals { width: 100%; margin-top: 12px; }
        .totals td { padding: 3px 0; }
        .totals .label { text-align: right; padding-right: 10px; }
        .totals .value { text-align: right; width: 120px; }
        .footer { text-align: center; margin-top: 18px; color: #6b7280; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Accesorios Ramirez</h1>
        <div>Boleta de Venta</div>
    </div>

    <table class="meta">
        <tr>
            <td><strong>Boleta:</strong> {{ $sale->receipt_number ?? ('BOL-' . $sale->id) }}</td>
            <td class="right"><strong>Fecha:</strong> {{ $sale->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td><strong>Vendedor:</strong> {{ $sale->user->name ?? 'N/A' }}</td>
            <td></td>
        </tr>
    </table>

    <table class="table">
        <thead>
        <tr>
            <th>Producto</th>
            <th>Código</th>
            <th>Categoría</th>
            <th>Atributos</th>
            <th class="right">Cant.</th>
            <th class="right">Precio</th>
            <th class="right">Desc.</th>
            <th class="right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($sale->items as $item)
            @php
                $lineTotal = ($item->price * $item->quantity) - $item->discount_amount;
            @endphp
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->product_sku ?? '-' }}</td>
                <td>{{ $item->product_category_name ?? 'Sin categoría' }}</td>
                <td>
                    @if($item->product_attributes)
                        @foreach($item->product_attributes as $key => $value)
                            @if(!empty($value))
                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}<br>
                            @endif
                        @endforeach
                    @else
                        <span>-</span>
                    @endif
                </td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">S/ {{ number_format($item->price, 2) }}</td>
                <td class="right">- S/ {{ number_format($item->discount_amount, 2) }}</td>
                <td class="right">S/ {{ number_format($lineTotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">S/ {{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Descuento</td>
            <td class="value">- S/ {{ number_format($sale->discount_total, 2) }}</td>
        </tr>
        <tr>
            <td class="label"><strong>TOTAL</strong></td>
            <td class="value"><strong>S/ {{ number_format($sale->total, 2) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        Gracias por su compra.
    </div>
</body>
</html>

