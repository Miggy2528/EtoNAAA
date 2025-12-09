@extends('layouts.butcher')

@push('page-styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    .chart-container {
        position: relative;
        height: 400px;
    }
    .profit-positive { color: #28a745; font-weight: bold; }
    .profit-negative { color: #dc3545; font-weight: bold; }
    .month-input-row input {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 0.5rem;
    }
    .month-input-row input:focus {
        border-color: #8B0000;
        box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.25);
    }
    .btn-save-month {
        background-color: #8B0000;
        border-color: #8B0000;
        color: white;
    }
    .btn-save-month:hover {
        background-color: #6d0000;
        border-color: #6d0000;
    }
    .action-buttons .btn {
        margin: 0 5px;
    }
    @media print {
        .no-print { display: none !important; }
        .card { page-break-inside: avoid; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none no-print">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            <i class="fas fa-chart-line text-success"></i> Sales Analytics
                        </h2>
                        <div class="text-muted mt-1">Comprehensive sales performance and trends analysis (2020-2025)</div>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="action-buttons">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Reports
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <a href="{{ route('reports.sales.analytics.export-csv') }}" class="btn btn-success">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-trophy"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $insights['highest_sales_year']->year ?? 'N/A' }}</div>
                            <div class="text-muted">Highest Sales Year</div>
                            <small class="text-success">₱{{ number_format($insights['highest_sales_year']->total_sales ?? 0, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-coins"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $insights['most_profitable_year']->year ?? 'N/A' }}</div>
                            <div class="text-muted">Most Profitable Year</div>
                            <small class="text-primary">₱{{ number_format($insights['most_profitable_year']->net_profit ?? 0, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $insights['top_product']->product_name ?? 'N/A' }}</div>
                            <div class="text-muted">Top-Selling Product</div>
                            <small class="text-warning">
                                Qty: {{ number_format($insights['top_product']->total_quantity ?? 0) }} | 
                                Revenue: ₱{{ number_format($insights['top_product']->total_revenue ?? 0, 2) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <!-- Sales Performance Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary"></i> Sales Performance (2020-2025)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trends Analysis Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-success"></i> Annual Trends (2020-2025)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2025 Automated Sales Projection Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-robot"></i> Automated 2025 Sales Projection (January - November)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Sales projection data for 2025 is automatically generated on a monthly basis.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2025 Data Visualization -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary"></i> 2025 Sales Projection
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="sales2025Chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Yearly Summary Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table text-info"></i> Yearly Summary (2020-2024)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Total Sales</th>
                                    <th>Total Expenses</th>
                                    <th>Net Profit</th>
                                    <th>Profit Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($yearlySummary as $year)
                                    @php
                                        $margin = $year->total_sales > 0 ? ($year->net_profit / $year->total_sales) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $year->year }}</strong></td>
                                        <td>₱{{ number_format($year->total_sales, 2) }}</td>
                                        <td class="text-danger">₱{{ number_format($year->total_expenses, 2) }}</td>
                                        <td class="{{ $year->net_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                            ₱{{ number_format($year->net_profit, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $margin >= 20 ? 'bg-success' : ($margin >= 10 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($margin, 2) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales and Profit Report -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary"></i> Monthly Sales and Profit Report ({{ date('Y') }})
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Test element to verify JavaScript is working -->
                    <div id="js-test" class="alert alert-info d-none">JavaScript is working correctly!</div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Total Sales (₱)</th>
                                    <th class="text-end">Daily Avg Sales (₱)</th>
                                    <th class="text-end">Net Profit (₱)</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyReportData['data'] as $data)
                                    <tr>
                                        <td>{{ $data['month'] }}</td>
                                        <td class="text-end">₱{{ number_format($data['total_sales'], 2) }}</td>
                                        <td class="text-end">₱{{ number_format($data['daily_average'], 2) }}</td>
                                        <td class="text-end">₱{{ number_format($data['net_profit'], 2) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary monthly-details-btn" 
                                                    data-month="{{ date('n', strtotime($data['month'])) }}"
                                                    data-month-name="{{ $data['month'] }}">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">₱{{ number_format($monthlyReportData['grand_total_sales'], 2) }}</td>
                                    <td class="text-end">₱{{ number_format($monthlyReportData['grand_total_daily_average'], 2) }}</td>
                                    <td class="text-end">₱{{ number_format($monthlyReportData['grand_total_profit'], 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Income Report (Sales - Expenses) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-money-bill-wave"></i> Monthly Income Report ({{ date('Y') }}) - Sales minus Expenses
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Total Sales</h6>
                                    <h4 class="card-title mb-0">₱{{ number_format($monthlyIncomeReport['total_sales'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Total Expenses</h6>
                                    <h4 class="card-title mb-0">₱{{ number_format($monthlyIncomeReport['total_expenses'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Net Income (Sales - Expenses)</h6>
                                    <h4 class="card-title mb-0">₱{{ number_format($monthlyIncomeReport['total_income'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Avg Profit Margin</h6>
                                    <h4 class="card-title mb-0">
                                        <span class="badge {{ $monthlyIncomeReport['avg_margin'] >= 30 ? 'bg-light text-success' : ($monthlyIncomeReport['avg_margin'] >= 15 ? 'bg-light text-warning' : 'bg-light text-danger') }}">
                                            {{ number_format($monthlyIncomeReport['avg_margin'], 2) }}%
                                        </span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calculation Formula Display -->
                    <div class="alert alert-success mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <h5 class="mb-3"><i class="fas fa-calculator"></i> <strong>Monthly Income Calculation:</strong></h5>
                                <div class="d-flex align-items-center justify-content-center flex-wrap" style="font-size: 1.2rem;">
                                    <div class="text-center mx-3">
                                        <div class="text-primary fw-bold">Total Sales</div>
                                        <div class="display-6 text-primary">₱{{ number_format($monthlyIncomeReport['total_sales'], 2) }}</div>
                                    </div>
                                    <div class="mx-3">
                                        <i class="fas fa-minus fa-2x"></i>
                                    </div>
                                    <div class="text-center mx-3">
                                        <div class="text-danger fw-bold">Total Expenses</div>
                                        <div class="display-6 text-danger">₱{{ number_format($monthlyIncomeReport['total_expenses'], 2) }}</div>
                                    </div>
                                    <div class="mx-3">
                                        <i class="fas fa-equals fa-2x"></i>
                                    </div>
                                    <div class="text-center mx-3">
                                        <div class="text-success fw-bold">Net Income</div>
                                        <div class="display-6 text-success fw-bold">₱{{ number_format($monthlyIncomeReport['total_income'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Income Chart -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="chart-container">
                                <canvas id="monthlyIncomeChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Breakdown Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Sales (₱)</th>
                                    <th class="text-end">Expenses (₱)</th>
                                    <th class="text-end">Utilities (₱)</th>
                                    <th class="text-end">Payroll (₱)</th>
                                    <th class="text-end">Other (₱)</th>
                                    <th class="text-end bg-success text-white">Net Income (₱)<br><small>(Sales - Expenses)</small></th>
                                    <th class="text-center">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyIncomeReport['data'] as $data)
                                    <tr>
                                        <td><strong>{{ $data['month'] }}</strong></td>
                                        <td class="text-end">₱{{ number_format($data['sales'], 2) }}</td>
                                        <td class="text-end text-danger">₱{{ number_format($data['expenses'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($data['utilities'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($data['payroll'], 2) }}</td>
                                        <td class="text-end text-muted">₱{{ number_format($data['other_expenses'], 2) }}</td>
                                        <td class="text-end {{ $data['income'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                            ₱{{ number_format($data['income'], 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $data['margin'] >= 30 ? 'bg-success' : ($data['margin'] >= 15 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($data['margin'], 2) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary fw-bold">
                                    <td>TOTAL</td>
                                    <td class="text-end">₱{{ number_format($monthlyIncomeReport['total_sales'], 2) }}</td>
                                    <td class="text-end text-danger">₱{{ number_format($monthlyIncomeReport['total_expenses'], 2) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end {{ $monthlyIncomeReport['total_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₱{{ number_format($monthlyIncomeReport['total_income'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $monthlyIncomeReport['avg_margin'] >= 30 ? 'bg-success' : ($monthlyIncomeReport['avg_margin'] >= 15 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($monthlyIncomeReport['avg_margin'], 2) }}%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> This report shows actual income by calculating Sales minus Expenses (Utilities + Payroll + Other Expenses). 
                        Color coding: <span class="badge bg-success">Green ≥30%</span>, <span class="badge bg-warning">Yellow 15-29%</span>, <span class="badge bg-danger">Red <15%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-calendar-day"></i> Daily Sales Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filter Controls -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="daily-filter-type" class="form-label">Filter Type</label>
                            <select id="daily-filter-type" class="form-select">
                                <option value="single">Single Date</option>
                                <option value="range" selected>Date Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="single-date-container">
                            <label for="filter-date" class="form-label">Date</label>
                            <input type="date" id="filter-date" class="form-control" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3" id="start-date-container">
                            <label for="filter-start-date" class="form-label">Start Date</label>
                            <input type="date" id="filter-start-date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3" id="end-date-container">
                            <label for="filter-end-date" class="form-label">End Date</label>
                            <input type="date" id="filter-end-date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="apply-daily-filter" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div id="daily-summary-cards" class="row mb-4" style="display: none;">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Total Sales</h6>
                                    <h4 class="card-title mb-0" id="daily-total-sales">₱0.00</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Total Orders</h6>
                                    <h4 class="card-title mb-0" id="daily-total-orders">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Average Order</h6>
                                    <h4 class="card-title mb-0" id="daily-avg-order">₱0.00</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-white-50">Total Profit</h6>
                                    <h4 class="card-title mb-0" id="daily-total-profit">₱0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Sales Chart -->
                    <div id="daily-chart-container" class="mb-4" style="display: none;">
                        <div class="chart-container">
                            <canvas id="dailySalesChart"></canvas>
                        </div>
                    </div>

                    <!-- Daily Sales Table -->
                    <div id="daily-sales-table-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Total Sales (₱)</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Avg Order (₱)</th>
                                        <th class="text-end">Profit (₱)</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="daily-sales-tbody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="daily-sales-loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading daily sales data...</p>
                    </div>

                    <!-- No Data Message -->
                    <div id="daily-sales-no-data" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-info-circle"></i> No sales data found for the selected date range.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Daily Sales Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-calendar-check"></i> Recent Daily Sales (Last 30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentDailySales->count() > 0)
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-white-50">Total Days with Sales</h6>
                                        <h4 class="card-title mb-0">{{ $recentDailySales->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-white-50">Total Sales</h6>
                                        <h4 class="card-title mb-0">₱{{ number_format($recentDailySales->sum('total_sales'), 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-white-50">Total Orders</h6>
                                        <h4 class="card-title mb-0">{{ number_format($recentDailySales->sum('total_orders')) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-white-50">Avg Daily Sales</h6>
                                        <h4 class="card-title mb-0">₱{{ number_format($recentDailySales->count() > 0 ? $recentDailySales->sum('total_sales') / $recentDailySales->count() : 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Sales Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Total Sales (₱)</th>
                                        <th class="text-end">Avg Order (₱)</th>
                                        <th class="text-end">Net Profit (₱)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDailySales->take(15) as $daySale)
                                        <tr>
                                            <td><strong>{{ $daySale['date'] }}</strong></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info badge view-daily-orders-btn" 
                                                        data-date="{{ $daySale['date_raw'] }}"
                                                        data-date-label="{{ $daySale['date'] }}"
                                                        style="cursor: pointer; border: none;">
                                                    {{ $daySale['total_orders'] }} orders
                                                </button>
                                            </td>
                                            <td class="text-end">₱{{ number_format($daySale['total_sales'], 2) }}</td>
                                            <td class="text-end">₱{{ number_format($daySale['average_order'], 2) }}</td>
                                            <td class="text-end text-success">₱{{ number_format($daySale['net_profit'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary fw-bold">
                                    <tr>
                                        <td>Total (Last {{ $recentDailySales->take(15)->count() }} Days)</td>
                                        <td class="text-center">{{ number_format($recentDailySales->take(15)->sum('total_orders')) }}</td>
                                        <td class="text-end">₱{{ number_format($recentDailySales->take(15)->sum('total_sales'), 2) }}</td>
                                        <td class="text-end">₱{{ number_format($recentDailySales->take(15)->count() > 0 ? $recentDailySales->take(15)->sum('total_sales') / $recentDailySales->take(15)->sum('total_orders') : 0, 2) }}</td>
                                        <td class="text-end">₱{{ number_format($recentDailySales->take(15)->sum('net_profit'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No sales data available for the last 30 days.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Details Modal -->
    <div class="modal fade" id="monthlyDetailsModal" tabindex="-1" aria-labelledby="monthlyDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="monthlyDetailsModalLabel">Monthly Sales Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="monthly-details-content">
                        <!-- Content will be loaded here via AJAX -->
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('page-scripts')
<script>
// Chart colors
const chartColors = {
    primary: '#0d6efd',
    success: '#28a745',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#17a2b8',
    darkRed: '#8B0000'
};

// Prepare data for charts
const yearlySummary = @json($yearlySummary);

// Sales Performance Chart (Line Chart)
const salesCtx = document.getElementById('salesPerformanceChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: yearlySummary.map(y => y.year),
        datasets: [
            {
                label: 'Total Sales',
                data: yearlySummary.map(y => y.total_sales),
                borderColor: chartColors.success,
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Total Expenses',
                data: yearlySummary.map(y => y.total_expenses),
                borderColor: chartColors.danger,
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2})
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '₱' + value.toLocaleString('en-PH')
                }
            }
        }
    }
});

// Trends Analysis Chart (Bar Chart)
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
new Chart(trendsCtx, {
    type: 'bar',
    data: {
        labels: yearlySummary.map(y => y.year),
        datasets: [{
            label: 'Net Profit',
            data: yearlySummary.map(y => y.net_profit),
            backgroundColor: chartColors.primary,
            borderColor: chartColors.primary,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Net Profit: ₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2})
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '₱' + value.toLocaleString('en-PH')
                }
            }
        }
    }
});

// Monthly Income Report Chart
const monthlyIncomeData = @json($monthlyIncomeReport);
const incomeCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
new Chart(incomeCtx, {
    type: 'bar',
    data: {
        labels: monthlyIncomeData.data.map(m => m.month),
        datasets: [
            {
                label: 'Sales',
                data: monthlyIncomeData.data.map(m => m.sales),
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: chartColors.primary,
                borderWidth: 2
            },
            {
                label: 'Expenses',
                data: monthlyIncomeData.data.map(m => m.expenses),
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: chartColors.danger,
                borderWidth: 2
            },
            {
                label: 'Net Income',
                data: monthlyIncomeData.data.map(m => m.income),
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: chartColors.success,
                borderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                position: 'top',
                labels: {
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return ctx.dataset.label + ': ₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2});
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '₱' + value.toLocaleString('en-PH')
                }
            },
            x: {
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    }
});

// 2025 Sales Chart
let sales2025Chart = null;

// Load 2025 data via AJAX
function load2025Data() {
    fetch('{{ route("reports.sales.analytics.get-2025") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            console.log('2025 data response:', result);
            if (result.success) {
                // Update the 2025 chart with the generated data
                update2025Chart(result.data);
            } else {
                console.error('Failed to load 2025 data:', result.message);
            }
        })
        .catch(error => {
            console.error('Error loading 2025 data:', error);
            // Show error message in the chart container
            const chartContainer = document.getElementById('sales2025Chart').parentNode;
            chartContainer.innerHTML = '<div class="alert alert-danger">Error loading sales projection data. Please try refreshing the page.</div>';
        });
}

// Update 2025 chart with data
function update2025Chart(data) {
    console.log('Updating 2025 chart with data:', data);
    
    // Check if data is valid
    if (!data || !Array.isArray(data) || data.length === 0) {
        console.warn('No 2025 data available to display');
        const chartContainer = document.getElementById('sales2025Chart').parentNode;
        chartContainer.innerHTML = '<div class="alert alert-info">No sales projection data available for 2025.</div>';
        return;
    }
    
    const ctx = document.getElementById('sales2025Chart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (sales2025Chart) {
        sales2025Chart.destroy();
    }
    
    // Month names for labels
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Extract data for chart
    const labels = data.map(item => monthNames[item.month - 1]);
    const salesData = data.map(item => parseFloat(item.total_sales));
    const profitData = data.map(item => parseFloat(item.net_profit));
    
    console.log('Chart data - Labels:', labels, 'Sales:', salesData, 'Profit:', profitData);
    
    // Create new chart
    sales2025Chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Sales',
                    data: salesData,
                    borderColor: chartColors.success,
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Net Profit',
                    data: profitData,
                    borderColor: chartColors.primary,
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': ₱' + parseFloat(ctx.parsed.y).toLocaleString('en-PH', {minimumFractionDigits: 2})
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₱' + parseFloat(value).toLocaleString('en-PH')
                    }
                }
            }
        }
    });
    
    console.log('2025 chart created successfully');
}

// Load 2025 data on page load
document.addEventListener('DOMContentLoaded', () => {
    // Test to verify JavaScript is working
    const testElement = document.getElementById('js-test');
    if (testElement) {
        testElement.classList.remove('d-none');
    }
    
    load2025Data();
    
    // Add event listeners for monthly details buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.monthly-details-btn')) {
            const button = e.target.closest('.monthly-details-btn');
            const month = button.dataset.month;
            const monthName = button.dataset.monthName;
            
            loadMonthlyDetails(month, monthName);
        }
    });

    // Daily Sales Filter Event Listeners
    initializeDailySalesFilter();
    
    // Daily Order Products Event Listeners
    initializeDailyOrdersView();
});

// Function to load monthly details
function loadMonthlyDetails(month, monthName) {
    console.log('Loading monthly details for:', monthName, 'Month:', month);
    
    // Show modal with loading state
    const modal = new bootstrap.Modal(document.getElementById('monthlyDetailsModal'));
    document.getElementById('monthlyDetailsModalLabel').textContent = monthName + ' Sales Details';
    modal.show();
    
    // Load data via AJAX
    const url = '/reports/sales-analytics/monthly-details?month=' + month + '&year=' + new Date().getFullYear();
    console.log('Fetching data from:', url);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Received data:', data);
            if (data.success) {
                displayMonthlyDetails(data, monthName);
            } else {
                document.getElementById('monthly-details-content').innerHTML = 
                    '<div class="alert alert-danger">Error loading monthly details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('monthly-details-content').innerHTML = 
                '<div class="alert alert-danger">Error loading monthly details. Please try again.</div>';
        });
}

// Function to display monthly details
function displayMonthlyDetails(data, monthName) {
    // Build top products table rows
    let topProductsRows = '';
    data.top_products.forEach(product => {
        topProductsRows += '<tr>' +
            '<td>' + product.product_name + '</td>' +
            '<td class="text-end">' + product.total_quantity + '</td>' +
            '<td class="text-end">₱' + parseFloat(product.total_revenue).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>' +
            '</tr>';
    });
    
    // Build payment breakdown rows
    let paymentBreakdownRows = '';
    Object.entries(data.payment_breakdown).forEach(([method, details]) => {
        paymentBreakdownRows += '<tr>' +
            '<td>' + method + '</td>' +
            '<td class="text-end">' + details.count + '</td>' +
            '<td class="text-end">₱' + parseFloat(details.total).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>' +
            '</tr>';
    });
    
    // Build orders table rows
    let ordersRows = '';
    data.orders.forEach(order => {
        ordersRows += '<tr>' +
            '<td>' + order.invoice_no + '</td>' +
            '<td>' + order.customer_name + '</td>' +
            '<td>' + order.order_date + '</td>' +
            '<td>' + order.items_count + '</td>' +
            '<td>' + order.payment_type + '</td>' +
            '<td class="text-end">₱' + parseFloat(order.total).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>' +
            '</tr>';
    });
    
    const content = 
        '<div class="row">' +
            '<div class="col-md-4">' +
                '<div class="card bg-primary text-white mb-3">' +
                    '<div class="card-body">' +
                        '<h5 class="card-title">Total Sales</h5>' +
                        '<h2>₱' + parseFloat(data.summary.total_sales).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</h2>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="col-md-4">' +
                '<div class="card bg-success text-white mb-3">' +
                    '<div class="card-body">' +
                        '<h5 class="card-title">Total Orders</h5>' +
                        '<h2>' + data.summary.total_orders + '</h2>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="col-md-4">' +
                '<div class="card bg-info text-white mb-3">' +
                    '<div class="card-body">' +
                        '<h5 class="card-title">Avg. Order Value</h5>' +
                        '<h2>₱' + parseFloat(data.summary.average_order_value).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</h2>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
        
        '<div class="row">' +
            '<div class="col-md-6">' +
                '<div class="card mb-3">' +
                    '<div class="card-header">' +
                        '<h5 class="card-title mb-0">Top Selling Products</h5>' +
                    '</div>' +
                    '<div class="card-body">' +
                        '<div class="table-responsive">' +
                            '<table class="table table-striped">' +
                                '<thead>' +
                                    '<tr>' +
                                        '<th>Product</th>' +
                                        '<th class="text-end">Quantity</th>' +
                                        '<th class="text-end">Revenue</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>' +
                                    topProductsRows +
                                '</tbody>' +
                            '</table>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            
            '<div class="col-md-6">' +
                '<div class="card mb-3">' +
                    '<div class="card-header">' +
                        '<h5 class="card-title mb-0">Payment Method Breakdown</h5>' +
                    '</div>' +
                    '<div class="card-body">' +
                        '<div class="table-responsive">' +
                            '<table class="table table-striped">' +
                                '<thead>' +
                                    '<tr>' +
                                        '<th>Payment Method</th>' +
                                        '<th class="text-end">Orders</th>' +
                                        '<th class="text-end">Amount</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>' +
                                    paymentBreakdownRows +
                                '</tbody>' +
                            '</table>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
        
        '<div class="card">' +
            '<div class="card-header">' +
                '<h5 class="card-title mb-0">Recent Orders</h5>' +
            '</div>' +
            '<div class="card-body">' +
                '<div class="table-responsive">' +
                    '<table class="table table-striped">' +
                        '<thead>' +
                            '<tr>' +
                                '<th>Invoice</th>' +
                                '<th>Customer</th>' +
                                '<th>Date</th>' +
                                '<th>Items</th>' +
                                '<th>Payment</th>' +
                                '<th class="text-end">Total</th>' +
                            '</tr>' +
                        '</thead>' +
                        '<tbody>' +
                            ordersRows +
                        '</tbody>' +
                    '</table>' +
                '</div>' +
            '</div>' +
        '</div>';
    
    document.getElementById('monthly-details-content').innerHTML = content;
}

// Daily Sales Filter Functions
let dailySalesChart = null;

function initializeDailySalesFilter() {
    // Filter type change handler
    const filterType = document.getElementById('daily-filter-type');
    const singleDateContainer = document.getElementById('single-date-container');
    const startDateContainer = document.getElementById('start-date-container');
    const endDateContainer = document.getElementById('end-date-container');
    
    filterType.addEventListener('change', function() {
        if (this.value === 'single') {
            singleDateContainer.style.display = 'block';
            startDateContainer.style.display = 'none';
            endDateContainer.style.display = 'none';
        } else {
            singleDateContainer.style.display = 'none';
            startDateContainer.style.display = 'block';
            endDateContainer.style.display = 'block';
        }
    });
    
    // Initialize display based on default selection
    if (filterType.value === 'range') {
        singleDateContainer.style.display = 'none';
    } else {
        startDateContainer.style.display = 'none';
        endDateContainer.style.display = 'none';
    }
    
    // Apply filter button
    document.getElementById('apply-daily-filter').addEventListener('click', loadDailySalesData);
}

function loadDailySalesData() {
    const filterType = document.getElementById('daily-filter-type').value;
    let params = new URLSearchParams();
    
    if (filterType === 'single') {
        const date = document.getElementById('filter-date').value;
        if (!date) {
            Swal.fire({
                icon: 'warning',
                title: 'Date Required',
                text: 'Please select a date to filter.'
            });
            return;
        }
        params.append('date', date);
    } else {
        const startDate = document.getElementById('filter-start-date').value;
        const endDate = document.getElementById('filter-end-date').value;
        
        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Dates Required',
                text: 'Please select both start and end dates.'
            });
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date Range',
                text: 'Start date must be before or equal to end date.'
            });
            return;
        }
        
        params.append('start_date', startDate);
        params.append('end_date', endDate);
    }
    
    // Show loading state
    document.getElementById('daily-sales-loading').style.display = 'block';
    document.getElementById('daily-summary-cards').style.display = 'none';
    document.getElementById('daily-chart-container').style.display = 'none';
    document.getElementById('daily-sales-table-container').style.display = 'none';
    document.getElementById('daily-sales-no-data').style.display = 'none';
    
    // Fetch data
    fetch('/reports/sales-analytics/daily-sales?' + params.toString())
        .then(response => response.json())
        .then(data => {
            document.getElementById('daily-sales-loading').style.display = 'none';
            
            if (data.success && data.daily_data.length > 0) {
                displayDailySalesData(data);
            } else {
                document.getElementById('daily-sales-no-data').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading daily sales:', error);
            document.getElementById('daily-sales-loading').style.display = 'none';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load daily sales data. Please try again.'
            });
        });
}

function displayDailySalesData(data) {
    // Update summary cards
    document.getElementById('daily-total-sales').textContent = '₱' + parseFloat(data.summary.total_sales).toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('daily-total-orders').textContent = data.summary.total_orders;
    document.getElementById('daily-avg-order').textContent = '₱' + parseFloat(data.summary.average_order_value).toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('daily-total-profit').textContent = '₱' + parseFloat(data.summary.total_profit).toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('daily-summary-cards').style.display = 'flex';
    
    // Update chart
    updateDailySalesChart(data.daily_data);
    document.getElementById('daily-chart-container').style.display = 'block';
    
    // Update table
    const tbody = document.getElementById('daily-sales-tbody');
    tbody.innerHTML = '';
    
    data.daily_data.forEach(day => {
        const row = document.createElement('tr');
        row.innerHTML = 
            '<td>' + day.date + '</td>' +
            '<td class="text-end">₱' + parseFloat(day.total_sales).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>' +
            '<td class="text-center">' + day.total_orders + '</td>' +
            '<td class="text-end">₱' + parseFloat(day.average_order_value).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>' +
            '<td class="text-end">₱' + parseFloat(day.profit).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-info view-day-details" data-date="' + day.date_raw + '" data-date-label="' + day.date + '">' +
                    '<i class="fas fa-eye"></i> View' +
                '</button>' +
            '</td>';
        tbody.appendChild(row);
    });
    
    document.getElementById('daily-sales-table-container').style.display = 'block';
    
    // Add event listeners for view buttons
    document.querySelectorAll('.view-day-details').forEach(btn => {
        btn.addEventListener('click', function() {
            const date = this.dataset.date;
            const dateLabel = this.dataset.dateLabel;
            loadDailyOrderProducts(date, dateLabel);
        });
    });
}

function updateDailySalesChart(dailyData) {
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (dailySalesChart) {
        dailySalesChart.destroy();
    }
    
    const labels = dailyData.map(d => d.date);
    const salesData = dailyData.map(d => parseFloat(d.total_sales));
    const profitData = dailyData.map(d => parseFloat(d.profit));
    
    dailySalesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Sales',
                    data: salesData,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: chartColors.success,
                    borderWidth: 2
                },
                {
                    label: 'Profit',
                    data: profitData,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: chartColors.warning,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': ₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2})
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₱' + value.toLocaleString('en-PH')
                    }
                }
            }
        }
    });
}

function showDayDetails(orders, dateLabel) {
    let ordersHtml = '';
    if (orders.length === 0) {
        ordersHtml = '<div class="alert alert-info">No orders found for this day.</div>';
    } else {
        ordersHtml = '<table class="table table-striped table-hover">' +
            '<thead>' +
                '<tr>' +
                    '<th>Invoice</th>' +
                    '<th>Customer</th>' +
                    '<th>Time</th>' +
                    '<th>Items</th>' +
                    '<th>Payment</th>' +
                    '<th class="text-end">Total</th>' +
                '</tr>' +
            '</thead>' +
            '<tbody>';
        
        orders.forEach(order => {
            ordersHtml += '<tr>' +
                '<td>' + order.invoice_no + '</td>' +
                '<td>' + order.customer_name + '</td>' +
                '<td>' + order.order_date + '</td>' +
                '<td>' + order.items_count + '</td>' +
                '<td>' + order.payment_type + '</td>' +
                '<td class="text-end">₱' + parseFloat(order.total).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>' +
                '</tr>';
        });
        
        ordersHtml += '</tbody></table>';
    }
    
    Swal.fire({
        title: dateLabel + ' - Order Details',
        html: ordersHtml,
        width: '800px',
        showCloseButton: true,
        confirmButtonText: 'Close'
    });
}

// Daily Order Products Functions
function initializeDailyOrdersView() {
    // Add event listeners for view daily orders buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-daily-orders-btn')) {
            const button = e.target.closest('.view-daily-orders-btn');
            const date = button.dataset.date;
            const dateLabel = button.dataset.dateLabel;
            
            loadDailyOrderProducts(date, dateLabel);
        }
    });
}

function loadDailyOrderProducts(date, dateLabel) {
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        html: 'Fetching order products for ' + dateLabel,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Fetch data from API
    fetch('/reports/sales-analytics/daily-order-products?date=' + date)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayDailyOrderProducts(data, dateLabel);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load order products.'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load order products. Please try again.'
            });
        });
}

function displayDailyOrderProducts(data, dateLabel) {
    let content = '';
    
    // Summary section
    content += '<div class="row mb-3">';
    content += '<div class="col-md-4">';
    content += '<div class="card bg-primary text-white">';
    content += '<div class="card-body p-2">';
    content += '<h6 class="mb-0">Total Orders</h6>';
    content += '<h4 class="mb-0">' + data.summary.total_orders + '</h4>';
    content += '</div></div></div>';
    
    content += '<div class="col-md-4">';
    content += '<div class="card bg-success text-white">';
    content += '<div class="card-body p-2">';
    content += '<h6 class="mb-0">Total Sales</h6>';
    content += '<h4 class="mb-0">₱' + parseFloat(data.summary.total_sales).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</h4>';
    content += '</div></div></div>';
    
    content += '<div class="col-md-4">';
    content += '<div class="card bg-info text-white">';
    content += '<div class="card-body p-2">';
    content += '<h6 class="mb-0">Total Items Sold</h6>';
    content += '<h4 class="mb-0">' + data.summary.total_items + '</h4>';
    content += '</div></div></div>';
    content += '</div>';
    
    if (data.products.length === 0) {
        content += '<div class="alert alert-info">No products found for this date.</div>';
    } else {
        // Products table
        content += '<h5 class="mt-3 mb-2"><i class="fas fa-box"></i> Products Sold</h5>';
        content += '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">';
        content += '<table class="table table-sm table-striped table-hover">';
        content += '<thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">';
        content += '<tr>';
        content += '<th>Product Name</th>';
        content += '<th>Category</th>';
        content += '<th class="text-center">Quantity</th>';
        content += '<th class="text-end">Total Sales</th>';
        content += '<th class="text-center">Orders</th>';
        content += '</tr>';
        content += '</thead>';
        content += '<tbody>';
        
        data.products.forEach(product => {
            content += '<tr>';
            content += '<td><strong>' + product.product_name + '</strong></td>';
            content += '<td><span class="badge bg-secondary">' + product.category_name + '</span></td>';
            content += '<td class="text-center"><span class="badge bg-primary">' + product.quantity + '</span></td>';
            content += '<td class="text-end">₱' + parseFloat(product.total_sales).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>';
            content += '<td class="text-center">' + product.orders_count + '</td>';
            content += '</tr>';
        });
        
        content += '</tbody>';
        content += '</table>';
        content += '</div>';
        
        // Orders section (collapsible)
        if (data.orders.length > 0) {
            content += '<div class="mt-3">';
            content += '<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#ordersDetails" aria-expanded="false">';
            content += '<i class="fas fa-shopping-cart"></i> View All Orders (' + data.orders.length + ')';
            content += '</button>';
            content += '<div class="collapse mt-2" id="ordersDetails">';
            content += '<div class="table-responsive" style="max-height: 300px; overflow-y: auto;">';
            content += '<table class="table table-sm table-bordered">';
            content += '<thead class="table-light">';
            content += '<tr>';
            content += '<th>Invoice</th>';
            content += '<th>Customer</th>';
            content += '<th>Time</th>';
            content += '<th class="text-center">Items</th>';
            content += '<th>Payment</th>';
            content += '<th class="text-end">Total</th>';
            content += '</tr>';
            content += '</thead>';
            content += '<tbody>';
            
            data.orders.forEach(order => {
                content += '<tr>';
                content += '<td>' + order.invoice_no + '</td>';
                content += '<td>' + order.customer_name + '</td>';
                content += '<td>' + order.order_date + '</td>';
                content += '<td class="text-center">' + order.items_count + '</td>';
                content += '<td>' + order.payment_type + '</td>';
                content += '<td class="text-end">₱' + parseFloat(order.total).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</td>';
                content += '</tr>';
            });
            
            content += '</tbody>';
            content += '</table>';
            content += '</div>';
            content += '</div>';
            content += '</div>';
        }
    }
    
    Swal.fire({
        title: '<i class="fas fa-calendar-day"></i> ' + dateLabel + ' - Order Products',
        html: content,
        width: '900px',
        showCloseButton: true,
        confirmButtonText: 'Close',
        customClass: {
            popup: 'swal-wide'
        }
    });
}

</script>
@endpush
