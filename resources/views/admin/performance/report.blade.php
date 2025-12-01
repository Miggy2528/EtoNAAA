@extends('layouts.butcher')

@push('page-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        border-left: 4px solid var(--bs-primary);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
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
    
    /* Enhanced badge visibility */
    .badge {
        font-weight: 500;
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .text-primary-emphasis {
        color: #0b5ed7;
    }
    
    /* Enhanced rank badge visibility */
    .bg-warning.text-dark {
        background-color: #ffc107 !important;
        color: #212529 !important;
        font-weight: 600;
    }
    
    .bg-secondary {
        background-color: #6c757d !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    .bg-info {
        background-color: #0dcaf0 !important;
        color: #000 !important;
        font-weight: 600;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
        color: #212529 !important;
        border: 1px solid #dee2e6;
        font-weight: 600;
    }
    
    /* Focus styles for accessibility */
    .sort-column:focus {
        outline: 2px solid var(--bs-primary);
        outline-offset: 2px;
    }
    
    .btn:focus, .form-control:focus {
        outline: 2px solid var(--bs-primary);
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
        color: var(--bs-primary);
    }
    
    .sort-column:hover i {
        color: var(--bs-primary) !important;
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
        
        .chart-container {
            height: 250px !important;
        }
        
        .btn-group-vertical .btn {
            border-radius: 0 !important;
            border: 1px solid #dee2e6;
        }
        
        .btn-group-vertical .btn:first-child {
            border-radius: 0.375rem 0.375rem 0 0 !important;
        }
        
        .btn-group-vertical .btn:last-child {
            border-radius: 0 0 0.375rem 0.375rem !important;
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
        
        .chart-container {
            height: 200px !important;
        }
        
        /* Hide less important columns on very small screens */
        .d-md-none.d-lg-table-cell {
            display: none !important;
        }
        
        /* Adjust table cell padding for small screens */
        #performanceMetricsTable td {
            padding: 0.5rem;
        }
        
        /* Make action buttons stacked on small screens */
        .action-column .btn {
            display: block;
            width: 100%;
            margin-bottom: 0.25rem;
        }
        
        .action-column .btn:last-child {
            margin-bottom: 0;
        }
    }
    
    /* Chart container */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* Performance row hover effect */
    .performance-row:hover {
        background-color: rgba(139, 0, 0, 0.05);
    }
    
    /* Sticky header for table */
    .sticky-top {
        top: 0;
        z-index: 10;
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
            <button id="printReport" class="btn btn-info ms-2">
                <i class="fas fa-print me-1"></i>
                Print Report
            </button>
        </div>
    </div>

    <!-- Enhanced Summary Cards -->
    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Total Staff</h6>
                            <h2 class="mb-0">{{ $staffAverages->count() }}</h2>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge bg-success-subtle text-success-emphasis px-2 py-1">
                                    <i class="fas fa-user-check me-1"></i>
                                    <span class="d-none d-sm-inline">Active</span>
                                </span>
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Average Performance</h6>
                            <h2 class="mb-0 text-success">
                                {{ number_format($staffAverages->avg('avg_performance'), 1) }}%
                            </h2>
                            <div class="d-flex align-items-center mt-2">
                                @php
                                    $avgScore = $staffAverages->avg('avg_performance');
                                    $trendClass = $avgScore >= 80 ? 'text-success' : ($avgScore >= 60 ? 'text-warning' : 'text-danger');
                                    $trendIcon = $avgScore >= 80 ? 'fa-arrow-up' : ($avgScore >= 60 ? 'fa-minus' : 'fa-arrow-down');
                                @endphp
                                <span class="badge {{ str_replace('text', 'bg', $trendClass) }}-subtle {{ $trendClass }}-emphasis px-2 py-1">
                                    <i class="fas {{ $trendIcon }} me-1"></i>
                                    <span class="d-none d-sm-inline">{{ $avgScore >= 80 ? 'Excellent' : ($avgScore >= 60 ? 'Good' : 'Needs Attention') }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Months Evaluated</h6>
                            <h2 class="mb-0">{{ $processedMonthlyTrends->count() }}</h2>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge bg-info-subtle text-info-emphasis px-2 py-1">
                                    <i class="fas fa-history me-1"></i>
                                    <span class="d-none d-sm-inline">Last {{ $processedMonthlyTrends->count() }} months</span>
                                </span>
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                            <i class="fas fa-calendar fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Top and Bottom Performers -->
    <div class="row mb-4 g-4">
        <!-- Top Performers -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success bg-opacity-10 border-success border-opacity-25">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-25 p-2 rounded-circle me-3">
                            <i class="fas fa-trophy text-success"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 text-success">Top Performers</h5>
                            <small class="text-muted">Highest performing staff members</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($topPerformers->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No performance data available</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($topPerformers as $index => $performer)
                                <div class="list-group-item px-0 py-3 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $rankBadges = ['🥇', '🥈', '🥉'];
                                                    $rankBadge = $index < 3 ? $rankBadges[$index] : '#'.($index+1);
                                                @endphp
                                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <span class="text-success fw-bold">{!! $rankBadge !!}</span>
                                                </div>
                                            </div>
                                            <div>
                                                @if($performer->staff)
                                                    <h6 class="mb-0">{{ $performer->staff->name }}</h6>
                                                    <small class="text-muted">{{ $performer->staff->position }}</small>
                                                @else
                                                    <h6 class="mb-0">Unknown Staff</h6>
                                                    <small class="text-muted">Position not available</small>
                                                @endif
                                            </div>
                                        </div>
                                        @php
                                            $score = $performer->avg_performance;
                                            $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="fs-5 fw-bold text-{{ $color }} me-2">{{ number_format($score, 1) }}%</span>
                                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis d-none d-sm-inline">{{ $score >= 90 ? 'Outstanding' : ($score >= 80 ? 'Excellent' : 'Good') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Performers -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-warning bg-opacity-10 border-warning border-opacity-25">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-25 p-2 rounded-circle me-3">
                            <i class="fas fa-chart-pie text-warning"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 text-warning">Needs Improvement</h5>
                            <small class="text-muted">Staff requiring attention</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($bottomPerformers->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No performance data available</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($bottomPerformers as $index => $performer)
                                <div class="list-group-item px-0 py-3 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <span class="text-warning fw-bold">#{{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                @if($performer->staff)
                                                    <h6 class="mb-0">{{ $performer->staff->name }}</h6>
                                                    <small class="text-muted">{{ $performer->staff->position }}</small>
                                                @else
                                                    <h6 class="mb-0">Unknown Staff</h6>
                                                    <small class="text-muted">Position not available</small>
                                                @endif
                                            </div>
                                        </div>
                                        @php
                                            $score = $performer->avg_performance;
                                            $color = $score >= 60 ? 'warning' : 'danger';
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="fs-5 fw-bold text-{{ $color }} me-2">{{ number_format($score, 1) }}%</span>
                                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis d-none d-sm-inline">{{ $score >= 60 ? 'Satisfactory' : 'Needs Support' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="row mb-4 g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                            <i class="fas fa-lightbulb text-primary"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Performance Insights</h5>
                            <small class="text-muted">Key observations and recommendations</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        @php
                            $avgPerformance = $staffAverages->avg('avg_performance');
                            $topPerformerCount = $topPerformers->count();
                            $needsImprovementCount = $bottomPerformers->count();
                        @endphp
                        
                        <!-- Overall Performance -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start p-3 bg-primary bg-opacity-5 rounded">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="fas fa-chart-line text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Overall Performance</h6>
                                    <p class="mb-0 text-muted small">
                                        The average performance is <strong>{{ number_format($avgPerformance, 1) }}%</strong>, 
                                        which is {{ $avgPerformance >= 80 ? 'excellent' : ($avgPerformance >= 60 ? 'satisfactory' : 'below expectations') }}.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Top Performers -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start p-3 bg-success bg-opacity-5 rounded">
                                <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="fas fa-trophy text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Top Performers</h6>
                                    <p class="mb-0 text-muted small">
                                        {{ $topPerformerCount }} staff members are exceeding expectations.
                                        Recognition and rewards are recommended.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Improvement Opportunities -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start p-3 bg-warning bg-opacity-5 rounded">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                                    <i class="fas fa-tools text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Opportunities</h6>
                                    <p class="mb-0 text-muted small">
                                        {{ $needsImprovementCount }} staff members need support.
                                        Consider training or mentoring programs.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Row -->
    <div class="row mb-4 g-4">
        <!-- Staff Performance Bar Chart -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                            <i class="fas fa-chart-bar text-primary"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Average Performance by Staff</h5>
                            <small class="text-muted">Comparison of staff performance scores</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-container" style="position: relative; height:300px; width:100%">
                        <canvas id="staffPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <!-- Monthly Trend Line Chart -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 p-2 rounded-circle me-3">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Monthly Performance Trends</h5>
                            <small class="text-muted">Performance metrics over time</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-container" style="position: relative; height:300px; width:100%">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Detailed Performance Metrics -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                <i class="fas fa-table text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Detailed Performance Metrics</h5>
                                <small class="text-muted">Comprehensive staff performance data</small>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="input-icon">
                                <input type="text" class="form-control" placeholder="Search staff..." id="staffSearch">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-filter"></i> <span class="d-none d-sm-inline ms-1">Filter</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="all">All Staff</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="excellent">Excellent (90%+)</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="good">Good (80-89%)</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="satisfactory">Satisfactory (60-79%)</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="needs-improvement">Needs Improvement (<60%)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter card-table mb-0" id="performanceMetricsTable">
                            <thead class="thead-light sticky-top bg-white">
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
                                    <th style="width: 25%; padding-right: 1.5rem;">
                                        <a href="#" class="text-decoration-none sort-column" data-column="performance">
                                            Average Performance
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        </a>
                                    </th>
                                    <th style="width: 15%; padding-right: 1.5rem;">
                                        <a href="#" class="text-decoration-none sort-column" data-column="grade">
                                            Grade
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        </a>
                                    </th>
                                    <th class="text-end action-column" style="width: 10%; padding-right: 1.5rem;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffAverages as $index => $staffAvg)
                                    @php
                                        $score = $staffAvg->avg_performance;
                                        $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                        $grade = $score >= 90 ? 'Excellent' : ($score >= 80 ? 'Very Good' : ($score >= 70 ? 'Good' : ($score >= 60 ? 'Satisfactory' : 'Needs Improvement')));
                                        // Determine rank badge color based on position
                                        $rankColor = $index == 0 ? 'bg-warning text-dark' : ($index == 1 ? 'bg-secondary' : ($index == 2 ? 'bg-info' : 'bg-light'));
                                    @endphp
                                    <tr class="performance-row" data-grade="{{ strtolower(str_replace(' ', '-', $grade)) }}">
                                        <td class="text-center d-none d-md-table-cell">
                                            <span class="badge {{ $rankColor }} rounded-pill px-2 py-1 fw-medium">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            @if($staffAvg->staff)
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar me-2 bg-primary bg-opacity-10 text-primary">{{ substr($staffAvg->staff->name, 0, 1) }}</span>
                                                    <div>
                                                        <a href="{{ route('staff.show', $staffAvg->staff) }}" class="text-decoration-none fw-medium">
                                                            {{ $staffAvg->staff->name }}
                                                        </a>
                                                        <div class="d-md-none small text-muted mt-1">
                                                            <span class="badge bg-light">{{ $staffAvg->staff->position }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar me-2 bg-secondary bg-opacity-10 text-secondary">U</span>
                                                    <div>
                                                        <span class="fw-medium">Unknown Staff</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            @if($staffAvg->staff)
                                                <span class="badge bg-primary-subtle text-primary-emphasis px-2 py-1 fw-medium">{{ $staffAvg->staff->position }}</span>
                                            @else
                                                <span class="text-muted">Position not available</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                                <div class="w-100">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-{{ str_replace('-high-contrast', '', $color) }}" style="width: {{ $score }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="performance-metric-value fw-bold text-{{ $color }}">
                                                        {{ number_format($score, 1) }}%
                                                    </span>
                                                    @if($index > 0 && isset($staffAverages[$index-1]))
                                                        @php
                                                            $prevScore = $staffAverages[$index-1]->avg_performance;
                                                            $diff = $score - $prevScore;
                                                        @endphp
                                                        @if($diff > 0)
                                                            <span class="ms-2 text-success" title="Improved from previous rank">
                                                                <i class="fas fa-arrow-up"></i>
                                                                <span class="d-none d-md-inline">{{ number_format(abs($diff), 1) }}%</span>
                                                            </span>
                                                        @elseif($diff < 0)
                                                            <span class="ms-2 text-danger" title="Decreased from previous rank">
                                                                <i class="fas fa-arrow-down"></i>
                                                                <span class="d-none d-md-inline">{{ number_format(abs($diff), 1) }}%</span>
                                                            </span>
                                                        @else
                                                            <span class="ms-2 text-muted" title="No change from previous rank">
                                                                <i class="fas fa-equals"></i>
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center flex-wrap gap-1">
                                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis">{{ $grade }}</span>
                                                @if($score >= 90)
                                                    <i class="fas fa-crown text-warning ms-2" title="Top Performer"></i>
                                                @elseif($score >= 80)
                                                    <i class="fas fa-medal text-info ms-2" title="High Performer"></i>
                                                @elseif($score < 60)
                                                    <i class="fas fa-exclamation-triangle text-danger ms-2" title="Needs Improvement"></i>
                                                @else
                                                    <i class="fas fa-chart-line text-success ms-2" title="Good Performance"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end action-column">
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
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div class="text-muted">
                            Showing <span id="visibleCount">{{ $staffAverages->count() }}</span> of {{ $staffAverages->count() }} staff members
                        </div>
                        <div class="d-flex flex-wrap gap-2">
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
    
    // Truncate long names for better chart display
    const truncatedStaffNames = staffNames.map(name => 
        name.length > 15 ? name.substring(0, 15) + '...' : name
    );
    
    const staffPerformanceChart = new Chart(staffCtx, {
        type: 'bar',
        data: {
            labels: truncatedStaffNames,
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
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const fullName = staffNames[context.dataIndex];
                            return [
                                `Staff: ${fullName}`,
                                `Performance: ${context.parsed.y.toFixed(1)}%`
                            ];
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
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                },
                {
                    label: 'Attendance',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_attendance')) !!},
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                },
                {
                    label: 'Task Completion',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_task_completion')) !!},
                    borderColor: 'rgb(255, 159, 64)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(255, 159, 64)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                },
                {
                    label: 'Customer Feedback',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_feedback')->map(function($score) {
                        return round(($score / 5) * 100, 2); // Convert to percentage
                    })) !!},
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(153, 102, 255)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
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
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const staffName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            if (staffName.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update visible count
        document.getElementById('visibleCount').textContent = visibleCount;
    });
    
    // Add hover effect to table rows
    document.querySelectorAll('#performanceMetricsTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(139, 0, 0, 0.05)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
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
                        aValue = parseFloat(a.querySelector('td:nth-child(4) .fw-bold').textContent);
                        bValue = parseFloat(b.querySelector('td:nth-child(4) .fw-bold').textContent);
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
    
    // Filtering functionality
    document.querySelectorAll('.filter-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            const filter = this.getAttribute('data-filter');
            const tableRows = document.querySelectorAll('.performance-row');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    const rowGrade = row.getAttribute('data-grade');
                    if (rowGrade === filter) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            // Update visible count
            document.getElementById('visibleCount').textContent = visibleCount;
        });
    });
    
    // Print report functionality
    document.getElementById('printReport').addEventListener('click', function() {
        window.print();
    });
    
</script>
@endpush



