@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Supplier Analytics</h2>
                        <div class="text-muted mt-1">Supplier performance, delivery tracking, and procurement insights</div>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <button onclick="refreshData()" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                        <button onclick="exportData()" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <div class="row mb-4" id="summaryCards">
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
                            <div class="font-weight-medium" id="totalSuppliers">Loading...</div>
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
                            <div class="font-weight-medium" id="activeSuppliers">Loading...</div>
                            <div class="text-muted">Active Suppliers</div>
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
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="recentPurchases">Loading...</div>
                            <div class="text-muted">Recent Purchases</div>
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
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalPurchaseAmount">Loading...</div>
                            <div class="text-muted">Total Purchase Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Supplier Highlight -->
    <div class="row mb-4" id="topSupplierCard" style="display: none;">
        <div class="col-12">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-white text-warning avatar-lg">
                                <i class="fas fa-crown"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h3 class="card-title text-white">Top Supplier</h3>
                            <h2 class="mb-0" id="topSupplierName">Loading...</h2>
                            <p class="mb-0" id="topSupplierDetails">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Performance Metrics -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Supplier Performance</h3>
                    <div class="card-actions">
                        <span class="badge bg-success" id="lastUpdated">Last updated: Never</span>
                    </div>
                </div>
                <div class="card-body" id="supplierPerformance">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading supplier performance data...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Procurement Insights</h3>
                </div>
                <div class="card-body" id="procurementInsights">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading procurement insights...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Activity Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Supplier Activity Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="supplierActivityChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Details Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Supplier Details</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="supplierDetailsTable">
                            <thead>
                                <tr>
                                    <th>Supplier Name</th>
                                    <th>Status</th>
                                    <th>Total Purchases</th>
                                    <th>Purchase Count</th>
                                    <th>Last Activity</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
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
let supplierData = {};
let supplierChart = null;
let currentDateFrom = '';
let currentDateTo = '';

// Load supplier data on page load
document.addEventListener('DOMContentLoaded', function() {
    const dateFromInput = document.getElementById('supplierDateFrom');
    const dateToInput = document.getElementById('supplierDateTo');

    if (dateFromInput && dateToInput) {
        dateFromInput.addEventListener('change', function() {
            currentDateFrom = this.value;
            loadSupplierData();
        });
        dateToInput.addEventListener('change', function() {
            currentDateTo = this.value;
            loadSupplierData();
        });
    }

    loadSupplierData();
    
    // Auto-refresh every 5 minutes
    setInterval(loadSupplierData, 300000);
});

// Load supplier analytics data
async function loadSupplierData() {
    try {
        let url = '/api/analytics/suppliers';
        const params = [];
        if (currentDateFrom) {
            params.push('date_from=' + encodeURIComponent(currentDateFrom));
        }
        if (currentDateTo) {
            params.push('date_to=' + encodeURIComponent(currentDateTo));
        }
        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        const response = await fetch(url);
        const data = await response.json();
        
        if (data.status === 'success') {
            supplierData = data.data;
            updateSummaryCards();
            updateTopSupplier();
            updateSupplierPerformance();
            updateProcurementInsights();
            updateSupplierDetailsTable();
            updateSupplierChart();
            updateLastUpdated();
        } else {
            console.error('Failed to load supplier data:', data.message);
            showError('Failed to load supplier data: ' + data.message);
        }
    } catch (error) {
        console.error('Error loading supplier data:', error);
        showError('Error loading supplier data: ' + error.message);
    }
}

// Update summary cards
function updateSummaryCards() {
    document.getElementById('totalSuppliers').textContent = supplierData.total_suppliers || 0;
    document.getElementById('activeSuppliers').textContent = supplierData.active_suppliers || 0;
    document.getElementById('recentPurchases').textContent = supplierData.recent_purchases || 0;
    document.getElementById('totalPurchaseAmount').textContent = '₱' + (supplierData.total_purchase_amount || 0).toLocaleString();
}

