@extends('layouts.butcher')

@push('page-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        border-left: 4px solid var(--primary-color);
    }
    .top-performer-card {
        border-left: 4px solid #28a745;
    }
    .needs-improvement-card {
        border-left: 4px solid #ffc107;
    }
    
    /* Additional styles for performance metrics table */
    .thead-light th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-top: none;
        font-size: 0.875rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        padding: 1rem 0.75rem;
    }
    
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .bg-primary-lt {
        background-color: rgba(139, 0, 0, 0.1);
        color: #8B0000; /* Dark red for better contrast */
    }
    
    .bg-secondary-lt {
        background-color: rgba(108, 117, 125, 0.1);
        color: #495057; /* Dark gray for better contrast */
    }
    
    .input-icon {
        position: relative;
    }
    
    .input-icon .form-control {
        padding-left: 2.5rem;
        border-radius: 0.375rem;
    }
    
    .input-icon .input-icon-addon {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        pointer-events: none;
        color: #6c757d;
    }
    
    .card.border-0.shadow-sm {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .table th {
        font-weight: 600;
    }
    
    .performance-metric-value {
        font-weight: 600;
        font-size: 1.1rem;
        color: #212529;
    }
    
    .performance-metric-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .performance-badge {
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 1rem;
    }
    
    /* Enhanced color coding for accessibility */
    .bg-success-high-contrast {
        background-color: #28a745; /* Standard green */
        color: white;
    }
    
    .bg-warning-high-contrast {
        background-color: #ffc107; /* Standard yellow */
        color: #212529; /* Dark text for contrast */
    }
    
    .bg-danger-high-contrast {
        background-color: #dc3545; /* Standard red */
        color: white;
    }
    
    .bg-light-high-contrast {
        background-color: #f8f9fa;
        color: #212529;
        border: 1px solid #dee2e6;
    }
    
    .bg-light-performance {
        background-color: #e9ecef;
        color: #495057;
        border: 1px solid #adb5bd;
    }
    
    /* Focus styles for accessibility */
    .sort-column:focus {
        outline: 2px solid #8B0000;
        outline-offset: 2px;
    }
    
    .btn:focus, .form-control:focus {
        outline: 2px solid #8B0000;
        outline-offset: 2px;
    }
    
    /* Export button styles */
    .btn-group .btn {
        border-radius: 0 !important;
    }
    
    .btn-group .btn:first-child {
        border-radius: 0.375rem 0 0 0.375rem !important;
    }
    
    .btn-group .btn:last-child {
        border-radius: 0 0.375rem 0.375rem 0 !important;
    }
    
    /* Sortable column headers */
    .sort-column {
        color: #495057;
        transition: color 0.2s;
    }
    
    .sort-column:hover {
        color: var(--primary-color);
    }
    
    .sort-column:hover i {
        color: var(--primary-color) !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767px) {
        .table-responsive {
            border: none;
        }
        
        #performanceMetricsTable td {
            padding: 0.75rem;
        }
        
        .progress {
            height: 6px !important;
        }
    }
    
    /* Additional padding for better spacing */
    #performanceMetricsTable td {
        padding: 1rem 0.75rem;
    }
    
    /* Action column padding */
    .action-column {
        padding-right: 1rem !important;
    }
    
    #performanceMetricsTable td.text-end {
        padding-right: 1rem !important;
    }
    
    @media (max-width: 576px) {
        .card-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .input-icon {
            width: 100%;
        }
        
        .input-icon .form-control {
            padding-left: 2.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-chart-bar me-2"></i>Staff Performance Report
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                <i class="fas fa-users me-1"></i>
                View Staff
            </a>
            <a href="{{ route('staff-performance.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i>
                All Records
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-users"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">{{ $staffAverages->count() }}</div>
                            <div class="text-muted">Total Staff</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-chart-line"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0 text-success">
                                {{ number_format($staffAverages->avg('avg_performance'), 1) }}%
                            </div>
                            <div class="text-muted">Average Performance</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">{{ $processedMonthlyTrends->count() }}</div>
                            <div class="text-muted">Months Evaluated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top and Bottom Performers -->
    <div class="row mb-4">
        <!-- Top Performers -->
        <div class="col-md-6">
            <div class="card top-performer-card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Top 3 Performers
                    </h3>
                </div>
                <div class="card-body">
                    @if($topPerformers->isEmpty())
                        <p class="text-muted text-center">No performance data available</p>
                    @else
                        @foreach($topPerformers as $index => $performer)
                            <div class="d-flex align-items-center mb-3 {{ $loop->last ? '' : 'pb-3 border-bottom' }}">
                                <div class="flex-grow-1">
                                    @if($performer->staff)
                                        <strong class="d-block">{{ $performer->staff->name }}</strong>
                                        <small class="text-muted">{{ $performer->staff->position }}</small>
                                    @else
                                        <strong class="d-block">Unknown Staff</strong>
                                        <small class="text-muted">Position not available</small>
                                    @endif
                                </div>
                                @php
                                    $score = $performer->avg_performance;
                                    $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $color }} fs-4">
                                    {{ number_format($score, 1) }}%
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Performers -->
        <div class="col-md-6">
            <div class="card needs-improvement-card">
                <div class="card-header bg-warning">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Needs Improvement
                    </h3>
                </div>
                <div class="card-body">
                    @if($bottomPerformers->isEmpty())
                        <p class="text-muted text-center">No performance data available</p>
                    @else
                        @foreach($bottomPerformers as $index => $performer)
                            <div class="d-flex align-items-center mb-3 {{ $loop->last ? '' : 'pb-3 border-bottom' }}">
                                <div class="flex-grow-1">
                                    @if($performer->staff)
                                        <strong class="d-block">{{ $performer->staff->name }}</strong>
                                        <small class="text-muted">{{ $performer->staff->position }}</small>
                                    @else
                                        <strong class="d-block">Unknown Staff</strong>
                                        <small class="text-muted">Position not available</small>
                                    @endif
                                </div>
                                @php
                                    $score = $performer->avg_performance;
                                    $color = $score >= 60 ? 'warning' : 'danger';
                                @endphp
                                <span class="badge bg-{{ $color }} fs-4">
                                    {{ number_format($score, 1) }}%
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Staff Performance Bar Chart -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Average Performance by Staff
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="staffPerformanceChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Trend Line Chart -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Monthly Performance Trends
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Metrics -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-table me-2 text-primary"></i>
                            Detailed Performance Metrics
                        </h3>
                        <div class="d-flex">
                            <div class="input-icon">
                                <input type="text" class="form-control" placeholder="Search staff..." id="staffSearch">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter card-table mb-0" id="performanceMetricsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center d-none d-md-table-cell" style="width: 5%">
                                        <a href="#" class="text-decoration-none sort-column" data-column="rank">
                                            #
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        </a>
                                    </th>
                                    <th style="width: 25%">
                                        <a href="#" class="text-decoration-none sort-column" data-column="name">
                                            Staff Name
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        </a>
                                    </th>
                                    <th class="d-none d-lg-table-cell" style="width: 20%">
                                        <a href="#" class="text-decoration-none sort-column" data-column="position">
                                            Position
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        </a>
                                    </th>
                                    <th style="width: 25%">
                                        <a href="#" class="text-decoration-none sort-column" data-column="performance">
                                            Average Performance
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        </a>
                                    </th>
                                    <th style="width: 15%">
                                        <a href="#" class="text-decoration-none sort-column" data-column="grade">
                                            Grade
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        </a>
                                    </th>
                                    <th class="text-end action-column" style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffAverages as $index => $staffAvg)
                                    @php
                                        $score = $staffAvg->avg_performance;
                                        $color = $score >= 80 ? 'success-high-contrast' : ($score >= 60 ? 'warning-high-contrast' : 'danger-high-contrast');
                                        $grade = $score >= 90 ? 'Excellent' : ($score >= 80 ? 'Very Good' : ($score >= 70 ? 'Good' : ($score >= 60 ? 'Satisfactory' : 'Needs Improvement')));
                                        // Determine rank badge color based on position
                                        $rankColor = $index == 0 ? 'bg-warning text-dark' : ($index == 1 ? 'bg-secondary' : ($index == 2 ? 'bg-info' : 'bg-light-high-contrast'));
                                    @endphp
                                    <tr>
                                        <td class="text-center d-none d-md-table-cell">
                                            <span class="badge {{ $rankColor }} rounded-pill performance-badge">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            @if($staffAvg->staff)
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar me-2 bg-primary-lt">{{ substr($staffAvg->staff->name, 0, 1) }}</span>
                                                    <div>
                                                        <a href="{{ route('staff.show', $staffAvg->staff) }}" class="text-decoration-none fw-medium">
                                                            {{ $staffAvg->staff->name }}
                                                        </a>
                                                        <div class="d-md-none small text-muted mt-1">
                                                            <span class="badge bg-light-performance performance-badge">{{ $staffAvg->staff->position }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar me-2 bg-secondary-lt">U</span>
                                                    <div>
                                                        <span class="fw-medium">Unknown Staff</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            @if($staffAvg->staff)
                                                <span class="badge bg-light-performance performance-badge">{{ $staffAvg->staff->position }}</span>
                                            @else
                                                <span class="text-muted performance-metric-label">Position not available</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="w-100">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-{{ str_replace('-high-contrast', '', $color) }}" style="width: {{ $score }}%"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2 align-items-center flex-wrap">
                                                        <span class="performance-metric-value">
                                                            {{ number_format($score, 1) }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-{{ $color }} performance-badge me-2">{{ $grade }}</span>
                                                @if($score >= 90)
                                                    <i class="fas fa-crown text-warning" title="Top Performer"></i>
                                                @elseif($score >= 80)
                                                    <i class="fas fa-medal text-info" title="High Performer"></i>
                                                @elseif($score < 60)
                                                    <i class="fas fa-exclamation-triangle text-danger" title="Needs Improvement"></i>
                                                @else
                                                    <i class="fas fa-chart-line text-success" title="Good Performance"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            @if($staffAvg->staff)
                                                <a href="{{ route('staff.show', $staffAvg->staff) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="d-none d-lg-inline ms-1">View</span>
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="fas fa-eye"></i>
                                                    <span class="d-none d-lg-inline ms-1">View</span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-top-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $staffAverages->count() }} staff members
                        </div>
                        <div>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-secondary" id="exportCSV">
                                    <i class="fas fa-file-csv"></i>
                                    <span class="d-none d-sm-inline ms-1">CSV</span>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="exportExcel">
                                    <i class="fas fa-file-excel"></i>
                                    <span class="d-none d-sm-inline ms-1">Excel</span>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="exportPDF">
                                    <i class="fas fa-file-pdf"></i>
                                    <span class="d-none d-sm-inline ms-1">PDF</span>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="printTable">
                                    <i class="fas fa-print"></i>
                                    <span class="d-none d-sm-inline ms-1">Print</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    // Staff Performance Bar Chart
    const staffCtx = document.getElementById('staffPerformanceChart').getContext('2d');
    
    // Filter out null staff and prepare chart data
    const validStaffAverages = @json($staffAverages->filter(function($item) { return $item->staff !== null; }));
    const staffNames = validStaffAverages.map(item => item.staff.name);
    const staffPerformanceData = validStaffAverages.map(item => parseFloat(item.avg_performance));
    
    const staffPerformanceChart = new Chart(staffCtx, {
        type: 'bar',
        data: {
            labels: staffNames,
            datasets: [{
                label: 'Average Performance (%)',
                data: staffPerformanceData,
                backgroundColor: function(context) {
                    const value = context.parsed.y;
                    return value >= 80 ? 'rgba(40, 167, 69, 0.8)' : 
                           value >= 60 ? 'rgba(255, 193, 7, 0.8)' : 
                           'rgba(220, 53, 69, 0.8)';
                },
                borderColor: function(context) {
                    const value = context.parsed.y;
                    return value >= 80 ? 'rgb(40, 167, 69)' : 
                           value >= 60 ? 'rgb(255, 193, 7)' : 
                           'rgb(220, 53, 69)';
                },
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Performance: ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    });

    // Monthly Trend Line Chart
    const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    const monthlyTrendChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($processedMonthlyTrends->pluck('formatted_month')) !!},
            datasets: [
                {
                    label: 'Overall Performance',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_performance')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Attendance',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_attendance')) !!},
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Task Completion',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_task_completion')) !!},
                    borderColor: 'rgb(255, 159, 64)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Customer Feedback',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_feedback')->map(function($score) {
                        return round(($score / 5) * 100, 2); // Convert to percentage
                    })) !!},
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    tension: 0.1,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
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
                            return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    });
    
    // Simple search functionality for staff table
    document.getElementById('staffSearch').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#performanceMetricsTable tbody tr');
        
        tableRows.forEach(row => {
            const staffName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            if (staffName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Add hover effect to table rows
    document.querySelectorAll('#performanceMetricsTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Render sparklines for performance trends
    function renderSparklines() {
        document.querySelectorAll('.sparkline-canvas').forEach(canvas => {
            const ctx = canvas.getContext('2d');
            const staffId = canvas.dataset.staffId;
            
            // Generate mock data for demonstration
            // In a real application, this would come from actual performance data
            const data = [];
            for (let i = 0; i < 6; i++) {
                data.push(Math.floor(Math.random() * 40) + 60); // Random values between 60-100
            }
            
            // Create a simple line chart as sparkline
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array(data.length).fill(''),
                    datasets: [{
                        data: data,
                        borderColor: '#8B0000',
                        backgroundColor: 'rgba(139, 0, 0, 0.1)',
                        borderWidth: 2,
                        pointRadius: 0,
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
                            enabled: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: false,
                            min: 50,
                            max: 100
                        }
                    }
                }
            });
        });
    }
    
    // Call renderSparklines after the page loads
    document.addEventListener('DOMContentLoaded', function() {
        renderSparklines();
    });
    
    // Export functionality
    document.getElementById('exportCSV').addEventListener('click', function() {
        exportTableToCSV('staff-performance.csv');
    });
    
    document.getElementById('exportExcel').addEventListener('click', function() {
        alert('Excel export functionality would be implemented here. In a real application, this would generate an Excel file.');
    });
    
    document.getElementById('exportPDF').addEventListener('click', function() {
        alert('PDF export functionality would be implemented here. In a real application, this would generate a PDF file.');
    });
    
    document.getElementById('printTable').addEventListener('click', function() {
        window.print();
    });
    
    // Function to export table to CSV
    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll('#performanceMetricsTable tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [];
            const cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                // Skip action column
                if (j === cols.length - 1) {
                    continue;
                }
                
                let data = cols[j].innerText;
                // Remove icons and extra formatting
                data = data.replace(/\n/g, ' ').replace(/\s+/g, ' ').trim();
                data = data.replace(/\u00A0/g, ' '); // Replace non-breaking spaces
                
                // If data contains commas, wrap in quotes
                if (data.includes(',')) {
                    data = `"${data}"`;
                }
                
                row.push(data);
            }
            
            csv.push(row.join(','));
        }
        
        // Download CSV file
        const csvString = csv.join('\n');
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
    
    // Sorting functionality
    document.querySelectorAll('.sort-column').forEach(header => {
        header.addEventListener('click', function(e) {
            e.preventDefault();
            
            const column = this.getAttribute('data-column');
            const icon = this.querySelector('i');
            
            // Reset all icons
            document.querySelectorAll('.sort-column i').forEach(i => {
                i.className = 'fas fa-sort ms-1 text-muted';
            });
            
            // Set current sort direction (toggle between asc and desc)
            let direction = 'asc';
            if (icon.classList.contains('fa-sort-up')) {
                direction = 'desc';
                icon.className = 'fas fa-sort-down ms-1';
            } else {
                icon.className = 'fas fa-sort-up ms-1';
            }
            
            // Get table rows
            const table = document.getElementById('performanceMetricsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Sort rows based on column
            rows.sort((a, b) => {
                let aValue, bValue;
                
                switch(column) {
                    case 'rank':
                        aValue = parseInt(a.querySelector('td:first-child .badge').textContent);
                        bValue = parseInt(b.querySelector('td:first-child .badge').textContent);
                        break;
                    case 'name':
                        aValue = a.querySelector('td:nth-child(2) a').textContent.trim().toLowerCase();
                        bValue = b.querySelector('td:nth-child(2) a').textContent.trim().toLowerCase();
                        break;
                    case 'position':
                        aValue = a.querySelector('td:nth-child(3) .badge') ? 
                            a.querySelector('td:nth-child(3) .badge').textContent.trim().toLowerCase() : 
                            a.querySelector('td:nth-child(2) .small .badge').textContent.trim().toLowerCase();
                        bValue = b.querySelector('td:nth-child(3) .badge') ? 
                            b.querySelector('td:nth-child(3) .badge').textContent.trim().toLowerCase() : 
                            b.querySelector('td:nth-child(2) .small .badge').textContent.trim().toLowerCase();
                        break;
                    case 'performance':
                        aValue = parseFloat(a.querySelector('td:nth-child(4) .fw-medium').textContent);
                        bValue = parseFloat(b.querySelector('td:nth-child(4) .fw-medium').textContent);
                        break;
                    case 'grade':
                        // Convert grade text to numeric value for sorting
                        const gradeValues = {
                            'Excellent': 5,
                            'Very Good': 4,
                            'Good': 3,
                            'Satisfactory': 2,
                            'Needs Improvement': 1
                        };
                        const aGrade = a.querySelector('td:nth-child(5) .badge').textContent.trim();
                        const bGrade = b.querySelector('td:nth-child(5) .badge').textContent.trim();
                        aValue = gradeValues[aGrade];
                        bValue = gradeValues[bGrade];
                        break;
                    default:
                        return 0;
                }
                
                if (direction === 'asc') {
                    return aValue > bValue ? 1 : -1;
                } else {
                    return aValue < bValue ? 1 : -1;
                }
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        });
    });
</script>
@endpush
