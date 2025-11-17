@extends('layouts.butcher')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Add Payroll Record</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.payroll.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Staff</label>
                                <select name="user_id" class="form-select" required>
                                    @foreach($staff as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Month</label>
                                <input type="number" name="month" class="form-control" min="1" max="12" value="{{ date('n') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Year</label>
                                <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Basic Salary (₱)</label>
                                <input type="number" step="0.01" name="basic_salary" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bonuses (₱)</label>
                                <input type="number" step="0.01" name="bonuses" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Deductions (₱)</label>
                                <input type="number" step="0.01" name="deductions" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-success"><i class="fas fa-save"></i> Save</button>
                            <a href="{{ route('expenses.payroll.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
