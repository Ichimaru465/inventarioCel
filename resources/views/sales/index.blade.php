@extends('layouts.app')
@section('title', 'Boletas')

@section('content')
    <header class="main-header">
        <h1>Boletas</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">Registrar Venta</a>
    </header>

    <div class="content-wrapper">
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #dcfce7; color: #166534; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
            <tr>
                <th>Boleta</th>
                <th>Total</th>
                <th>Vendedor</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->receipt_number ?? ('BOL-' . $sale->id) }}</td>
                    <td>S/ {{ number_format($sale->total, 2) }}</td>
                    <td>{{ $sale->user->name ?? 'N/A' }}</td>
                    <td>{{ $sale->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}</td>
                    <td>
                        <div class="actions-container">
                            <a class="btn btn-info" href="{{ route('sales.show', $sale) }}">Ver</a>
                            <a class="btn btn-secondary" href="{{ route('sales.receipt.download', $sale) }}">Descargar PDF</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b;">Aún no hay boletas registradas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $sales->links() }}
        </div>
    </div>
@endsection

