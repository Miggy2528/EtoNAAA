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
                                    <th class="text-end">Net Profit (₱)</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyReportData['data'] as $data)
                                    <tr>
                                        <td>{{ $data['month'] }}</td>
                                        <td class="text-end">₱{{ number_format($data['total_sales'], 2) }}</td>
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

</script>
@endpush
