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
    
    .metric-input {
        font-size: 1.1rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
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
                <i class="fas fa-edit me-2"></i>Edit Performance Record
            </h1>
        </div>
        <div class="col-md-6 text-md-end text-start">
            <a href="{{ route('staff-performance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Records
            </a>
        </div>
    </div>

    <x-alert/>

    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Performance Evaluation Form
            </h5>
        </div>
        
        <form method="POST" action="{{ route('staff-performance.update', $staffPerformance) }}" id="performanceForm">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required metric-label">Staff Member</label>
                            <select name="staff_id" id="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ old('staff_id', $staffPerformance->staff_id) == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} - {{ $member->position }}
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required metric-label">Evaluation Month</label>
                            <input type="month" name="month" id="month" class="form-control @error('month') is-invalid @enderror" 
                                value="{{ old('month', $staffPerformance->month) }}" required>
                            @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics Section -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-4 pb-2 border-bottom">
                            <i class="fas fa-tasks me-2"></i>Performance Metrics
                        </h5>
                    </div>
                </div>

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
                                <div class="mb-3">
                                    <input type="number" name="attendance_rate" id="attendance_rate" 
                                        class="form-control form-control-lg metric-input text-center @error('attendance_rate') is-invalid @enderror" 
                                        value="{{ old('attendance_rate', $staffPerformance->attendance_rate) }}" min="0" max="100" step="0.1" required>
                                    @error('attendance_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="small text-muted text-center mb-2">Percentage (0-100%)</div>
                                <div class="mt-2">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-primary" id="attendanceProgress" style="width: {{ $staffPerformance->attendance_rate }}%"></div>
                                    </div>
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
                                <div class="mb-3">
                                    <input type="number" name="task_completion_rate" id="task_completion_rate" 
                                        class="form-control form-control-lg metric-input text-center @error('task_completion_rate') is-invalid @enderror" 
                                        value="{{ old('task_completion_rate', $staffPerformance->task_completion_rate) }}" min="0" max="100" step="0.1" required>
                                    @error('task_completion_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="small text-muted text-center mb-2">Percentage (0-100%)</div>
                                <div class="mt-2">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-info" id="taskProgress" style="width: {{ $staffPerformance->task_completion_rate }}%"></div>
                                    </div>
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
                                <div class="mb-3">
                                    <input type="number" name="customer_feedback_score" id="customer_feedback_score" 
                                        class="form-control form-control-lg metric-input text-center @error('customer_feedback_score') is-invalid @enderror" 
                                        value="{{ old('customer_feedback_score', $staffPerformance->customer_feedback_score) }}" min="1" max="5" step="0.1" required>
                                    @error('customer_feedback_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="small text-muted text-center mb-2">Scale of 1-5</div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-warning me-1" id="star{{ $i }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Preview -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>Calculated Overall Performance
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                                    <div class="mb-3 mb-md-0 text-center">
                                        <h5 class="alert-heading mb-1">Overall Score</h5>
                                        <div class="display-4 fw-bold text-primary">
                                            <span id="calculatedScore">{{ number_format($staffPerformance->overall_performance, 1) }}</span>%
                                        </div>
                                    </div>
                                    <div class="text-center mb-3 mb-md-0">
                                        <h5 class="alert-heading mb-1">Performance Grade</h5>
                                        <span id="performanceGrade" class="badge bg-success fs-5">{{ $staffPerformance->grade }}</span>
                                    </div>
                                    <div class="text-center">
                                        <h5 class="alert-heading mb-1">Status</h5>
                                        <span id="performanceStatus" class="badge bg-info fs-6">On Track</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="small text-muted text-center mt-3">
                                    Formula: (Attendance × 30%) + (Task Completion × 40%) + (Feedback × 30%)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label metric-label">Additional Remarks</label>
                            <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                rows="4" placeholder="Add any additional comments or observations about this performance evaluation...">{{ old('remarks', $staffPerformance->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Provide context or specific examples to support the evaluation</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        All fields marked with <span class="text-danger">*</span> are required
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Update Performance Record
                        </button>
                        <a href="{{ route('staff-performance.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('page-scripts')
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
<script>
    // Auto-calculate overall performance
    function calculateOverallPerformance() {
        const attendance = parseFloat(document.getElementById('attendance_rate').value) || 0;
        const taskCompletion = parseFloat(document.getElementById('task_completion_rate').value) || 0;
        const feedback = parseFloat(document.getElementById('customer_feedback_score').value) || 0;
        
        const attendanceScore = (attendance / 100) * 0.3;
        const taskScore = (taskCompletion / 100) * 0.4;
        const feedbackScore = (feedback / 5) * 0.3;
        
        const overall = (attendanceScore + taskScore + feedbackScore) * 100;
        
        document.getElementById('calculatedScore').textContent = overall.toFixed(1);
        
        // Update progress bars
        document.getElementById('attendanceProgress').style.width = attendance + '%';
        document.getElementById('taskProgress').style.width = taskCompletion + '%';
        
        // Update star ratings
        for(let i = 1; i <= 5; i++) {
            const star = document.getElementById('star' + i);
            if(i <= Math.floor(feedback)) {
                star.className = 'fas fa-star text-warning';
            } else if(i <= feedback) {
                star.className = 'fas fa-star-half-alt text-warning';
            } else {
                star.className = 'far fa-star text-muted';
            }
        }
        
        // Determine grade and status
        let grade, gradeClass, status, statusClass;
        if(overall >= 90) {
            grade = 'Outstanding';
            gradeClass = 'bg-success';
            status = 'Exceptional';
            statusClass = 'bg-success';
        } else if(overall >= 80) {
            grade = 'Excellent';
            gradeClass = 'bg-success';
            status = 'On Track';
            statusClass = 'bg-info';
        } else if(overall >= 70) {
            grade = 'Good';
            gradeClass = 'bg-info';
            status = 'Satisfactory';
            statusClass = 'bg-warning';
        } else if(overall >= 60) {
            grade = 'Satisfactory';
            gradeClass = 'bg-warning';
            status = 'Needs Attention';
            statusClass = 'bg-warning';
        } else {
            grade = 'Needs Improvement';
            gradeClass = 'bg-danger';
            status = 'Critical';
            statusClass = 'bg-danger';
        }
        
        const gradeElement = document.getElementById('performanceGrade');
        gradeElement.textContent = grade;
        gradeElement.className = 'badge ' + gradeClass + ' fs-5';
        
        const statusElement = document.getElementById('performanceStatus');
        statusElement.textContent = status;
        statusElement.className = 'badge ' + statusClass + ' fs-6';
    }
    
    // Add event listeners
    document.getElementById('attendance_rate').addEventListener('input', calculateOverallPerformance);
    document.getElementById('task_completion_rate').addEventListener('input', calculateOverallPerformance);
    document.getElementById('customer_feedback_score').addEventListener('input', calculateOverallPerformance);
    
    // Calculate on page load
    calculateOverallPerformance();

    // Show success toast on form submission
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif
</script>
@endpush
@endsection
