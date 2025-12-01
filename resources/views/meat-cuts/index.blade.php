@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-cut me-2"></i>Meat Cuts Management
            </h1>
            <p class="text-muted">Manage all meat cuts in your inventory system</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('meat-cuts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Meat Cut
                </a>
                <a href="{{ route('meat-cuts.create', ['type' => 'by-product']) }}" class="btn btn-info text-white">
                    <i class="fas fa-recycle me-2"></i>By-Product
                </a>
                <a href="{{ route('meat-cuts.create', ['type' => 'processing']) }}" class="btn btn-warning text-white">
                    <i class="fas fa-blender me-2"></i>Processing Meat
                </a>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header filter-header text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filter Meat Cuts
            </h5>
            <span class="badge bg-light text-primary">{{ $meatCuts->total() }} Total</span>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('meat-cuts.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search by name or type..." 
                               value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Meat Type</label>
                        <select name="meat_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($meatTypes as $type)
                                <option value="{{ $type }}" {{ request('meat_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Quality Grade</label>
                        <select name="cut_type" class="form-select">
                            <option value="">All Grades</option>
                            @foreach($cutTypes as $type)
                                <option value="{{ $type }}" {{ request('cut_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Availability</label>
                        <select name="availability" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('availability') == '1' ? 'selected' : '' }}>Available</option>
                            <option value="0" {{ request('availability') == '0' ? 'selected' : '' }}>Not Available</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-danger me-2">
                            <i class="fas fa-filter me-1"></i>
                        </button>
                        <a href="{{ route('meat-cuts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Meat Cuts Table --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Meat Cuts List
            </h5>
            <div class="small">
                Showing {{ $meatCuts->firstItem() }} to {{ $meatCuts->lastItem() }} of {{ $meatCuts->total() }} results
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Meat Type</th>
                            <th>Quality Grade</th>
                            <th>Price/kg</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Min. Stock</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meatCuts as $cut)
                            <tr>
                                <td>
                                    @if($cut->image_path)
                                        <img src="{{ Storage::url($cut->image_path) }}" 
                                             alt="{{ $cut->name }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 80px; height: auto;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $cut->name }}</strong>
                                </td>
                                <td>
                                    @if($cut->meat_type)
                                        <span class="badge bg-info">{{ ucfirst($cut->meat_type) }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cut->quality_grade)
                                        <span class="badge bg-warning text-dark">{{ ucfirst($cut->quality_grade) }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="fw-bold">₱{{ number_format($cut->default_price_per_kg, 2) }}</td>
                                <td>
                                    @if($cut->isLowStock())
                                        <span class="badge bg-warning text-dark">{{ $cut->quantity ?? 0 }}</span>
                                    @else
                                        {{ $cut->quantity ?? 0 }}
                                    @endif
                                </td>
                                <td>
                                    @if($cut->is_by_product)
                                        <span class="badge bg-warning text-dark">By-Product</span>
                                    @elseif($cut->is_processing_meat)
                                        <span class="badge bg-primary">Processing</span>
                                    @else
                                        <span class="badge {{ $cut->is_available ? 'bg-success' : 'bg-danger' }}">
                                            {{ $cut->is_available ? 'Available' : 'Not Available' }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $cut->minimum_stock_level }}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        @if(auth()->user()->isAdmin())
                                        <a href="{{ route('meat-cuts.edit', $cut) }}" 
                                           class="btn btn-sm btn-outline-primary me-1"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('meat-cuts.destroy', $cut) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this meat cut?')"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                            <span class="text-muted small">View Only</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-cut fs-1 text-muted mb-3"></i>
                                    <p class="mb-0">No meat cuts found.</p>
                                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing {{ $meatCuts->firstItem() }} to {{ $meatCuts->lastItem() }} of {{ $meatCuts->total() }} results
                </div>
                <div>
                    {{ $meatCuts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
    // No additional JavaScript needed for meat cuts index page
</script>
@endpush

@endsection

@push('page-styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .bg-danger {
        background-color: var(--primary-color) !important;
    }
    .table th {
        font-weight: 600;
    }
    .badge.bg-info {
        background-color: #17a2b8 !important;
    }
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    .btn-outline-primary:hover {
        color: white !important;
    }
    .btn-outline-danger:hover {
        color: white !important;
    }
    .btn-outline-info {
        color: #17a2b8;
        border-color: #17a2b8;
    }
    .btn-outline-info:hover {
        color: white !important;
        background-color: #17a2b8;
    }
    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }
    .btn-outline-warning:hover {
        color: #212529 !important;
        background-color: #ffc107;
    }
    .filter-header {
        background: linear-gradient(120deg, #007bff, #0056b3) !important;
    }
</style>
@endpush