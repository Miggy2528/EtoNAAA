@extends('layouts.butcher')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Add Utility Expense</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.utilities.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="Electricity">Electricity</option>
                                    <option value="Water">Water</option>
                                    <option value="Rent">Rent</option>
                                    <option value="Internet">Internet</option>
                                    <option value="Misc">Misc</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Billing Period (YYYY-MM)</label>
                                <input type="text" name="billing_period" class="form-control" value="{{ date('Y-m') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Amount (₱)</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Paid Date</label>
                                <input type="date" name="paid_date" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-warning"><i class="fas fa-save"></i> Save</button>
                            <a href="{{ route('expenses.utilities.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
