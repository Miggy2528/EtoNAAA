@extends('layouts.butcher')

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
            <h1 class="page-title mb-0">
                <i class="fas fa-user me-2"></i>Staff Profile: {{ $staff->name }}
            </h1>
        </div>
        <div class="col-md-6 text-md-end text-start">
            <a href="{{ route('staff.edit', $staff) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <x-alert/>

    <!-- Staff Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Staff Information
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                            <div class="text-center">
                                <div class="mx-auto mb-3">
                                    <div class="avatar avatar-xxl rounded-circle mx-auto" style="background-color: var(--primary-color); color: white; font-weight: 600; width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                                        {{ strtoupper(substr($staff->name, 0, 2)) }}
                                    </div>
                                </div>
                                <h4 class="mb-1">{{ $staff->name }}</h4>
                                <p class="text-muted mb-3">{{ $staff->position }}</p>
                                
                                @php
                                    $avgPerf = $staff->average_performance ?? 0;
                                    $perfColor = $avgPerf >= 80 ? 'success' : ($avgPerf >= 60 ? 'warning' : 'danger');
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                        <span class="badge bg-{{ $perfColor }} fs-6 fw-bold px-3 py-2">
                                            {{ number_format($avgPerf, 1) }}% Performance
                                        </span>
                                    </div>
                                    <div class="progress d-none d-md-block" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $perfColor }}" 
                                             role="progressbar" 
                                             style="width: {{ $avgPerf }}%" 
                                             aria-valuenow="{{ $avgPerf }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                                
                                <span class="badge rounded-pill bg-{{ $staff->status == 'Active' ? 'success' : 'secondary' }} px-3 py-2">
                                    {{ $staff->status }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-lg-9 col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Department</label>
                                        <div class="fw-medium">{{ $staff->department ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Contact Number</label>
                                        @if($staff->contact_number)
                                            <div>
                                                <a href="tel:{{ $staff->contact_number }}" class="text-decoration-none">
                                                    <i class="fas fa-phone me-1"></i>{{ $staff->contact_number }}
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-muted">N/A</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Date Hired</label>
                                        @if($staff->date_hired)
                                            <div class="fw-medium">{{ $staff->date_hired->format('F d, Y') }}</div>
                                            <div class="small text-muted">{{ $staff->date_hired->diffForHumans() }}</div>
                                        @else
                                            <div class="text-muted">N/A</div>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Performance Records</label>
                                        <div class="fw-medium">{{ $staff->performances->count() }} Records</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="mb-3">Performance Summary</h6>
                                <div class="row g-3">
                                    @php
                                        $latestPerformance = $staff->performances->sortByDesc('month')->first();
                                    @endphp
                                    
                                    @if($latestPerformance)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="border rounded p-3 text-center">
                                                <div class="text-muted small mb-1">Attendance</div>
                                                <div class="fs-5 fw-bold">{{ number_format($latestPerformance->attendance_rate, 0) }}%</div>
                                                <div class="progress mt-2" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $latestPerformance->attendance_rate }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 col-sm-6">
                                            <div class="border rounded p-3 text-center">
                                                <div class="text-muted small mb-1">Task Completion</div>
                                                <div class="fs-5 fw-bold">{{ number_format($latestPerformance->task_completion_rate, 0) }}%</div>
                                                <div class="progress mt-2" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $latestPerformance->task_completion_rate }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 col-sm-6">
                                            <div class="border rounded p-3 text-center">
                                                <div class="text-muted small mb-1">Feedback Score</div>
                                                <div class="fs-5 fw-bold">{{ number_format($latestPerformance->customer_feedback_score, 1) }}/5</div>
                                                <div class="small text-muted">Grade: {{ $latestPerformance->grade }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <div class="text-center py-3">
                                                <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No performance records available</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Trend Chart -->
    @if($staff->performances->count() > 1)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Performance Trend
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="performanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Records -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Performance History
                        </h5>
                        <div>
                            <a href="{{ route('staff-performance.create', ['staff_id' => $staff->id]) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-plus me-1"></i>Add Performance Record
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($staff->performances->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="mb-2">No Performance Records Found</h5>
                            <p class="text-muted mb-4">Add performance records to track staff progress over time.</p>
                            <a href="{{ route('staff-performance.create', ['staff_id' => $staff->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add First Performance Record
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%;">Month</th>
                                        <th style="width: 15%;">Attendance</th>
                                        <th style="width: 15%;">Task Completion</th>
                                        <th style="width: 15%;">Feedback Score</th>
                                        <th style="width: 15%;" class="text-center">Overall Score</th>
                                        <th style="width: 10%;">Grade</th>
                                        <th style="width: 20%;">Remarks</th>
                                        <th style="width: 10%;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staff->performances->sortByDesc('month') as $performance)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ \Carbon\Carbon::parse($performance->month)->format('F Y') }}</div>
                                            <div class="small text-muted d-none d-md-block">{{ \Carbon\Carbon::parse($performance->month)->format('M Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-fill me-2" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $performance->attendance_rate }}%"></div>
                                                </div>
                                                <div class="small d-none d-md-block">{{ number_format($performance->attendance_rate, 0) }}%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-fill me-2" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $performance->task_completion_rate }}%"></div>
                                                </div>
                                                <div class="small d-none d-md-block">{{ number_format($performance->task_completion_rate, 0) }}%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-fill me-2" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ ($performance->customer_feedback_score / 5) * 100 }}%"></div>
                                                </div>
                                                <div class="small d-none d-md-block">{{ number_format($performance->customer_feedback_score, 1) }}/5</div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $score = $performance->overall_performance;
                                                $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge bg-{{ $color }} fs-6 fw-bold">
                                                {{ number_format($score, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $performance->grade }}</span>
                                        </td>
                                        <td>
                                            @if($performance->remarks)
                                                <div class="text-truncate d-none d-md-block" style="max-width: 150px;" title="{{ $performance->remarks }}">{{ $performance->remarks }}</div>
                                                <div class="d-md-none text-truncate" title="{{ $performance->remarks }}">{{ Str::limit($performance->remarks, 20) }}</div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group float-end" role="group">
                                                <a href="{{ route('staff-performance.edit', $performance) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('staff-performance.destroy', $performance) }}" method="POST" 
                                                    onsubmit="return confirm('Delete this performance record?');" class="d-inline">
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

                        <!-- Performance Summary -->
                        <div class="mt-4 pt-4 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <h5 class="mb-0">Overall Performance: 
                                        <span class="badge bg-success fs-4 fw-bold ms-2">
                                            {{ number_format($staff->average_performance, 1) }}%
                                        </span>
                                    </h5>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <a href="{{ route('staff-performance.create', ['staff_id' => $staff->id]) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i>Add New Record
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($staff->performances->count() > 1)
    // Performance Trend Chart
    const trendCtx = document.getElementById('performanceTrendChart').getContext('2d');
    
    // Prepare data from PHP
    const trendData = @json($performanceTrend);
    const labels = trendData.map(item => item.month);
    const overallPerformance = trendData.map(item => item.performance);
    const attendance = trendData.map(item => item.attendance);
    const taskCompletion = trendData.map(item => item.task_completion);
    const feedback = trendData.map(item => item.feedback);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Overall Performance',
                    data: overallPerformance,
                    borderColor: 'rgba(139, 0, 0, 1)', // Primary red color
                    backgroundColor: 'rgba(139, 0, 0, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Attendance',
                    data: attendance,
                    borderColor: 'rgba(0, 123, 255, 1)', // Blue
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Task Completion',
                    data: taskCompletion,
                    borderColor: 'rgba(40, 167, 69, 1)', // Green
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Feedback Score',
                    data: feedback,
                    borderColor: 'rgba(255, 193, 7, 1)', // Yellow
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
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
                        text: 'Performance (%)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Months'
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush