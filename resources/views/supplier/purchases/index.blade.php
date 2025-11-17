@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    My Purchase Orders
                </h2>
                <div class="text-muted mt-1">Track and manage your purchase orders</div>
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
                <h3 class="card-title">Purchase Orders List</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Purchase No.</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->purchase_no }}</td>
                                <td>{{ $purchase->date->format('M d, Y') }}</td>
                                <td>₱{{ number_format($purchase->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $purchase->status->value === 'pending' ? 'warning' : ($purchase->status->value === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($purchase->status->value) }}
                                    </span>
                                </td>
                                <td>{{ $purchase->createdBy->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('supplier.purchases.show', $purchase->id) }}" class="btn btn-sm btn-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No purchase orders found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($purchases->hasPages())
                <div class="card-footer">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