// Update top supplier
function updateTopSupplier() {
    if (supplierData.most_frequent_supplier && supplierData.most_frequent_supplier !== 'No data') {
        document.getElementById('topSupplierName').textContent = supplierData.most_frequent_supplier;
        document.getElementById('topSupplierDetails').textContent = `Deliveries: ${supplierData.most_frequent_supplier_count || 0} | Amount: ₱${(supplierData.top_supplier_amount || 0).toLocaleString()}`;
        document.getElementById('topSupplierCard').style.display = 'block';
    } else {
        document.getElementById('topSupplierCard').style.display = 'none';
    }
}

// Update supplier performance
function updateSupplierPerformance() {
    const container = document.getElementById('supplierPerformance');
    
    const totalSuppliers = supplierData.total_suppliers || 0;
    const activeSuppliers = supplierData.active_suppliers || 0;
    const recentPurchases = supplierData.recent_purchases || 0;
    const totalPurchaseAmount = supplierData.total_purchase_amount || 0;
    
    const activityRate = totalSuppliers > 0 ? (activeSuppliers / totalSuppliers) * 100 : 0;
    
    container.innerHTML = `
        <div class="row">
            <div class="col-12">
                <h5>Key Metrics</h5>
                <p><strong>Total Suppliers:</strong> ${totalSuppliers}</p>
                <p><strong>Active Suppliers:</strong> ${activeSuppliers}</p>
                <p><strong>Recent Purchases:</strong> ${recentPurchases}</p>
                <p><strong>Total Purchase Amount:</strong> ₱${totalPurchaseAmount.toLocaleString()}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Activity Rate</h5>
                <div class="progress mb-2">
                    <div class="progress-bar bg-success" style="width: ${activityRate}%"></div>
                </div>
                <small class="text-muted">${activityRate.toFixed(1)}% of suppliers are active</small>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Top Performers</h5>
                <p><strong>Most Frequent:</strong> ${supplierData.most_frequent_supplier || 'N/A'}</p>
                <p><strong>Highest Value:</strong> ${supplierData.top_supplier_by_amount || 'N/A'}</p>
            </div>
        </div>
    `;
}

// Update procurement insights
function updateProcurementInsights() {
    const container = document.getElementById('procurementInsights');
    
    const totalSuppliers = supplierData.total_suppliers || 0;
    const activeSuppliers = supplierData.active_suppliers || 0;
    const recentPurchases = supplierData.recent_purchases || 0;
    const totalPurchaseAmount = supplierData.total_purchase_amount || 0;
    
    const averagePurchaseValue = recentPurchases > 0 ? totalPurchaseAmount / recentPurchases : 0;
    const supplierUtilization = totalSuppliers > 0 ? (activeSuppliers / totalSuppliers) * 100 : 0;
    
    container.innerHTML = `
        <div class="row">
            <div class="col-12">
                <h5>Procurement Analysis</h5>
                <p><strong>Average Purchase Value:</strong> ₱${averagePurchaseValue.toLocaleString()}</p>
                <p><strong>Supplier Utilization:</strong> ${supplierUtilization.toFixed(1)}%</p>
                <p><strong>Purchase Frequency:</strong> ${getPurchaseFrequency(recentPurchases)}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Recommendations</h5>
                <ul class="list-unstyled">
                    ${getRecommendations(supplierUtilization, recentPurchases)}
                </ul>
            </div>
        </div>
    `;
}

