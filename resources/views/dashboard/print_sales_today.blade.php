<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas del Día - Accesorios Ramirez</title>
    <style>
        body { font-family: 'Nunito', sans-serif; font-size: 12px; }
        h1 { text-align: center; }
        .date-header { text-align: center; margin-bottom: 20px; color: #555; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tfoot tr td { font-weight: bold; background-color: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <h1>Reporte de Ventas del Día  - Accesorios Ramirez</h1>
    <p class="date-header">Fecha: {{ now()->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Producto</th>
                <th>Atributos</th>
                <th>Categoría</th>
                <th>Cant.</th>
                <th>Precio Unit.</th>
                <th>Descuento</th>
                <th>Subtotal</th>
                <th>Vendido por</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($salesToday as $sale)
                @php
                    $unitPrice = $sale->price ?? $sale->product->price ?? 0;
                    $subtotal = $unitPrice * $sale->quantity;
                    $discount = $sale->discount_amount ?? 0;
                    $finalTotal = $subtotal - $discount;
                @endphp
                <tr>
                    <td>{{ $sale->created_at->timezone('America/Lima')->format('h:i A') }}</td>
                    <td>{{ $sale->product->name ?? 'N/A' }}</td>
                    <td class="attributes-cell">
                        @if($sale->product && $sale->product->attributes)
                            @foreach($sale->product->attributes as $key => $value)
                                @if(!empty($value))
                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}<br>
                                @endif
                            @endforeach
                        @else
                            <span>-</span>
                        @endif
                    </td>
                    <td>{{ $sale->product->category->name ?? 'Sin categoría' }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td class="text-right">S/ {{ number_format($unitPrice, 2) }}</td>
                    <td class="text-right">- S/ {{ number_format($discount, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($finalTotal, 2) }}</td>
                    <td>{{ $sale->user->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No hay ventas que mostrar para hoy.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>TOTALES</strong></td>
                <td><strong>{{ $totalItems }}</strong></td>
                <td></td>
                <td></td>
                <td class="text-right"><strong>S/ {{ number_format($totalRevenue, 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>

    </table>
</body>
</html>
