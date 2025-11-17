@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Delivery Tracking
                </h2>
                <div class="text-muted mt-1">Monitor procurement deliveries and performance</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('supplier.dashboard') }}" class="btn btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Procurement Deliveries</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Expected Delivery</th>
                            <th>Actual Delivery</th>
                            <th>Status</th>
                            <th>Total Cost</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procurements as $procurement)
                            <tr>
                                <td>{{ $procurement->product->product_name ?? 'N/A' }}</td>
                                <td>{{ $procurement->quantity_supplied }} {{ $procurement->product->unit->name ?? '' }}</td>
                                <td>{{ $procurement->expected_delivery_date ? $procurement->expected_delivery_date->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    @if($procurement->delivery_date)
                                        {{ $procurement->delivery_date->format('M d, Y') }}
                                        @if($procurement->isOnTime())
                                            <span class="badge bg-success ms-1">On Time</span>
                                        @else
                                            <span class="badge bg-warning ms-1">{{ $procurement->getDeliveryDelayDays() }} days late</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $procurement->status === 'on-time' ? 'success' : ($procurement->status === 'delayed' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($procurement->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($procurement->total_cost, 2) }}</td>
                                <td>
                                    <a href="{{ route('supplier.deliveries.show', $procurement->id) }}" class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No procurement deliveries found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($procurements->hasPages())
                <div class="card-footer">
                    {{ $procurements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
