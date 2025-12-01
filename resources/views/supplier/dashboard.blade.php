@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-store me-2"></i>Supplier Portal
                </h2>
                <div class="text-muted mt-1">Welcome back, {{ $user->name }}!</div>
                @if($supplier)
                    <div class="text-muted">{{ $supplier->shopname ?? $supplier->name }}</div>
                @endif
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('supplier.purchases.index') }}" class="btn btn-primary d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" /><rect x="9" y="3" width="6" height="4" rx="2" /></svg>
                        <span>View Orders from Admin</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Enhanced Statistics Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card supplier-stat-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="supplier-stat-icon bg-primary-lt text-primary">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Active Orders</div>
                                <div class="text-muted">{{ $stats['active_orders'] }} pending</div>
                            </div>
                            <div class="col-auto">
                                <div class="h2 mb-0">{{ $stats['pending_purchases'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card supplier-stat-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="supplier-stat-icon bg-green-lt text-green">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Completed Orders</div>
                                <div class="text-muted">Successfully delivered</div>
                            </div>
                            <div class="col-auto">
                                <div class="h2 mb-0">{{ $stats['completed_purchases'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card supplier-stat-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="supplier-stat-icon bg-azure-lt text-azure">
                                    <i class="fas fa-peso-sign"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Total Revenue</div>
                                <div class="text-muted">From approved orders</div>
                            </div>
                            <div class="col-auto">
                                <div class="h2 mb-0">₱{{ number_format($stats['total_revenue'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card supplier-stat-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="supplier-stat-icon bg-purple-lt text-purple">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Delivery Rating</div>
                                <div class="text-muted">{{ number_format($stats['on_time_percentage'], 1) }}% on-time</div>
                            </div>
                            <div class="col-auto">
                                <div class="h2 mb-0">{{ number_format($stats['delivery_rating'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Performance Section -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Revenue Trend (Last 6 Months)</h3>
                    </div>
                    <div class="card-body">
                        <div class="revenue-chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <div class="h2">{{ $stats['total_procurements'] }}</div>
                                <div class="text-muted">Total Procurements</div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="h2">{{ number_format($stats['on_time_percentage'], 1) }}%</div>
                                <div class="text-muted">On-Time Delivery</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-muted mb-2">Delivery Rating</div>
                            <div class="d-flex align-items-center">
                                <div class="h1 mb-0 me-3">{{ number_format($stats['delivery_rating'], 2) }}</div>
                                <div class="text-muted">/ 5.00</div>
                            </div>
                            <div class="progress delivery-rating-progress mt-2">
                                <div class="progress-bar" style="width: {{ min(100, $stats['delivery_rating'] * 20) }}%" role="progressbar" aria-valuenow="{{ $stats['delivery_rating'] }}" aria-valuemin="0" aria-valuemax="5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions and Recent Activity -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <a href="{{ route('supplier.purchases.index') }}" class="card card-link quick-action-card h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <div class="quick-action-icon bg-blue-lt text-blue mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                            <i class="fas fa-shopping-cart fa-lg"></i>
                                        </div>
                                        <div class="card-title mb-1 fw-bold">Orders</div>
                                        <div class="text-muted small">Manage orders</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-6 col-md-4">
                                <a href="{{ route('supplier.deliveries.index') }}" class="card card-link quick-action-card h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <div class="quick-action-icon bg-orange-lt text-orange mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                            <i class="fas fa-truck fa-lg"></i>
                                        </div>
                                        <div class="card-title mb-1 fw-bold">Deliveries</div>
                                        <div class="text-muted small">Track shipments</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-6 col-md-4">
                                <a href="#" class="card card-link quick-action-card h-100">
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <div class="quick-action-icon bg-green-lt text-green mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                                        </div>
                                        <div class="card-title mb-1 fw-bold">Invoices</div>
                                        <div class="text-muted small">View invoices</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Orders</h3>
                    </div>
                    <div class="card-body">
                        @if($recentPurchases->count() > 0)
                            <div class="divide-y">
                                @foreach($recentPurchases as $purchase)
                                    <div class="recent-order-item {{ !$loop->last ? 'border-bottom' : '' }} py-3">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar bg-primary-lt text-primary rounded-circle">#{{ substr($purchase->purchase_no, -4) }}</span>
                                            </div>
                                            <div class="col">
                                                <div class="text-reset d-block fw-medium">Order #{{ $purchase->purchase_no }}</div>
                                                <div class="text-muted small">{{ $purchase->created_at->format('M d, Y') }}</div>
                                            </div>
                                            <div class="col-auto text-end">
                                                <div class="h3 mb-1 text-dark">₱{{ number_format($purchase->total_amount, 2) }}</div>
                                                <div class="badge bg-{{ $purchase->status->value == 'approved' ? 'success' : ($purchase->status->value == 'pending' ? 'warning' : 'secondary') }} fs-7 py-1 px-2">
                                                    {{ ucfirst($purchase->status->value) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="text-muted">No recent orders</div>
                                <div class="small text-muted mt-1">Orders from admin will appear here</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Welcome to Supplier Portal</h3>
                    </div>
                    <div class="card-body">
                        <p>This is your supplier dashboard where you can view orders from the admin, track deliveries, and monitor your business performance.</p>
                        <p class="mb-0">Use the quick actions above to manage your orders and deliveries efficiently.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyRevenue['months'] ?? []),
            datasets: [{
                label: 'Revenue (₱)',
                data: @json($monthlyRevenue['revenue'] ?? []),
                borderColor: '#8B0000',
                backgroundColor: 'rgba(139, 0, 0, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection

@push('styles')
<style>
.quick-action-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-color: rgba(0, 0, 0, 0.2);
}

.quick-action-icon {
    transition: all 0.3s ease;
}

.quick-action-card:hover .quick-action-icon {
    transform: scale(1.1);
}
</style>
@endpush