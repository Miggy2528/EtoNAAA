@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
            <h1 class="page-title mb-0">
                <i class="fas fa-chart-line me-2"></i>Performance Records
            </h1>
        </div>
        <div class="col-md-6 text-md-end text-start">
            <a href="{{ route('staff.report') }}" class="btn btn-success">
                <i class="fas fa-chart-bar me-2"></i>View Report
            </a>
            <a href="{{ route('staff-performance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Performance Record
            </a>
        </div>
    </div>

    <x-alert/>

    @if($performances->isEmpty())
        <div class="col-12 text-center py-5">
            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
            <h3>No performance records found</h3>
            <p class="text-muted">Add performance evaluations to track staff performance</p>
            <a href="{{ route('staff-performance.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-1"></i>Add First Performance Record
            </a>
        </div>
    @else
        <!-- Summary Cards -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">{{ $performances->count() }}</h5>
                                <p class="card-text mb-0 small text-muted">Total Records</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line fa-2x text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">
                                    @php
                                        $avgPerformance = $performances->avg('overall_performance');
                                    @endphp
                                    {{ number_format($avgPerformance, 1) }}%
                                </h5>
                                <p class="card-text mb-0 small text-muted">Avg Performance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-medal fa-2x text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">
                                    @php
                                        $excellentCount = $performances->filter(fn($p) => $p->overall_performance >= 80)->count();
                                    @endphp
                                    {{ $excellentCount }}
                                </h5>
                                <p class="card-text mb-0 small text-muted">Excellent (80%+)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-left-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-friends fa-2x text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">
                                    @php
                                        $uniqueStaff = $performances->pluck('staff_id')->unique()->count();
                                    @endphp
                                    {{ $uniqueStaff }}
                                </h5>
                                <p class="card-text mb-0 small text-muted">Staff Evaluated</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Performance Evaluations
                    </h5>
                    <span class="badge bg-primary">{{ $performances->count() }} Records</span>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter card-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%;">Staff Member</th>
                                <th style="width: 12%;">Month</th>
                                <th style="width: 15%;">Attendance</th>
                                <th style="width: 15%;">Task Completion</th>
                                <th style="width: 10%;">Feedback</th>
                                <th style="width: 12%;" class="text-center">Overall Score</th>
                                <th style="width: 8%;">Grade</th>
                                <th style="width: 8%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performances as $performance)
                            <tr>
                                <td>
                                    <a href="{{ route('staff.show', $performance->staff) }}" class="text-decoration-none">
                                        <strong>{{ $performance->staff->name }}</strong>
                                    </a>
                                    <div class="small text-muted">{{ $performance->staff->position }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ \Carbon\Carbon::parse($performance->month)->format('M Y') }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($performance->month)->format('F') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-fill me-2" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $performance->attendance_rate }}%"></div>
                                        </div>
                                        <div class="small">{{ number_format($performance->attendance_rate, 0) }}%</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-fill me-2" style="height: 6px;">
                                            <div class="progress-bar bg-info" style="width: {{ $performance->task_completion_rate }}%"></div>
                                        </div>
                                        <div class="small">{{ number_format($performance->task_completion_rate, 0) }}%</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-fill me-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ ($performance->customer_feedback_score / 5) * 100 }}%"></div>
                                        </div>
                                        <div class="small">{{ number_format($performance->customer_feedback_score, 1) }}/5</div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $score = $performance->overall_performance;
                                        $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $color }} fs-6 fw-bold">
                                        {{ number_format($score, 1) }}%
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-secondary">
                                        {{ $performance->grade }}
                                    </span>
                                </td>
                                <td class="text-end align-middle" style="vertical-align: middle;">
                                    <div class="btn-group d-flex justify-content-end" role="group">
                                        <a href="{{ route('staff-performance.show', $performance) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('staff-performance.edit', $performance) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('staff-performance.destroy', $performance) }}" method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this record?');" class="d-inline">
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

        <div class="mt-4">
            {{ $performances->links() }}
        </div>
    @endif
</div>
@endsection