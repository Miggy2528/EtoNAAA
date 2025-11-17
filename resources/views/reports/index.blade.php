@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Analytics Dashboard</h2>
                        <div class="text-muted mt-1">Real-time insights for ButcherPro Management System</div>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <button onclick="refreshAllData()" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Inventory Analytics -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-boxes"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Inventory Analytics</h5>
                            <p class="card-text">Real-time inventory insights, stock levels, and expiration tracking.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.inventory') }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Analytics -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-chart-line"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Sales Analytics</h5>
                            <p class="card-text">Comprehensive sales performance and trends analysis (2020-2025).</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.sales.analytics') }}" class="btn btn-success">
                            <i class="fas fa-chart-bar"></i> View Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Analytics -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <i class="fas fa-truck"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Supplier Analytics</h5>
                            <p class="card-text">Supplier performance, delivery tracking, and procurement insights.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.supplier.analytics') }}" class="btn btn-warning">
                            <i class="fas fa-chart-bar"></i> View Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Performance -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-users"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Staff Performance</h5>
                            <p class="card-text">Staff productivity, performance evaluations, and team analytics.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('staff.report') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-users"></i> Manage Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>
        

    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables for API data
let analyticsData = {};

// Load all analytics data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAllData();
    
    // Auto-refresh every 5 minutes
    setInterval(loadAllData, 300000);
});

// Load all analytics data
async function loadAllData() {
    try {
        const response = await fetch('/api/analytics/dashboard');
        const data = await response.json();
        
        if (data.status === 'success') {
            analyticsData = data.data;
            updateLastUpdated();
        } else {
            console.error('Failed to load analytics data:', data.message);
        }
    } catch (error) {
        console.error('Error loading analytics data:', error);
    }
}

// Update last updated timestamp
function updateLastUpdated() {
    // This function is kept for future use but does nothing since the element was removed
}

// Individual data loading functions
async function loadInventoryData() {
    try {
        const response = await fetch('/api/analytics/inventory');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.inventory = data.data;
        }
    } catch (error) {
        console.error('Error loading inventory data:', error);
    }
}

async function loadSalesData() {
    try {
        const response = await fetch('/api/analytics/sales');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.sales = data.data;
        }
    } catch (error) {
        console.error('Error loading sales data:', error);
    }
}

async function loadSupplierData() {
    try {
        const response = await fetch('/api/analytics/suppliers');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.suppliers = data.data;
        }
    } catch (error) {
        console.error('Error loading supplier data:', error);
    }
}

async function loadStaffData() {
    try {
        const response = await fetch('/api/analytics/staff');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.staff = data.data;
        }
    } catch (error) {
        console.error('Error loading staff data:', error);
    }
}

// Refresh all data
function refreshAllData() {
    loadAllData();
}

// Show API endpoints modal
function showApiEndpoints() {
    const modal = new bootstrap.Modal(document.getElementById('apiEndpointsModal'));
    modal.show();
}

// Test API endpoint
async function testEndpoint(endpoint) {
    try {
        const response = await fetch(endpoint);
        const data = await response.json();
        
        if (data.status === 'success') {
            alert('API endpoint working! Check console for data.');
            console.log(`${endpoint} response:`, data);
        } else {
            alert('API endpoint returned error: ' + data.message);
        }
    } catch (error) {
        alert('Error testing endpoint: ' + error.message);
    }
}

// Export all data
function exportAllData() {
    if (Object.keys(analyticsData).length === 0) {
        alert('No data to export. Please refresh the data first.');
        return;
    }
    
    const dataStr = JSON.stringify(analyticsData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `butcherpro-analytics-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush