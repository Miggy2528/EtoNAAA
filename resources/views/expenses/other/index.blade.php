@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h2><i class="fas fa-receipt text-info"></i> Other Expenses</h2>
            <p class="text-muted">Supplies, maintenance, marketing, and more</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.other.create') }}" class="btn btn-info">
                <i class="fas fa-plus"></i> Add Expense
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Expense Date</th>
                            <th class="text-end">Amount (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->category }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '—' }}</td>
                            <td class="text-end">{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No expenses found.</td>
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
