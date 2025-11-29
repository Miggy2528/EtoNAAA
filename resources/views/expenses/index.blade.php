@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-money-bill-wave me-2"></i>Expense Management
            </h1>
            <p class="text-muted">Track utilities, payroll, and other business expenses</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.voided') }}" class="btn btn-outline-danger me-2">
                <i class="fas fa-ban me-1"></i>View Voided Expenses
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Reports
            </a>
        </div>
    </div>

    <!-- Monthly Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-calendar-alt me-1"></i>This Month</h6>
                    <h3>₱{{ number_format($monthlyTotal, 2) }}</h3>
                    <small>Total Expenses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-bolt me-1"></i>Utilities</h6>
                    <h3>₱{{ number_format($monthlyUtilities, 2) }}</h3>
                    <small class="d-block">This Month</small>
                    @if($pendingUtilities > 0)
                    <span class="badge badge-danger mt-2">{{ $pendingUtilities }} Pending</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-users me-1"></i>Payroll</h6>
                    <h3>₱{{ number_format($monthlyPayroll, 2) }}</h3>
                    <small class="d-block">This Month</small>
                    @if($pendingPayroll > 0)
                    <span class="badge badge-danger mt-2">{{ $pendingPayroll }} Pending</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-receipt me-1"></i>Other</h6>
                    <h3>₱{{ number_format($monthlyOther, 2) }}</h3>
                    <small>This Month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Yearly Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>{{ date('Y') }} Total Expenses: ₱{{ number_format($yearlyTotal, 2) }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6>Utilities</h6>
                                <h4 class="text-warning">₱{{ number_format($yearlyUtilities, 2) }}</h4>
                                <small class="text-muted">{{ $yearlyTotal > 0 ? round(($yearlyUtilities / $yearlyTotal) * 100, 1) : 0 }}% of total</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6>Payroll</h6>
                                <h4 class="text-success">₱{{ number_format($yearlyPayroll, 2) }}</h4>
                                <small class="text-muted">{{ $yearlyTotal > 0 ? round(($yearlyPayroll / $yearlyTotal) * 100, 1) : 0 }}% of total</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6>Other Expenses</h6>
                                <h4 class="text-info">₱{{ number_format($yearlyOther, 2) }}</h4>
                                <small class="text-muted">{{ $yearlyTotal > 0 ? round(($yearlyOther / $yearlyTotal) * 100, 1) : 0 }}% of total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Management Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Utilities</h5>
                </div>
                <div class="card-body">
                    <p>Manage electricity, water, rent, internet, and other utility bills.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Track billing periods</li>
                        <li><i class="fas fa-check text-success me-2"></i>Monitor payment status</li>
                        <li><i class="fas fa-check text-success me-2"></i>Upload receipts</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('expenses.utilities.index') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-cog me-1"></i>Manage Utilities
                    </a>
                    <a href="{{ route('expenses.utilities.create') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-plus me-1"></i>Add New
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Payroll</h5>
                </div>
                <div class="card-body">
                    <p>Manage staff salaries, bonuses, and deductions.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Monthly payroll records</li>
                        <li><i class="fas fa-check text-success me-2"></i>Salary calculations</li>
                        <li><i class="fas fa-check text-success me-2"></i>Payment tracking</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('expenses.payroll.index') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-cog me-1"></i>Manage Payroll
                    </a>
                    <a href="{{ route('expenses.payroll.create') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Record
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Other Expenses</h5>
                </div>
                <div class="card-body">
                    <p>Track supplies, maintenance, marketing, and miscellaneous expenses.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Categorize expenses</li>
                        <li><i class="fas fa-check text-success me-2"></i>Attach receipts</li>
                        <li><i class="fas fa-check text-success me-2"></i>Date tracking</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('expenses.other.index') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-cog me-1"></i>Manage Expenses
                    </a>
                    <a href="{{ route('expenses.other.create') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Expense
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Expense Trends (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="expenseTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Payments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUtilities as $util)
                                <tr>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'electricity' => 'bg-warning text-dark',
                                                'water' => 'bg-info text-white',
                                                'internet' => 'bg-primary text-white',
                                                'rent' => 'bg-secondary text-white',
                                                'telephone' => 'bg-success text-white',
                                            ];
                                            $colorClass = $typeColors[strtolower($util->type)] ?? 'bg-dark text-white';
                                        @endphp
                                        <span class="badge {{ $colorClass }} rounded-pill">
                                            <i class="fas fa-bolt me-1"></i>{{ ucfirst($util->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($util->notes)
                                            {{ $util->notes }}
                                        @elseif($util->description)
                                            {{ $util->description }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><strong class="text-success">₱{{ number_format($util->amount, 2) }}</strong></td>
                                    <td>
                                        @if($util->status === 'paid')
                                            <span class="badge bg-success text-white rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </span>
                                        @else
                                            <span class="badge bg-danger text-white rounded-pill">
                                                <i class="fas fa-exclamation-circle me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent utility expenses</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>Recent Payroll</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayroll as $payroll)
                                <tr>
                                    <td>
                                        @if($payroll->staff)
                                            <strong class="text-primary">{{ $payroll->staff->name }}</strong>
                                            <br><small class="badge bg-light text-dark">{{ $payroll->staff->position }}</small>
                                        @elseif($payroll->user)
                                            <strong>{{ $payroll->user->name }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $payroll->month_name }} {{ $payroll->year }}</small></td>
                                    <td><strong class="text-success">₱{{ number_format($payroll->total_salary, 2) }}</strong></td>
                                    <td>
                                        @if($payroll->status === 'paid')
                                            <span class="badge bg-success text-white rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </span>
                                        @else
                                            <span class="badge bg-danger text-white rounded-pill">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent payroll records</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('expenseTrendChart').getContext('2d');
    const monthlyData = @json($monthlyBreakdown);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Utilities',
                data: monthlyData.map(d => d.utilities),
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4
            }, {
                label: 'Payroll',
                data: monthlyData.map(d => d.payroll),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }, {
                label: 'Other',
                data: monthlyData.map(d => d.other),
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2});
                        }
                    }
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
</script>
@endpush
@endsection
