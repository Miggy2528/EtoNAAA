@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">Supplier Management</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
                        </ol>
                    </nav>
                </div>
                @can('create', App\Models\Supplier::class)
                <div>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Add New Supplier
                    </a>
                    <a href="{{ route('purchases.create') }}" class="btn btn-success btn-lg ms-2">
                        <i class="fas fa-shopping-cart me-2"></i>Order Products
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-truck text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ $suppliers->count() }}</h5>
                            <p class="mb-0 text-muted">Total Suppliers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ $suppliers->where('status', 'active')->count() }}</h5>
                            <p class="mb-0 text-muted">Active Suppliers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-4 border-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-tags text-info fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ $suppliers->groupBy('type')->count() }}</h5>
                            <p class="mb-0 text-muted">Supplier Types</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ $suppliers->where('status', 'inactive')->count() }}</h5>
                            <p class="mb-0 text-muted">Inactive Suppliers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Supplier Directory</h5>
                        <div class="d-flex align-items-center">
                            <div class="input-group input-group-sm w-100" style="max-width: 300px;">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Search suppliers..." id="supplierSearch">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Supplier</th>
                                    <th>Contact</th>
                                    <th>Type</th>
                                    <th>Performance</th>
                                    <th>Procurements</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="supplierTableBody">
                                @forelse($suppliers as $supplier)
                                @php
                                    $procurement = $supplier->procurements->first();
                                    $totalDeliveries = $procurement->total_deliveries ?? 0;
                                    $onTimeDeliveries = $procurement->on_time_deliveries ?? 0;
                                    $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100, 1) : 0;
                                @endphp
                                <tr class="supplier-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($supplier->photo)
                                                <img src="{{ Storage::url($supplier->photo) }}" alt="{{ $supplier->name }}" class="rounded-circle me-3" width="50" height="50">
                                            @else
                                                <div class="bg-light rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $supplier->name }}</h6>
                                                <small class="text-muted">{{ $supplier->shopname }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <small><i class="fas fa-envelope me-1 text-muted"></i> {{ $supplier->email }}</small>
                                        </div>
                                        <div>
                                            <small><i class="fas fa-phone me-1 text-muted"></i> {{ $supplier->phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ ucfirst($supplier->type->value ?? '') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($totalDeliveries > 0)
                                            <div class="d-flex align-items-center">
                                                <div class="progress" style="width: 80px; height: 10px;">
                                                    <div class="progress-bar {{ $onTimeRate >= 90 ? 'bg-success' : ($onTimeRate >= 75 ? 'bg-warning' : 'bg-danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $onTimeRate }}%" 
                                                         aria-valuenow="{{ $onTimeRate }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="ms-2 fw-bold">{{ $onTimeRate }}%</small>
                                            </div>
                                            <small class="text-muted">On-time rate</small>
                                        @else
                                            <span class="text-muted">No data</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-boxes text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $supplier->procurements_count }}</div>
                                                <small class="text-muted">deliveries</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $supplier->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex gap-2 justify-content-center" role="group">
                                            @can('view', $supplier)
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-primary btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('update', $supplier)
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm text-white" title="Edit Supplier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('delete', $supplier)
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this supplier?')" title="Delete Supplier">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr id="noResultsRow">
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-center">
                                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                            <h5>No suppliers found</h5>
                                            <p class="text-muted">Get started by adding a new supplier.</p>
                                            @can('create', App\Models\Supplier::class)
                                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Add Supplier</a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing <span id="showingCount">{{ $suppliers->firstItem() }}</span> to <span id="toCount">{{ $suppliers->lastItem() }}</span> of <span id="totalCount">{{ $suppliers->total() }}</span> suppliers
                        </div>
                        <div>
                            {{ $suppliers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
document.getElementById('supplierSearch').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.supplier-row');
    let visibleCount = 0;
    
    // Show/hide rows based on search term
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (searchTerm === '' || text.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const noResultsRow = document.getElementById('noResultsRow');
    if (noResultsRow) {
        if (visibleCount === 0 && searchTerm !== '') {
            noResultsRow.style.display = '';
        } else {
            noResultsRow.style.display = 'none';
        }
    }
    
    // Update counts
    document.getElementById('showingCount').textContent = visibleCount > 0 ? '1' : '0';
    document.getElementById('toCount').textContent = visibleCount;
    document.getElementById('totalCount').textContent = visibleCount;
});

// Focus the search input on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('supplierSearch').focus();
});
</script>
@endpush
