@extends('layouts.butcher')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Utility Expense</h5>
                </div>
                <div class="card-body">
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('expenses.utilities.update', $expense->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="Electricity" {{ $expense->type == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                    <option value="Water" {{ $expense->type == 'Water' ? 'selected' : '' }}>Water</option>
                                    <option value="Rent" {{ $expense->type == 'Rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="Internet" {{ $expense->type == 'Internet' ? 'selected' : '' }}>Internet</option>
                                    <option value="Misc" {{ $expense->type == 'Misc' ? 'selected' : '' }}>Misc</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Billing Period (YYYY-MM)</label>
                                <input type="text" name="billing_period" class="form-control" value="{{ old('billing_period', $expense->billing_period) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Amount (₱)</label>
                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $expense->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $expense->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ $expense->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $expense->due_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Paid Date</label>
                                <input type="date" name="paid_date" class="form-control" value="{{ old('paid_date', $expense->paid_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                            <a href="{{ route('expenses.utilities.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
