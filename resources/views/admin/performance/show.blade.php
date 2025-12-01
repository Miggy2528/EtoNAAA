@extends('layouts.butcher')

@push('page-styles')
<style>
    .performance-metric-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid var(--primary-color);
        height: 100%;
    }
    
    .performance-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .metric-value {
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .metric-label {
        font-weight: 600;
        color: #495057;
    }
    
    .metric-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
            <h1 class="page-title mb-0">
                <i class="fas fa-info-circle me-2"></i>Performance Details
            </h1>
        </div>
        <div class="col-md-6 text-md-end text-start">
            <a href="{{ route('staff-performance.edit', $staffPerformance) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('staff-performance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <x-alert/>

    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-user me-2"></i>Staff Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Name:</th>
                            <td>{{ $staffPerformance->staff->name }}</td>
                        </tr>
                        <tr>
                            <th>Position:</th>
                            <td>{{ $staffPerformance->staff->position }}</td>
                        </tr>
                        <tr>
                            <th>Department:</th>
                            <td>{{ $staffPerformance->staff->department ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Month:</th>
                            <td>{{ \Carbon\Carbon::parse($staffPerformance->month)->format('F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Evaluated On:</th>
                            <td>{{ $staffPerformance->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $staffPerformance->updated_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>Performance Metrics
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Attendance Rate Card -->
                <div class="col-md-4">
                    <div class="card performance-metric-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title metric-label mb-1">Attendance Rate</h6>
                                    <p class="card-text small text-muted mb-0">Regular presence and punctuality</p>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-calendar-check fs-4"></i>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <div class="metric-value text-primary">{{ number_format($staffPerformance->attendance_rate, 1) }}%</div>
                            </div>
                            <div class="mt-2">
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $staffPerformance->attendance_rate }}%"></div>
                                </div>
                                <div class="small text-muted text-center mt-2">Weight: 30%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Completion Rate Card -->
                <div class="col-md-4">
                    <div class="card performance-metric-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title metric-label mb-1">Task Completion</h6>
                                    <p class="card-text small text-muted mb-0">Quality and timeliness of work</p>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-clipboard-list fs-4"></i>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <div class="metric-value text-info">{{ number_format($staffPerformance->task_completion_rate, 1) }}%</div>
                            </div>
                            <div class="mt-2">
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-info" style="width: {{ $staffPerformance->task_completion_rate }}%"></div>
                                </div>
                                <div class="small text-muted text-center mt-2">Weight: 40%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Feedback Score Card -->
                <div class="col-md-4">
                    <div class="card performance-metric-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title metric-label mb-1">Customer Feedback</h6>
                                    <p class="card-text small text-muted mb-0">Service quality rating</p>
                                </div>
                                <div class="metric-icon">
                                    <i class="fas fa-comments fs-4"></i>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <div class="metric-value text-success">{{ number_format($staffPerformance->customer_feedback_score, 1) }}/5.0</div>
                            </div>
                            <div class="mt-2">
                                <div class="d-flex justify-content-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $staffPerformance->customer_feedback_score)
                                            <i class="fas fa-star text-warning me-1"></i>
                                        @elseif($i - 0.5 <= $staffPerformance->customer_feedback_score)
                                            <i class="fas fa-star-half-alt text-warning me-1"></i>
                                        @else
                                            <i class="far fa-star text-muted me-1"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="small text-muted text-center mt-2">Weight: 30%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body text-center">
            <h4 class="mb-3">Overall Performance Score</h4>
            @php
                $score = $staffPerformance->overall_performance;
                $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
            @endphp
            <div class="display-1 mb-3">
                <span class="badge bg-{{ $color }}">
                    {{ number_format($score, 1) }}%
                </span>
            </div>
            <h5 class="mb-3">
                <span class="badge bg-{{ $color }} fs-4">
                    {{ $staffPerformance->grade }}
                </span>
            </h5>
            <p class="text-muted mb-0">
                Formula: (Attendance × 30%) + (Task Completion × 40%) + (Feedback × 30%)
            </p>
        </div>
    </div>

    @if($staffPerformance->remarks)
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-comment me-2"></i>Remarks
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                {{ $staffPerformance->remarks }}
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-body bg-light">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('staff.show', $staffPerformance->staff) }}" class="btn btn-primary">
                    <i class="fas fa-user me-1"></i>
                    View Staff Profile
                </a>
                <a href="{{ route('staff-performance.edit', $staffPerformance) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>
                    Edit Evaluation
                </a>
                <form action="{{ route('staff-performance.destroy', $staffPerformance) }}" method="POST" 
                    style="display: inline-block;" 
                    onsubmit="return confirm('Are you sure you want to delete this performance record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
