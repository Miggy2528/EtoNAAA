@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-ban text-danger me-2"></i>Voided Expenses
            </h1>
            <p class="text-muted">All voided expenses across utilities, payroll, and other categories</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Expenses
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-ban me-1"></i>Total Voided</h6>
                    <h3>{{ $allVoidedExpenses->count() }}</h3>
                    <small>Expenses</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-dollar-sign me-1"></i>Total Amount Voided</h6>
                    <h3>₱{{ number_format($allVoidedExpenses->sum('amount'), 2) }}</h3>
                    <small>Across all categories</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-layer-group me-1"></i>Categories</h6>
                    <h3>{{ $allVoidedExpenses->unique('type')->count() }}</h3>
                    <small>Expense Types</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Voided Expenses Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>All Voided Expenses</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-end">Amount (₱)</th>
                            <th>Date/Period</th>
                            <th>Voided At</th>
                            <th>Voided By</th>
                            <th>Void Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allVoidedExpenses as $expense)
                        <tr class="table-danger">
                            <td>
                                <span class="badge 
                                    @if($expense['type'] == 'Utility') bg-warning
                                    @elseif($expense['type'] == 'Payroll') bg-success
                                    @else bg-info
                                    @endif
                                ">
                                    {{ $expense['type'] }}
                                </span>
                            </td>
                            <td>{{ $expense['category'] }}</td>
                            <td>
                                @if($expense['description'])
                                    {{ Str::limit($expense['description'], 50) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <del class="text-danger"><strong>{{ number_format($expense['amount'], 2) }}</strong></del>
                            </td>
                            <td>{{ $expense['date'] }}</td>
                            <td>
                                @if($expense['voided_at'])
                                    {{ \Carbon\Carbon::parse($expense['voided_at'])->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $expense['voided_by'] }}</span>
                            </td>
                            <td>
                                @if($expense['void_reason'])
                                    <small class="text-muted">{{ $expense['void_reason'] }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                                <h5 class="text-muted">No Voided Expenses</h5>
                                <p class="text-muted">All expenses are active. Voided expenses will appear here.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
