@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h2><i class="fas fa-bolt text-warning"></i> Utility Expenses</h2>
            <p class="text-muted">List of utility bills by billing period</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.utilities.create') }}" class="btn btn-warning">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>

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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->type }}</td>
                            <td>{{ $expense->billing_period }}</td>
                            <td class="text-end">{{ number_format($expense->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $expense->status === 'paid' ? 'success' : ($expense->status === 'overdue' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($expense->status) }}
                                </span>
                            </td>
                            <td>{{ $expense->due_date ? $expense->due_date->format('Y-m-d') : '—' }}</td>
                            <td>{{ $expense->paid_date ? $expense->paid_date->format('Y-m-d') : '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No utility expenses found.</td>
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
@endsection
