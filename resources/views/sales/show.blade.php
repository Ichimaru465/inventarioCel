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

        <div style="display:flex; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
            <div>
                <div><strong>Boleta:</strong> {{ $sale->receipt_number ?? ('BOL-' . $sale->id) }}</div>
                <div><strong>Fecha:</strong> {{ $sale->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}</div>
                <div><strong>Vendedor:</strong> {{ $sale->user->name ?? 'N/A' }}</div>
            </div>
            <div style="min-width: 220px;">
                <div style="display:flex; justify-content: space-between;"><span>Subtotal</span><strong>S/ {{ number_format($sale->subtotal, 2) }}</strong></div>
                <div style="display:flex; justify-content: space-between;"><span>Descuento</span><strong>- S/ {{ number_format($sale->discount_total, 2) }}</strong></div>
                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 10px 0;">
                <div style="display:flex; justify-content: space-between; font-size: 18px;"><span>Total</span><strong>S/ {{ number_format($sale->total, 2) }}</strong></div>
            </div>
        </div>

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

