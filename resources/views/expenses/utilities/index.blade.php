@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h2><i class="fas fa-bolt text-warning"></i> Utility Expenses</h2>
            <p class="text-muted">List of utility bills by billing period</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('expenses.utilities.create') }}" class="btn btn-warning">
                <i class="fas fa-plus"></i> Add New
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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" id="expenseTabs" role="tablist" style="border-bottom: 3px solid #dee2e6;">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') != 'voided' ? 'active' : '' }}" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="{{ request('tab') != 'voided' ? 'true' : 'false' }}" style="font-weight: 600; font-size: 1.1rem; padding: 12px 24px; {{ request('tab') != 'voided' ? 'background-color: #ffc107; color: #000; border-color: #ffc107;' : 'color: #6c757d;' }}">
                <i class="fas fa-list me-2"></i>Active Expenses
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') == 'voided' ? 'active' : '' }}" id="voided-tab" data-bs-toggle="tab" data-bs-target="#voided" type="button" role="tab" aria-controls="voided" aria-selected="{{ request('tab') == 'voided' ? 'true' : 'false' }}" style="font-weight: 600; font-size: 1.1rem; padding: 12px 24px; {{ request('tab') == 'voided' ? 'background-color: #dc3545; color: #fff; border-color: #dc3545;' : 'color: #6c757d;' }}">
                <i class="fas fa-ban me-2"></i>Voided Expenses
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="expenseTabContent">
        <!-- Active Expenses Tab -->
        <div class="tab-pane fade {{ request('tab') != 'voided' ? 'show active' : '' }}" id="active" role="tabpanel" aria-labelledby="active-tab">

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Billing Period</th>
                            <th class="text-end">Amount (₱)</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Paid Date</th>
                            <th>Notes</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->type }}</td>
                            <td>{{ $expense->billing_period }}</td>
                            <td class="text-end">{{ number_format($expense->amount, 2) }}</td>
                            <td>
                                @if($expense->status === 'paid')
                                    <span class="badge bg-success text-dark">
                                        <i class="fas fa-check-circle me-1"></i>{{ ucfirst($expense->status) }}
                                    </span>
                                @elseif($expense->status === 'overdue')
                                    <span class="badge bg-danger text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>{{ ucfirst($expense->status) }}
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i>{{ ucfirst($expense->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $expense->due_date ? $expense->due_date->format('Y-m-d') : '—' }}</td>
                            <td>{{ $expense->paid_date ? $expense->paid_date->format('Y-m-d') : '—' }}</td>
                            <td>
                                @if($expense->notes)
                                    <small>{{ Str::limit($expense->notes, 50) }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('expenses.utilities.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="voidExpense({{ $expense->id }}, '{{ $expense->type }}')" title="Void">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No utility expenses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
        </div>

        <!-- Voided Expenses Tab -->
        <div class="tab-pane fade {{ request('tab') == 'voided' ? 'show active' : '' }}" id="voided" role="tabpanel" aria-labelledby="voided-tab">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Billing Period</th>
                                    <th class="text-end">Amount (₱)</th>
                                    <th>Status</th>
                                    <th>Voided At</th>
                                    <th>Voided By</th>
                                    <th>Void Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($voidedExpenses as $expense)
                                <tr class="table-danger">
                                    <td>{{ $expense->type }}</td>
                                    <td>{{ $expense->billing_period }}</td>
                                    <td class="text-end"><del>{{ number_format($expense->amount, 2) }}</del></td>
                                    <td>
                                        <span class="badge bg-danger text-white rounded-pill">
                                            <i class="fas fa-ban me-1"></i>Voided
                                        </span>
                                    </td>
                                    <td>{{ $expense->voided_at ? $expense->voided_at->format('M d, Y h:i A') : '—' }}</td>
                                    <td>{{ $expense->voidedBy->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($expense->void_reason)
                                            <small>{{ $expense->void_reason }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No voided expenses found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $voidedExpenses->appends(['tab' => 'voided'])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Void Confirmation Modal -->
<div class="modal fade" id="voidModal" tabindex="-1" aria-labelledby="voidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="voidModalLabel"><i class="fas fa-ban me-2"></i>Void Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="voidForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning!</strong> This action will void the selected expense and remove it from calculations.
                    </div>
                    <p>Are you sure you want to void <strong id="voidExpenseName"></strong>?</p>
                    <div class="mb-3">
                        <label for="voidReason" class="form-label">Void Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="voidReason" name="void_reason" rows="3" required placeholder="Please provide a reason for voiding this expense..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-ban me-1"></i>Void Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
function voidExpense(id, name) {
    console.log('Void button clicked:', {id: id, name: name});
    
    try {
        document.getElementById('voidExpenseName').textContent = name;
        document.getElementById('voidForm').action = `/expenses/utilities/${id}/void`;
        document.getElementById('voidReason').value = '';
        
        console.log('Form action set to:', document.getElementById('voidForm').action);
        
        const modalElement = document.getElementById('voidModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal shown successfully');
        } else {
            console.error('Modal element not found');
        }
    } catch (error) {
        console.error('Error in voidExpense function:', error);
        alert('Error opening void modal: ' + error.message);
    }
}
</script>
