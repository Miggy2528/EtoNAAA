@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h2><i class="fas fa-users text-success"></i> Payroll</h2>
            <p class="text-muted">Monthly payroll records</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.payroll.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Record
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th class="text-end">Basic (₱)</th>
                            <th class="text-end">Bonuses (₱)</th>
                            <th class="text-end">Deductions (₱)</th>
                            <th class="text-end">Total (₱)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                        <tr>
                            <td>{{ $record->user->name ?? 'N/A' }}</td>
                            <td>{{ date('F', mktime(0, 0, 0, $record->month, 1)) }}</td>
                            <td>{{ $record->year }}</td>
                            <td class="text-end">{{ number_format($record->basic_salary, 2) }}</td>
                            <td class="text-end">{{ number_format($record->bonuses, 2) }}</td>
                            <td class="text-end">{{ number_format($record->deductions, 2) }}</td>
                            <td class="text-end">{{ number_format($record->total_salary, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $record->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No payroll records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
