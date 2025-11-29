@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-2" style="color: #8B0000;"></i>Income Report
            </h1>
            <p class="text-muted">Sales minus Expenses - Comprehensive profit and loss analysis</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Reports
            </a>
            <button onclick="window.print()" class="btn btn-info me-2">
                <i class="fas fa-print me-1"></i>Print
            </button>
            <a href="{{ route('reports.income.export-csv', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-success">
                <i class="fas fa-file-csv me-1"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.income') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('reports.income') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-dollar-sign me-1"></i>Total Sales</h6>
                    <h3 class="text-end">₱{{ number_format($totalSales, 2) }}</h3>
                    <small>Revenue from completed orders</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-money-bill-wave me-1"></i>Total Expenses</h6>
                    <h3 class="text-end">₱{{ number_format($totalExpenses, 2) }}</h3>
                    <small>All business expenses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $netIncome >= 0 ? 'bg-success' : 'bg-warning' }} text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-chart-line me-1"></i>Net Income</h6>
                    <h3 class="text-end">₱{{ number_format($netIncome, 2) }}</h3>
                    <small>Sales - Expenses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-percent me-1"></i>Profit Margin</h6>
                    <h3 class="text-end">{{ number_format($profitMargin, 2) }}%</h3>
                    <small>Income / Sales ratio</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Expense Breakdown Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Expense Breakdown</h5>
                </div>
                <div class="card-body">
                    <canvas id="expenseBreakdownChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Sales Trend Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Daily Sales Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Monthly Income Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Sales</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-end">Net Income</th>
                            <th class="text-end">Profit Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyData as $data)
                        <tr>
                            <td><strong>{{ $data['month'] }}</strong></td>
                            <td class="text-end">₱{{ number_format($data['sales'], 2) }}</td>
                            <td class="text-end text-danger">₱{{ number_format($data['expenses'], 2) }}</td>
                            <td class="text-end {{ $data['income'] >= 0 ? 'text-success' : 'text-warning' }}">
                                <strong>₱{{ number_format($data['income'], 2) }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="badge {{ $data['margin'] >= 30 ? 'bg-success' : ($data['margin'] >= 15 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($data['margin'], 2) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No data available for selected period</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td><strong>TOTAL</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalSales, 2) }}</strong></td>
                            <td class="text-end text-danger"><strong>₱{{ number_format($totalExpenses, 2) }}</strong></td>
                            <td class="text-end {{ $netIncome >= 0 ? 'text-success' : 'text-warning' }}">
                                <strong>₱{{ number_format($netIncome, 2) }}</strong>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($profitMargin, 2) }}%</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Expense Breakdown Chart
const expenseCtx = document.getElementById('expenseBreakdownChart').getContext('2d');
new Chart(expenseCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($expenseBreakdown)) !!},
        datasets: [{
            data: {!! json_encode(array_values($expenseBreakdown)) !!},
            backgroundColor: [
                '#ffc107', // Utilities - Yellow
                '#28a745', // Payroll - Green
                '#17a2b8'  // Other - Blue
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ₱' + context.parsed.toLocaleString('en-PH', {minimumFractionDigits: 2});
                    }
                }
            }
        }
    }
});

// Daily Sales Trend Chart
const dailyCtx = document.getElementById('dailySalesTrendChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($dailyTrend, 'date')) !!},
        datasets: [{
            label: 'Daily Sales',
            data: {!! json_encode(array_column($dailyTrend, 'sales')) !!},
            borderColor: '#8B0000',
            backgroundColor: 'rgba(139, 0, 0, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Sales: ₱' + context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2});
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString('en-PH');
                    }
                }
            }
        }
    }
});
</script>

<style>
@media print {
    .btn, .card-header .btn-group, .form-control {
        display: none !important;
    }
    
    .card {
        page-break-inside: avoid;
    }
}
</style>
@endsection
