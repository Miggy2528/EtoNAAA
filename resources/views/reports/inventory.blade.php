@extends('layouts.butcher')

@push('page-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        border-left: 4px solid var(--primary-color);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stat-card.success {
        border-left-color: #28a745;
    }
    .stat-card.warning {
        border-left-color: #ffc107;
    }
    .stat-card.danger {
        border-left-color: #dc3545;
    }
    .stat-card.info {
        border-left-color: #17a2b8;
    }
    .stat-card.purple {
        border-left-color: #6f42c1;
    }
    .stat-card.orange {
        border-left-color: #fd7e14;
    }
    
    .expiring-badge {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    
    .activity-item {
        border-left: 3px solid #e9ecef;
        padding-left: 1rem;
        margin-bottom: 1rem;
    }
    
    .activity-item.created { border-left-color: #28a745; }
    .activity-item.updated { border-left-color: #17a2b8; }
    .activity-item.deleted { border-left-color: #dc3545; }
    
    .real-time-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #28a745;
        border-radius: 50%;
        animation: blink 1.5s infinite;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header with Real-time Indicator -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-warehouse me-2"></i>Inventory Analytics
                <span class="badge bg-success ms-2">
                    <span class="real-time-indicator me-1"></span>Live
                </span>
            </h1>
            <p class="text-muted">Real-time inventory insights, stock levels, and expiration tracking</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Reports
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-box me-1"></i>
                View Products
            </a>
        </div>
    </div>

    <x-alert/>

    <!-- Enhanced Inventory Overview Cards - Row 1 -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary text-white avatar me-3">
                            <i class="fas fa-box-open"></i>
                        </span>
                        <div>
                            <div class="h3 mb-0">{{ $totalProducts }}</div>
                            <div class="text-muted small">Total Products</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-success text-white avatar me-3">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div>
                            <div class="h3 mb-0 text-success">{{ $inStockItems }}</div>
                            <div class="text-muted small">In Stock</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stat-card warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-warning text-white avatar me-3">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <div>
                            <div class="h3 mb-0 text-warning">{{ $lowStockItems }}</div>
                            <div class="text-muted small">Low Stock</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stat-card danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-danger text-white avatar me-3">
                            <i class="fas fa-times-circle"></i>
                        </span>
                        <div>
                            <div class="h3 mb-0 text-danger">{{ $outOfStockItems }}</div>
                            <div class="text-muted small">Out of Stock</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stat-card orange">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-warning text-white avatar me-3">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <div class="h3 mb-0 text-warning expiring-badge">{{ $expiringItems }}</div>
                            <div class="text-muted small">Expiring Soon</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card stat-card info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-info text-white avatar me-3">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <div>
                            <div class="h4 mb-0 text-info">₱{{ number_format($totalStockValue, 0) }}</div>
                            <div class="text-muted small">Stock Value</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alerts and Top Selling Row -->
    <div class="row mb-4">
        
        <!-- Expiring Products Alert -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-header bg-warning text-white">
                    <h3 class="card-title">
                        <i class="fas fa-hourglass-half me-2"></i>
                        Products Expiring Soon
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($expiringProducts as $product)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <div class="small text-muted">{{ $product->meatCut->name ?? 'N/A' }}</div>
                                    </div>
                                    <span class="badge bg-danger">
                                        {{ \Carbon\Carbon::parse($product->expiration_date)->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="small mt-1">
                                    <i class="fas fa-calendar me-1"></i>
                                    Expires: {{ \Carbon\Carbon::parse($product->expiration_date)->format('M d, Y') }}
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p class="mb-0">No products expiring soon</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-md-8">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-fire me-2"></i>Top Selling Today <span class="badge bg-secondary ms-2">Top 5</span></h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSellingDaily as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td class="text-end">{{ $item->total_qty }}</td>
                                            <td class="text-end">₱{{ number_format($item->revenue, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-alt me-2"></i>Top Selling This Month <span class="badge bg-secondary ms-2">Top 5</span></h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSellingMonthly as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td class="text-end">{{ $item->total_qty }}</td>
                                            <td class="text-end">₱{{ number_format($item->revenue, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line me-2"></i>Top Selling This Year <span class="badge bg-secondary ms-2">Top 5</span></h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSellingYearly as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td class="text-end">{{ $item->total_qty }}</td>
                                            <td class="text-end">₱{{ number_format($item->revenue, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
>


    <!-- Analytics Charts Row -->
    <div class="row mb-4">
        <!-- Product Distribution Pie Chart -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Product Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="productDistributionChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Stock Level Distribution -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group me-2"></i>
                        Stock Level Status
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="stockLevelChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Stock Value by Category -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Top 5 Categories by Value
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="stockValueChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity moved to bottom --}}

    <!-- Products Table -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <form method="GET" action="{{ route('reports.inventory') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label mb-0">Animal Type</label>
                    <select name="animal_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($animalTypes as $type)
                            <option value="{{ $type }}" {{ request('animal_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-0">Stock Status</label>
                    <select name="stock_status" class="form-select">
                        <option value="">All Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-0">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-0">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="font-weight: 600; font-size: 1rem; padding: 10px 20px;">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('reports.inventory') }}" class="btn btn-secondary" style="font-weight: 600; font-size: 1rem; padding: 10px 20px;">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
        </div>

        <!-- Top Selling Products moved beside Expiring Products -->

        <div class="col-12">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i>
                        Product Inventory Details
                        <span class="badge bg-secondary ms-2">{{ $products->count() }} items</span>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter card-table mb-0">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Code</th>
                                    <th>Animal Type</th>
                                    <th>Cut Type</th>
                                    <th>Price/kg</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Expiration</th>
                                    <th>Last Updated By</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productsByBatch as $batchDate => $batchProducts)
                                    <tr>
                                        <td colspan="10" class="bg-light">
                                            <strong>Batch:</strong> {{ $batchDate ?? 'Unknown' }}
                                        </td>
                                    </tr>
                                    @foreach($batchProducts as $product)
                                    <tr>
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td><code>{{ $product->code }}</code></td>
                                        <td>
                                            <span class="badge bg-primary">{{ $product->meatCut->animal_type ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $product->meatCut->name ?? 'N/A' }}</td>
                                        <td>₱{{ number_format($product->price_per_kg, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $product->quantity > ($product->quantity_alert ?? 10) ? 'success' : ($product->quantity > 0 ? 'warning' : 'danger') }}">
                                                {{ $product->quantity }} {{ $product->unit->name ?? 'pcs' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($product->quantity <= 0)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                </span>
                                            @elseif($product->quantity <= ($product->quantity_alert ?? 10))
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>In Stock
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->expiration_date)
                                                @php
                                                    $expirationDate = \Carbon\Carbon::parse($product->expiration_date);
                                                    $daysUntilExpiry = now()->diffInDays($expirationDate, false);
                                                @endphp
                                                @if($daysUntilExpiry < 0)
                                                    <span class="badge bg-dark">
                                                        <i class="fas fa-skull-crossbones me-1"></i>Expired
                                                    </span>
                                                @elseif($daysUntilExpiry <= 7)
                                                    <span class="badge bg-danger expiring-badge">
                                                        <i class="fas fa-clock me-1"></i>{{ ceil($daysUntilExpiry) }}d left
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        {{ $expirationDate->format('M d, Y') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->updatedByUser)
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm me-2" style="background-color: var(--primary-color); color: white;">
                                                        {{ strtoupper(substr($product->updatedByUser->name, 0, 2)) }}
                                                    </span>
                                                    <div>
                                                        <strong>{{ $product->updatedByUser->name }}</strong>
                                                        <div class="small text-muted">{{ ucfirst($product->updatedByUser->role) }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="small">{{ $product->updated_at->format('M d, Y') }}</span>
                                            <div class="small text-muted">{{ $product->updated_at->format('h:i A') }}</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No products found matching your criteria</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Expired Products History -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title"><i class="fas fa-skull-crossbones me-2"></i>Expired Products History</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>Animal Type</th>
                                    <th>Cut</th>
                                    <th class="text-end">Quantity</th>
                                    <th>Expired On</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiredProducts as $product)
                                <tr class="table-dark">
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>{{ $product->meatCut->animal_type ?? 'N/A' }}</td>
                                    <td>{{ $product->meatCut->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ $product->quantity }} {{ $product->unit->name ?? 'pcs' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($product->expiration_date)->format('M d, Y') }}</td>
                                    <td><span class="badge bg-dark"><i class="fas fa-times me-1"></i>Expired</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x d-block mb-2"></i>
                                        No expired products
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Stock Activity moved to bottom -->
    <div class="row mb-4">
        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history me-2"></i>
                        Recent Stock Activity (Last 7 Days)
                    </h3>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                        <div class="activity-item {{ $activity->action }}">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $activity->product->name ?? 'Unknown Product' }}</strong>
                                    <span class="badge bg-{{ $activity->action === 'created' ? 'success' : ($activity->action === 'updated' ? 'info' : 'danger') }} ms-2">
                                        {{ ucfirst($activity->action) }}
                                    </span>
                                    <div class="small text-muted">
                                        by {{ $activity->staff->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

@push('page-scripts')
<script>

    // Product Distribution Pie Chart
    const distributionCtx = document.getElementById('productDistributionChart').getContext('2d');
    const productDistributionChart = new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($productDistribution->keys()) !!},
            datasets: [{
                data: {!! json_encode($productDistribution->values()) !!},
                backgroundColor: [
                    'rgba(139, 0, 0, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Stock Level Distribution Chart
    const stockLevelCtx = document.getElementById('stockLevelChart').getContext('2d');
    const stockLevelChart = new Chart(stockLevelCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($stockLevelDistribution)) !!},
            datasets: [{
                data: {!! json_encode(array_values($stockLevelDistribution)) !!},
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // Green - In Stock
                    'rgba(255, 193, 7, 0.8)',    // Yellow - Low Stock
                    'rgba(220, 53, 69, 0.8)'     // Red - Out of Stock
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Stock Value by Category Chart
    const stockValueCtx = document.getElementById('stockValueChart').getContext('2d');
    const stockValueChart = new Chart(stockValueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($stockValueByCategory->keys()) !!},
            datasets: [{
                label: 'Stock Value (₱)',
                data: {!! json_encode($stockValueByCategory->values()) !!},
                backgroundColor: 'rgba(139, 0, 0, 0.8)',
                borderColor: 'rgb(139, 0, 0)',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.x.toLocaleString();
                        }
                    }
                }
            }
        }
    });


    // Auto-refresh analytics every 30 seconds
    setInterval(function() {
        fetch('{{ route("reports.inventory.analytics") }}')
            .then(response => response.json())
            .then(data => {
                console.log('Analytics updated:', data.last_updated);
                // Update charts with new data if needed
            });
    }, 30000);
</script>
@endpush
@endsection
