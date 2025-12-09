@extends('layouts.butcher')

@push('page-styles')
<style>
    .stat-card { border-left: 4px solid var(--primary-color); transition: transform .2s, box-shadow .2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,.1); }
    .page-title { display: flex; align-items: center; font-weight: 700; }
    .card-header .card-title { font-weight: 600; }
    #salesTrendChart { max-height: 320px; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-title"><i class="fas fa-chart-line me-2"></i>Sales Analytics</h1>
                        <div class="text-muted mt-1">Sales performance, trends, and revenue insights</div>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-2" style="font-weight:600;">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <button onclick="refreshData()" class="btn btn-primary me-2" style="font-weight:600;">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                        <button onclick="exportData()" class="btn btn-success" style="font-weight:600;">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stat-card">
                <div class="card-body">
                    <form id="dateFilterForm" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-0">Date From</label>
                            <input type="date" name="date_from" id="dateFrom" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-0">Date To</label>
                            <input type="date" name="date_to" id="dateTo" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button type="button" onclick="applyDateFilter()" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Apply Filter
                            </button>
                            <button type="button" onclick="resetDateFilter()" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4" id="summaryCards">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalSales">Loading...</div>
                            <div class="text-muted">Total Sales</div>
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
                                <i class="fas fa-chart-line"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="averageDailySales">Loading...</div>
                            <div class="text-muted">Avg Daily Sales</div>
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
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalOrders">Loading...</div>
                            <div class="text-muted">Total Orders</div>
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
                            <span class="bg-danger text-white avatar">
                                <i class="fas fa-money-bill-wave"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalExpenses">Loading...</div>
                            <div class="text-muted">Total Expenses</div>
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
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-balance-scale"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="netIncome">Loading...</div>
                            <div class="text-muted">Net Income (Sales - Expenses)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Product Highlight -->
    <div class="row mb-4" id="topProductCard" style="display: none;">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-white text-primary avatar-lg">
                                <i class="fas fa-trophy"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h3 class="card-title text-white">Top Selling Product</h3>
                            <h2 class="mb-0" id="topProductName">Loading...</h2>
                            <p class="mb-0" id="topProductQuantity">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Trend Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-area me-2"></i>7-Day Sales Trend</h3>
                    <div class="card-actions">
                        <span class="badge bg-success" id="lastUpdated">Last updated: Never</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesTrendChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Performance Metrics -->
    <div class="row">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">Sales Performance</h3>
                </div>
                <div class="card-body" id="salesPerformance">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading sales performance data...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">Revenue Insights</h3>
                </div>
                <div class="card-body" id="revenueInsights">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading revenue insights...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Trend Data Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card stat-card">
                <div class="card-header">
                    <h3 class="card-title">Daily Sales Breakdown (Last 7 Days)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="salesTrendTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Sales Amount</th>
                                    <th>Performance</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesData = {};
let salesChart = null;

// Load sales data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSalesData();
    
    // Auto-refresh every 3 minutes
    setInterval(loadSalesData, 180000);
});

// Load sales analytics data
async function loadSalesData() {
    try {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        let url = '/api/analytics/sales';
        
        if (dateFrom || dateTo) {
            const params = new URLSearchParams();
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            url += '?' + params.toString();
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.status === 'success') {
            salesData = data.data;
            updateSummaryCards();
            updateTopProduct();
            updateSalesPerformance();
            updateRevenueInsights();
            updateSalesTrendTable();
            updateSalesChart();
            updateLastUpdated();
        } else {
            console.error('Failed to load sales data:', data.message);
            showError('Failed to load sales data: ' + data.message);
        }
    } catch (error) {
        console.error('Error loading sales data:', error);
        showError('Error loading sales data: ' + error.message);
    }
}

// Update summary cards
function updateSummaryCards() {
    document.getElementById('totalSales').textContent = '₱' + (salesData.total_sales || 0).toLocaleString();
    document.getElementById('averageDailySales').textContent = '₱' + (salesData.average_daily_sales || 0).toLocaleString();
    document.getElementById('totalOrders').textContent = salesData.total_orders || 0;
    document.getElementById('totalExpenses').textContent = '₱' + (salesData.total_expenses || 0).toLocaleString();
    document.getElementById('netIncome').textContent = '₱' + (salesData.net_income || 0).toLocaleString();
}

