@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            <i class="fas fa-truck text-warning me-2"></i>
                            Supplier Analytics
                        </h2>
                        <div class="text-muted mt-1">Supplier performance, delivery tracking, and procurement insights</div>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="action-buttons">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Reports
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <button onclick="refreshData()" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$hasProcurementData)
    <!-- Simulated Data Notice -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h4 class="alert-heading mb-1">Preview Mode - Simulated Data</h4>
                <p class="mb-0">No actual procurement records found. The analytics shown below are simulated examples for two suppliers: <strong>Manila Premium Meat Suppliers</strong> and <strong>Laguna Fresh Meat Distributors</strong>. Start recording procurements to see real analytics.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-truck"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">{{ $suppliers->count() }}</div>
                            <div class="text-muted">Total Suppliers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">{{ $deliveryTracking['on_time_percentage'] }}%</div>
                            <div class="text-muted">On-Time Delivery</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <i class="fas fa-boxes"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">{{ number_format($procurementInsights['total_procurements']) }}</div>
                            <div class="text-muted">Total Procurements</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">₱{{ number_format($procurementInsights['total_cost'], 2) }}</div>
                            <div class="text-muted">Total Cost</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Monthly Procurement Cost Trends -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Monthly Procurement Cost Trends
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Delivery Performance -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shipping-fast me-2"></i>
                        Delivery Performance
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="deliveryChart" height="200"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success"><i class="fas fa-circle"></i> On-Time</span>
                            <strong>{{ $deliveryTracking['on_time'] }} ({{ $deliveryTracking['on_time_percentage'] }}%)</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-danger"><i class="fas fa-circle"></i> Delayed</span>
                            <strong>{{ $deliveryTracking['delayed'] }} ({{ $deliveryTracking['delayed_percentage'] }}%)</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Suppliers -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Top Suppliers by Total Cost
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="topSuppliersChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Procurement Insights -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb me-2 text-info"></i>
                        Procurement Insights
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-dollar-sign text-success me-2"></i>
                                Average Cost per Procurement
                            </div>
                            <strong class="badge bg-success">₱{{ number_format($procurementInsights['avg_cost'], 2) }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-boxes text-primary me-2"></i>
                                Total Quantity Supplied
                            </div>
                            <strong class="badge bg-primary">{{ number_format($procurementInsights['total_quantity']) }} units</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-line text-{{ $procurementInsights['trend_direction'] == 'up' ? 'success' : 'danger' }} me-2"></i>
                                30-Day Trend
                            </div>
                            <strong class="badge bg-{{ $procurementInsights['trend_direction'] == 'up' ? 'success' : 'danger' }}">
                                {{ $procurementInsights['trend_direction'] == 'up' ? '↑' : '↓' }} 
                                {{ abs($procurementInsights['trend_percentage']) }}%
                            </strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar-check text-info me-2"></i>
                                Active Suppliers
                            </div>
                            <strong class="badge bg-info">{{ $suppliers->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Supplier Performance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table me-2"></i>
                        Detailed Supplier Performance
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="supplierPerformanceTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Supplier</th>
                                    <th>Total Deliveries</th>
                                    <th>On-Time %</th>
                                    <th>Avg Delay (Days)</th>
                                    <th>Defective %</th>
                                    <th>Total Cost</th>
                                    <th>Performance Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($performanceData as $performance)
                                <tr>
                                    <td>
                                        <i class="fas fa-store me-2 text-primary"></i>
                                        <strong>{{ $performance['supplier_name'] }}</strong>
                                    </td>
                                    <td>{{ $performance['total_deliveries'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $performance['on_time_rate'] >= 80 ? 'success' : ($performance['on_time_rate'] >= 60 ? 'warning' : 'danger') }}">
                                            {{ $performance['on_time_rate'] }}%
                                        </span>
                                    </td>
                                    <td>{{ $performance['avg_delay_days'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $performance['avg_defective_rate'] <= 2 ? 'success' : ($performance['avg_defective_rate'] <= 5 ? 'warning' : 'danger') }}">
                                            {{ $performance['avg_defective_rate'] }}%
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($performance['total_cost'], 2) }}</td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-{{ $performance['performance_score'] >= 80 ? 'success' : ($performance['performance_score'] >= 60 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $performance['performance_score'] }}%"
                                                 aria-valuenow="{{ $performance['performance_score'] }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $performance['performance_score'] }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No supplier performance data available
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
</div>

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Procurement Cost Trends Chart
    const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    const monthlyTrendsChart = new Chart(monthlyTrendsCtx, {
        type: 'line',
        data: {
            labels: @json($monthlySupplierTrends['months']),
            datasets: @json($monthlySupplierDatasets),
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Total Cost (₱)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Procurement Count'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.datasetIndex === 0) {
                                    label += '₱' + context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2});
                                } else {
                                    label += context.parsed.y.toLocaleString();
                                }
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Delivery Performance Chart (Doughnut)
    const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
    const deliveryChart = new Chart(deliveryCtx, {
        type: 'doughnut',
        data: {
            labels: ['On-Time', 'Delayed'],
            datasets: [{
                data: [{{ $deliveryTracking['on_time'] }}, {{ $deliveryTracking['delayed'] }}],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgb(40, 167, 69)',
                    'rgb(220, 53, 69)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(2);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Top Suppliers Chart (Horizontal Bar)
    const topSuppliersCtx = document.getElementById('topSuppliersChart').getContext('2d');
    const topSuppliersChart = new Chart(topSuppliersCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topSuppliers->pluck('name')) !!},
            datasets: [{
                label: 'Total Spent (₱)',
                data: {!! json_encode($topSuppliers->pluck('total_spent')) !!},
                backgroundColor: 'rgba(255, 193, 7, 0.6)',
                borderColor: 'rgb(255, 193, 7)',
                borderWidth: 2
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.x.toLocaleString('en-PH', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Cost (₱)'
                    }
                }
            }
        }
    });

    // Refresh Data Function
    function refreshData() {
        location.reload();
    }
</script>
@endpush
@endsection
