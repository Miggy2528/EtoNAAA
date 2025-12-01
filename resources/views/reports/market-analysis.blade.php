@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Market Analysis</h2>
                        <div class="text-muted mt-1">Profitable meat types, popular preparations, and customer insights</div>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <button onclick="window.print()" class="btn btn-white d-none d-sm-inline-block">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                            <button onclick="exportData()" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Export Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-lightbulb me-2"></i>Key Insights</h3>
                </div>
                <div class="card-body">
                    @if(isset($analysisData['insights']) && count($analysisData['insights']) > 0)
                        <ul class="mb-0">
                            @foreach($analysisData['insights'] as $insight)
                                <li>{{ $insight }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0">No insights available at this time.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Profitable Meat Types -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Most Profitable Meat Types</h3>
                </div>
                <div class="card-body">
                    @if(isset($analysisData['profitable_meat_types']) && count($analysisData['profitable_meat_types']) > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Meat Type</th>
                                                <th class="text-end">Total Revenue (₱)</th>
                                                <th class="text-end">Quantity Sold</th>
                                                <th class="text-center">Orders</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analysisData['profitable_meat_types'] as $meatType)
                                                <tr>
                                                    <td><span class="badge bg-primary">{{ $meatType['meat_type'] }}</span></td>
                                                    <td class="text-end">₱{{ number_format($meatType['total_revenue'], 2) }}</td>
                                                    <td class="text-end">{{ number_format($meatType['total_quantity']) }}</td>
                                                    <td class="text-center">{{ number_format($meatType['order_count']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No data available for profitable meat types.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Preparations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-fire me-2 text-success"></i>Most Popular Preparations</h3>
                </div>
                <div class="card-body">
                    @if(isset($analysisData['popular_preparations']) && count($analysisData['popular_preparations']) > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Preparation Method</th>
                                                <th class="text-end">Quantity Sold</th>
                                                <th class="text-end">Total Revenue (₱)</th>
                                                <th class="text-center">Orders</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analysisData['popular_preparations'] as $preparation)
                                                <tr>
                                                    <td><span class="badge bg-success">{{ $preparation['preparation_type'] }}</span></td>
                                                    <td class="text-end">{{ number_format($preparation['total_quantity']) }}</td>
                                                    <td class="text-end">₱{{ number_format($preparation['total_revenue'], 2) }}</td>
                                                    <td class="text-center">{{ number_format($preparation['order_count']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No data available for popular preparations.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Location Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location-Based Analysis</h3>
                </div>
                <div class="card-body">
                    @if(isset($analysisData['location_analysis']) && count($analysisData['location_analysis']) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Barangay</th>
                                        <th class="text-center">Order Count</th>
                                        <th class="text-end">Total Revenue (₱)</th>
                                        <th class="text-end">Avg. Order Value (₱)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analysisData['location_analysis'] as $location)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $location['barangay'] }}</span>
                                            </td>
                                            <td class="text-center">{{ number_format($location['order_count']) }}</td>
                                            <td class="text-end">₱{{ number_format($location['total_revenue'], 2) }}</td>
                                            <td class="text-end">₱{{ number_format($location['average_order_value'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No location data available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Demographic Analysis -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users me-2"></i>Customer Demographics</h3>
                </div>
                <div class="card-body">
                    @if(isset($analysisData['demographic_analysis']))
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">Customer Base</h4>
                                        <div class="display-4 text-primary">{{ number_format($analysisData['demographic_analysis']['total_customers']) }}</div>
                                        <p class="text-muted">Total Customers</p>
                                        <div class="mt-3">
                                            <span class="badge bg-success">{{ number_format($analysisData['demographic_analysis']['active_customers']) }} Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">Spending Patterns</h4>
                                        <div class="display-4 text-success">₱{{ number_format($analysisData['demographic_analysis']['customer_spending']['average_order_value'], 2) }}</div>
                                        <p class="text-muted">Avg. Order Value</p>
                                        <div class="mt-3">
                                            <span class="badge bg-info">{{ number_format($analysisData['demographic_analysis']['customer_spending']['total_orders']) }} Orders</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p>No demographic data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function exportData() {
    window.location.href = "{{ route('reports.market.analysis.export') }}";
}

// Always execute this JavaScript to ensure DOM elements are handled
console.log('Initializing market analysis charts');



// Print styles
@media print {
    .page-header, .btn-list {
        display: none !important;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .table {
        font-size: 12px;
    }
    
    .badge {
        color: #000 !important;
        background: none !important;
        border: 1px solid #000;
    }
    
    canvas {
        display: none;
    }
}
</script>
@endpush