// Update top product
function updateTopProduct() {
    if (salesData.top_product && salesData.top_product !== 'No sales data') {
        document.getElementById('topProductName').textContent = salesData.top_product;
        document.getElementById('topProductQuantity').textContent = `Quantity sold: ${salesData.top_product_quantity || 0}`;
        document.getElementById('topProductCard').style.display = 'block';
    } else {
        document.getElementById('topProductCard').style.display = 'none';
    }
}

// Update sales performance
function updateSalesPerformance() {
    const container = document.getElementById('salesPerformance');
    
    const totalSales = salesData.total_sales || 0;
    const averageOrderValue = salesData.average_order_value || 0;
    const totalExpenses = salesData.total_expenses || 0;
    const netIncome = salesData.net_income || 0;
    
    container.innerHTML = `
        <div class="row">
            <div class="col-12">
                <h5>Key Metrics</h5>
                <p><strong>Total Revenue:</strong> ₱${totalSales.toLocaleString()}</p>
                <p><strong>Daily Average:</strong> ₱${averageDailySales.toLocaleString()}</p>
                <p><strong>Total Orders:</strong> ${totalOrders.toLocaleString()}</p>
                <p><strong>Average Order Value:</strong> ₱${averageOrderValue.toLocaleString()}</p>
                <p><strong>Total Expenses:</strong> ₱${totalExpenses.toLocaleString()}</p>
                <p><strong>Net Income (Sales - Expenses):</strong> ₱${netIncome.toLocaleString()}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Performance Rating</h5>
                <div class="progress mb-2">
                    <div class="progress-bar bg-success" style="width: ${getPerformancePercentage()}%"></div>
                </div>
                <small class="text-muted">Performance: ${getPerformanceRating()}</small>
            </div>
        </div>
    `;
}

// Update revenue insights
function updateRevenueInsights() {
    const container = document.getElementById('revenueInsights');
    
    const totalSales = salesData.total_sales || 0;
    const averageDailySales = salesData.average_daily_sales || 0;
    const trend = salesData.trend || [];
    
    // Calculate trend analysis
    const trendAnalysis = analyzeTrend(trend);
    
    container.innerHTML = `
        <div class="row">
            <div class="col-12">
                <h5>Revenue Analysis</h5>
                <p><strong>Total Revenue:</strong> ₱${totalSales.toLocaleString()}</p>
                <p><strong>Daily Average:</strong> ₱${averageDailySales.toLocaleString()}</p>
                <p><strong>Trend:</strong> <span class="badge ${trendAnalysis.badgeClass}">${trendAnalysis.text}</span></p>
                <p><strong>Growth Rate:</strong> ${trendAnalysis.growthRate}%</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Weekly Projection</h5>
                <p class="h4 text-primary">₱${(averageDailySales * 7).toLocaleString()}</p>
                <small class="text-muted">Based on current daily average</small>
            </div>
        </div>
    `;
}