// Update supplier details table
function updateSupplierDetailsTable() {
    const tbody = document.querySelector('#supplierDetailsTable tbody');
    
    // Since we don't have detailed supplier data from the API, we'll show summary info
    const suppliers = [
        {
            name: supplierData.most_frequent_supplier || 'N/A',
            status: 'Active',
            totalPurchases: supplierData.top_supplier_amount || 0,
            purchaseCount: supplierData.most_frequent_supplier_count || 0,
            lastActivity: 'Recent',
            performance: 'Excellent'
        },
        {
            name: supplierData.top_supplier_by_amount || 'N/A',
            status: 'Active',
            totalPurchases: supplierData.top_supplier_amount || 0,
            purchaseCount: 'N/A',
            lastActivity: 'Recent',
            performance: 'Good'
        }
    ];
    
    let html = '';
    suppliers.forEach(supplier => {
        if (supplier.name !== 'N/A') {
            html += `
                <tr>
                    <td>${supplier.name}</td>
                    <td><span class="badge bg-success">${supplier.status}</span></td>
                    <td>₱${supplier.totalPurchases.toLocaleString()}</td>
                    <td>${supplier.purchaseCount}</td>
                    <td>${supplier.lastActivity}</td>
                    <td><span class="badge bg-primary">${supplier.performance}</span></td>
                </tr>
            `;
        }
    });
    
    if (html === '') {
        html = '<tr><td colspan="6" class="text-center text-muted">No supplier data available</td></tr>';
    }
    
    tbody.innerHTML = html;
}

// Update supplier chart
function updateSupplierChart() {
    const ctx = document.getElementById('supplierActivityChart').getContext('2d');
    
    if (supplierChart) {
        supplierChart.destroy();
    }
    
    const totalSuppliers = supplierData.total_suppliers || 0;
    const activeSuppliers = supplierData.active_suppliers || 0;
    const inactiveSuppliers = totalSuppliers - activeSuppliers;
    
    supplierChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active Suppliers', 'Inactive Suppliers'],
            datasets: [{
                data: [activeSuppliers, inactiveSuppliers],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Get purchase frequency
function getPurchaseFrequency(recentPurchases) {
    if (recentPurchases >= 20) return 'High';
    if (recentPurchases >= 10) return 'Medium';
    if (recentPurchases >= 5) return 'Low';
    return 'Very Low';
}

// Get recommendations
function getRecommendations(utilization, purchases) {
    let recommendations = [];
    
    if (utilization < 50) {
        recommendations.push('<li class="text-warning"><i class="fas fa-exclamation-triangle"></i> Consider activating more suppliers</li>');
    }
    
    if (purchases < 10) {
        recommendations.push('<li class="text-info"><i class="fas fa-info-circle"></i> Increase purchase frequency</li>');
    }
    
    if (utilization >= 80 && purchases >= 15) {
        recommendations.push('<li class="text-success"><i class="fas fa-check-circle"></i> Excellent supplier utilization</li>');
    }
    
    if (recommendations.length === 0) {
        recommendations.push('<li class="text-muted">No specific recommendations at this time</li>');
    }
    
    return recommendations.join('');
}

// Update last updated timestamp
function updateLastUpdated() {
    const now = new Date();
    document.getElementById('lastUpdated').textContent = `Last updated: ${now.toLocaleTimeString()}`;
}

// Refresh data
function refreshData() {
    loadSupplierData();
}

function clearSupplierDateFilters() {
    currentDateFrom = '';
    currentDateTo = '';
    const dateFromInput = document.getElementById('supplierDateFrom');
    const dateToInput = document.getElementById('supplierDateTo');
    if (dateFromInput) dateFromInput.value = '';
    if (dateToInput) dateToInput.value = '';
    loadSupplierData();
}

// Export data
function exportData() {
    if (Object.keys(supplierData).length === 0) {
        alert('No data to export. Please refresh the data first.');
        return;
    }
    
    const dataStr = JSON.stringify(supplierData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `supplier-analytics-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}

// Show error message
function showError(message) {
    const performanceContainer = document.getElementById('supplierPerformance');
    const insightsContainer = document.getElementById('procurementInsights');
    
    const errorHtml = `
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-circle"></i> Error</h5>
            <p>${message}</p>
            <button onclick="loadSupplierData()" class="btn btn-danger">
                <i class="fas fa-retry"></i> Retry
            </button>
        </div>
    `;
    
    performanceContainer.innerHTML = errorHtml;
    insightsContainer.innerHTML = errorHtml;
}
</script>
@endpush
