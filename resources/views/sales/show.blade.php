@extends('layouts.app')
@section('title', 'Boleta')

@section('content')
    <header class="main-header">
        <h1>Boleta</h1>
        <div class="actions-container">
            <a class="btn btn-secondary" href="{{ route('sales.receipt.download', $sale) }}">Descargar PDF</a>
            <a class="btn btn-info" href="{{ route('sales.index') }}">Volver</a>
        </div>
    </header>

    <div class="content-wrapper">
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #dcfce7; color: #166534; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                {{ $errors->first() }}
            </div>
        @endif

        <div style="display:flex; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
            <div>
                <div><strong>Boleta:</strong> {{ $sale->receipt_number ?? ('BOL-' . $sale->id) }}</div>
                <div><strong>Fecha:</strong> {{ $sale->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}</div>
                <div><strong>Vendedor:</strong> {{ $sale->user->name ?? 'N/A' }}</div>
                <div>
                    <strong>Estado:</strong>
                    @if($sale->isCanceled())
                        <span style="background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:12px;font-size:.8em;font-weight:700;">Anulada</span>
                    @else
                        <span style="background:#dcfce7;color:#166534;padding:4px 8px;border-radius:12px;font-size:.8em;font-weight:700;">Completada</span>
                    @endif
                </div>
                @if($sale->isCanceled())
                    <div><strong>Anulada por:</strong> {{ $sale->canceledBy->name ?? 'N/A' }}</div>
                    <div><strong>Fecha de anulación:</strong> {{ $sale->canceled_at?->timezone('America/Lima')->format('d/m/Y h:i A') }}</div>
                    @if($sale->cancellation_reason)
                        <div><strong>Motivo:</strong> {{ $sale->cancellation_reason }}</div>
                    @endif
                @endif
            </div>
            <div style="min-width: 220px;">
                <div style="display:flex; justify-content: space-between;"><span>Subtotal</span><strong>S/ {{ number_format($sale->subtotal, 2) }}</strong></div>
                <div style="display:flex; justify-content: space-between;"><span>Descuento</span><strong>- S/ {{ number_format($sale->discount_total, 2) }}</strong></div>
                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 10px 0;">
                <div style="display:flex; justify-content: space-between; font-size: 18px;"><span>Total</span><strong>S/ {{ number_format($sale->total, 2) }}</strong></div>
            </div>
        </div>

        @if(auth()->user()->role === 'admin' && ! $sale->isCanceled())
            <form method="POST" action="{{ route('sales.cancel', $sale) }}" style="margin-top:20px;" onsubmit="return confirm('¿Seguro que deseas anular esta venta? El stock volverá al inventario.');">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-danger">Anular venta</button>
            </form>
        @endif

        <table class="table" style="margin-top: 20px;">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Código</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Descuento</th>
                <th>Total Línea</th>
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
                    <td>{{ $item->quantity }}</td>
                    <td>S/ {{ number_format($item->price, 2) }}</td>
                    <td>- S/ {{ number_format($item->discount_amount, 2) }}</td>
                    <td>S/ {{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if($autoDownload)
        <script>
            // Dispara la descarga automáticamente al llegar a esta página
            window.addEventListener('load', function () {
                window.location.href = @json(route('sales.receipt.download', $sale));
            });
        </script>
    @endif
@endsection