// Update sales trend table
function updateSalesTrendTable() {
    const tbody = document.querySelector('#salesTrendTable tbody');
    const trend = salesData.trend || [];
    
    if (trend.length > 0) {
        let html = '';
        const dates = getLast7Days();
        
        trend.forEach((amount, index) => {
            const date = dates[index];
            const performance = getPerformanceLevel(amount, salesData.average_daily_sales);
            const trendIcon = getTrendIcon(index, trend);
            
            html += `
                <tr>
                    <td>${date}</td>
                    <td>₱${amount.toLocaleString()}</td>
                    <td><span class="badge ${performance.badgeClass}">${performance.text}</span></td>
                    <td>${trendIcon}</td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    } else {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No sales data available</td></tr>';
    }
}

// Update sales chart
function updateSalesChart() {
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    const trend = salesData.trend || [];
    const dates = getLast7Days();
    
    if (salesChart) {
        salesChart.destroy();
    }
    
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Daily Sales (₱)',
                data: trend,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            elements: { point: { radius: 3 } },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: { display: true, position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return '₱' + ctx.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// Get last 7 days
function getLast7Days() {
    const dates = [];
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        dates.push(date.toLocaleDateString());
    }
    return dates;
}

// Analyze trend
function analyzeTrend(trend) {
    if (trend.length < 2) {
        return { text: 'Insufficient Data', badgeClass: 'bg-secondary', growthRate: 0 };
    }
    
    const firstHalf = trend.slice(0, Math.floor(trend.length / 2));
    const secondHalf = trend.slice(Math.floor(trend.length / 2));
    
    const firstAvg = firstHalf.reduce((a, b) => a + b, 0) / firstHalf.length;
    const secondAvg = secondHalf.reduce((a, b) => a + b, 0) / secondHalf.length;
    
    const growthRate = firstAvg > 0 ? ((secondAvg - firstAvg) / firstAvg) * 100 : 0;
    
    if (growthRate > 10) {
        return { text: 'Growing Strong', badgeClass: 'bg-success', growthRate: growthRate.toFixed(1) };
    } else if (growthRate > 0) {
        return { text: 'Growing', badgeClass: 'bg-info', growthRate: growthRate.toFixed(1) };
    } else if (growthRate > -10) {
        return { text: 'Stable', badgeClass: 'bg-warning', growthRate: growthRate.toFixed(1) };
    } else {
        return { text: 'Declining', badgeClass: 'bg-danger', growthRate: growthRate.toFixed(1) };
    }
}

// Get performance level
function getPerformanceLevel(amount, average) {
    if (amount > average * 1.2) {
        return { text: 'Excellent', badgeClass: 'bg-success' };
    } else if (amount > average) {
        return { text: 'Good', badgeClass: 'bg-info' };
    } else if (amount > average * 0.8) {
        return { text: 'Average', badgeClass: 'bg-warning' };
    } else {
        return { text: 'Below Average', badgeClass: 'bg-danger' };
    }
}

// Get trend icon
function getTrendIcon(index, trend) {
    if (index === 0) return '<i class="fas fa-minus text-muted"></i>';
    
    const current = trend[index];
    const previous = trend[index - 1];
    
    if (current > previous) {
        return '<i class="fas fa-arrow-up text-success"></i>';
    } else if (current < previous) {
        return '<i class="fas fa-arrow-down text-danger"></i>';
    } else {
        return '<i class="fas fa-minus text-muted"></i>';
    }
}

// Get performance percentage
function getPerformancePercentage() {
    const averageDailySales = salesData.average_daily_sales || 0;
    const totalSales = salesData.total_sales || 0;
    
    if (averageDailySales === 0) return 0;
    
    // Simple performance calculation based on daily average
    const performance = Math.min(100, (averageDailySales / 10000) * 100); // Assuming 10k is excellent
    return Math.round(performance);
}

// Get performance rating
function getPerformanceRating() {
    const percentage = getPerformancePercentage();
    
    if (percentage >= 90) return 'Excellent';
    if (percentage >= 75) return 'Very Good';
    if (percentage >= 60) return 'Good';
    if (percentage >= 40) return 'Fair';
    return 'Needs Improvement';
}

// Update last updated timestamp
function updateLastUpdated() {
    const now = new Date();
    document.getElementById('lastUpdated').textContent = `Last updated: ${now.toLocaleTimeString()}`;
}

// Refresh data
function refreshData() {
    loadSalesData();
}

// Export data
function exportData() {
    if (Object.keys(salesData).length === 0) {
        alert('No data to export. Please refresh the data first.');
        return;
    }
    
    const dataStr = JSON.stringify(salesData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `sales-analytics-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}

// Show error message
function showError(message) {
    const performanceContainer = document.getElementById('salesPerformance');
    const insightsContainer = document.getElementById('revenueInsights');
    
    const errorHtml = `
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-circle"></i> Error</h5>
            <p>${message}</p>
            <button onclick="loadSalesData()" class="btn btn-danger">
                <i class="fas fa-retry"></i> Retry
            </button>
        </div>
    `;
    
    performanceContainer.innerHTML = errorHtml;
    insightsContainer.innerHTML = errorHtml;
}

// Apply date filter
function applyDateFilter() {
    loadSalesData();
}

// Reset date filter
function resetDateFilter() {
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    loadSalesData();
}
</script>
@endpush
