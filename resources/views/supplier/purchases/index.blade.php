@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-shopping-cart me-2"></i>Orders from Admin
                </h2>
                <div class="text-muted mt-1">Track and manage orders placed by the admin</div>
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
                <h3 class="card-title">
                    <i class="fas fa-list me-2"></i>Admin Orders List
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped supplier-order-table">
                    <thead>
                        <tr>
                            <th>Order No.</th>
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
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-primary-lt text-primary me-2">#{{ substr($purchase->purchase_no, -4) }}</span>
                                        <div>
                                            <div class="font-weight-medium">{{ $purchase->purchase_no }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $purchase->date->format('M d, Y') }}</div>
                                    <div class="text-muted small">{{ $purchase->date->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <div class="h4 mb-0">₱{{ number_format($purchase->total_amount, 2) }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                                    @endphp
                                    @if($statusValue == 0)
                                        <span class="badge bg-warning status-badge">Pending</span>
                                    @elseif($statusValue == 1)
                                        <span class="badge bg-success status-badge">Approved</span>
                                    @elseif($statusValue == 2)
                                        <span class="badge bg-info status-badge">For Delivery</span>
                                    @elseif($statusValue == 3)
                                        <span class="badge bg-primary status-badge">Complete</span>
                                    @elseif($statusValue == 4)
                                        <span class="badge bg-success status-badge">Received</span>
                                    @else
                                        <span class="badge bg-secondary status-badge">{{ ucfirst((string) $purchase->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $purchase->createdBy->name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('supplier.purchases.show', $purchase->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('supplier.purchases.download-invoice', $purchase->id) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-file-invoice me-1"></i>Invoice
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <div class="h3">No orders found</div>
                                        <div class="text-muted mt-2">Orders from admin will appear here</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($purchases->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">Showing <span>{{ $purchases->firstItem() }}</span> to <span>{{ $purchases->lastItem() }}</span> of <span>{{ $purchases->total() }}</span> entries</p>
                    <ul class="pagination m-0 ms-auto">
                        {{ $purchases->links() }}
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection