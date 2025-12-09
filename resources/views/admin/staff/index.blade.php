@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
            <h1 class="page-title mb-0">
                <i class="fas fa-users me-2"></i>Staff Management
            </h1>
        </div>
        <div class="col-md-6 text-md-end text-start">
            <a href="{{ route('staff.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Staff
            </a>
        </div>
    </div>

    <x-alert/>

    @if($staff->isEmpty())
        <div class="col-12 text-center py-5">
            <i class="fas fa-users fa-4x text-muted mb-3"></i>
            <h3>No staff members found</h3>
            <p class="text-muted">Add your first staff member to get started</p>
            <a href="{{ route('staff.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-1"></i>Add your first Staff Member
            </a>
        </div>
    @else
        <!-- Performance Overview Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Top Performers Overview
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filters
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body">
                    <form method="GET" action="{{ route('staff.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" placeholder="Name, Position..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Department</label>
                                <select class="form-select" name="department">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Position</label>
                                <select class="form-select" name="position">
                                    <option value="">All Positions</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
                                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                    <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                        <i class="fas fa-search me-1"></i>Apply Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sort Controls -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
            <div>
                <span class="text-muted">Showing {{ $staff->count() }} staff members</span>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-sort me-1"></i>Sort By
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => (request('sort') == 'name' && request('direction') == 'asc') ? 'desc' : 'asc']) }}">
                        Name {{ request('sort') == 'name' ? (request('direction') == 'asc' ? '(A-Z)' : '(Z-A)') : '' }}
                    </a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'performance', 'direction' => (request('sort') == 'performance' && request('direction') == 'asc') ? 'desc' : 'asc']) }}">
                        Performance {{ request('sort') == 'performance' ? (request('direction') == 'asc' ? '(Low-High)' : '(High-Low)') : '' }}
                    </a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'date_hired', 'direction' => (request('sort') == 'date_hired' && request('direction') == 'asc') ? 'desc' : 'asc']) }}">
                        Date Hired {{ request('sort') == 'date_hired' ? (request('direction') == 'asc' ? '(Oldest-Newest)' : '(Newest-Oldest)') : '' }}
                    </a></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Staff Members
                    </h5>
                    <span class="badge bg-primary">{{ $staff->count() }} Members</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter card-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Name</th>
                                <th style="width: 15%;">Position</th>
                                <th style="width: 15%;">Department</th>
                                <th style="width: 12%;">Contact</th>
                                <th style="width: 12%;">Date Hired</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%;" class="text-center">Performance</th>
                                <th style="width: 12%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-md me-3 rounded" style="background-color: var(--primary-color); color: white; font-weight: 600;">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </span>
                                        <div>
                                            <div class="font-weight-bold">{{ $member->name }}</div>
                                            <div class="text-muted small d-none d-md-block">{{ $member->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-weight-medium">{{ $member->position }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $member->department ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($member->contact_number)
                                        <a href="tel:{{ $member->contact_number }}" class="text-decoration-none">
                                            <i class="fas fa-phone me-1"></i>
                                            <span class="d-none d-md-inline">{{ $member->contact_number }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($member->date_hired)
                                        <div class="text-nowrap">{{ $member->date_hired->format('M d, Y') }}</div>
                                        <div class="small text-muted d-none d-md-block">{{ $member->date_hired->diffForHumans() }}</div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $member->status == 'Active' ? 'success' : 'secondary' }} px-2 py-1">
                                        {{ $member->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $avgPerf = $member->performances_avg_overall_performance ?? 0;
                                        $badgeColor = $avgPerf >= 80 ? 'success' : ($avgPerf >= 60 ? 'warning' : 'danger');
                                        $progressBarColor = $avgPerf >= 80 ? 'bg-success' : ($avgPerf >= 60 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill me-2 d-none d-md-block">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $progressBarColor }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $avgPerf }}%" 
                                                     aria-valuenow="{{ $avgPerf }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $badgeColor }} fs-6 fw-bold">
                                                {{ number_format($avgPerf, 0) }}%
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end align-middle" style="vertical-align: middle;">
                                    <div class="btn-group d-flex justify-content-end" role="group">
                                        <a href="{{ route('staff.show', $member) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('staff.destroy', $member) }}" method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this staff member?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('page-scripts')
<script>
// Wait for Chart.js to be fully loaded
function initPerformanceChart() {
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart');
    
    if (performanceCtx) {
        const ctx = performanceCtx.getContext('2d');
        
        // Prepare data from PHP
        const performanceData = @json($performanceData);
        
        // Check if we have data
        if (performanceData && performanceData.length > 0) {
            const labels = performanceData.map(item => item.name);
            const data = performanceData.map(item => item.performance);
            
            // Define colors based on performance values
            const backgroundColors = data.map(value => {
                if (value >= 80) return 'rgba(40, 167, 69, 0.7)'; // Green for excellent
                if (value >= 60) return 'rgba(255, 193, 7, 0.7)';  // Yellow for good
                return 'rgba(220, 53, 69, 0.7)';                   // Red for poor
            });
            
            const borderColors = data.map(value => {
                if (value >= 80) return 'rgba(40, 167, 69, 1)';
                if (value >= 60) return 'rgba(255, 193, 7, 1)';
                return 'rgba(220, 53, 69, 1)';
            });
            
            try {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Performance (%)',
                            data: data,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 1
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
                                        return context.dataset.label + ': ' + context.parsed.y + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Performance Score'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Staff Members'
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Chart initialization error:', error);
            }
        } else {
            // Show a message when no data is available
            performanceCtx.closest('.chart-container').innerHTML = 
                '<div class="text-center py-5 text-muted">' +
                '<i class="fas fa-chart-bar fa-2x mb-3"></i>' +
                '<p>No performance data available to display chart</p>' +
                '</div>';
        }
    }
}

// Initialize chart when DOM is loaded and Chart.js is available
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        initPerformanceChart();
    } else {
        // If Chart.js isn't loaded yet, wait a bit and try again
        setTimeout(function() {
            if (typeof Chart !== 'undefined') {
                initPerformanceChart();
            } else {
                // Show error message
                const performanceCtx = document.getElementById('performanceChart');
                if (performanceCtx) {
                    performanceCtx.closest('.chart-container').innerHTML = 
                        '<div class="text-center py-5 text-danger">' +
                        '<i class="fas fa-exclamation-triangle fa-2x mb-3"></i>' +
                        '<p>Chart library failed to load. Please refresh the page.</p>' +
                        '</div>';
                }
            }
        }, 1000);
    }
});
</script>
@endpush