@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-box-open me-2"></i>Products Management
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Products</h5>
                            <h2 class="mb-0">{{ $products->total() }}</h2>
                        </div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">In Stock</h5>
                            <h2 class="mb-0">{{ $products->filter(function($product) { return $product->quantity > $product->quantity_alert; })->count() }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Low Stock</h5>
                            <h2 class="mb-0">{{ $products->filter(function($product) { return $product->quantity > 0 && $product->quantity <= $product->quantity_alert; })->count() }}</h2>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Out of Stock</h5>
                            <h2 class="mb-0">{{ $products->filter(function($product) { return $product->quantity <= 0; })->count() }}</h2>
                        </div>
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Products</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" id="product-filter-form">
                <div class="row g-3">
                    {{-- Search Bar --}}
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search by product name, code..." 
                               value="{{ request('search') }}">
                    </div>

                    {{-- Category Filter --}}
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Meat Cut Filter --}}
                    <div class="col-md-2">
                        <label class="form-label">Meat Cut</label>
                        <select name="meat_cut_id" class="form-select">
                            <option value="">All Meat Cuts</option>
                            @foreach($meatCuts as $cut)
                                <option value="{{ $cut->id }}" {{ request('meat_cut_id') == $cut->id ? 'selected' : '' }}>
                                    {{ $cut->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Meat Type Filter --}}
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

                    {{-- Quality Filter --}}
                    <div class="col-md-2">
                        <label class="form-label">Quality</label>
                        <select name="quality" class="form-select">
                            <option value="">All Qualities</option>
                            @foreach($qualities as $quality)
                                <option value="{{ $quality }}" {{ request('quality') == $quality ? 'selected' : '' }}>
                                    {{ ucfirst($quality) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Preparation Type Filter --}}
                    <div class="col-md-2">
                        <label class="form-label">Preparation</label>
                        <select name="preparation_type" class="form-select">
                            <option value="">All Preparations</option>
                            @foreach($preparations as $prep)
                                <option value="{{ $prep }}" {{ request('preparation_type') == $prep ? 'selected' : '' }}>
                                    {{ ucfirst($prep) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Meat Subtype Filter (depends on Meat Type) --}}
                    <div class="col-md-2">
                        <label class="form-label">Meat Subtype</label>
                        <select name="meat_subtype" class="form-select">
                            <option value="">All Subtypes</option>
                            @foreach($meatSubtypes as $sub)
                                <option value="{{ $sub }}" {{ request('meat_subtype') == $sub ? 'selected' : '' }}>
                                    {{ ucfirst($sub) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Quality Grade Filter (depends on Quality) --}}
                    <div class="col-md-2">
                        <label class="form-label">Quality Grade</label>
                        <select name="quality_grade" class="form-select">
                            <option value="">All Grades</option>
                            @foreach($qualityGrades as $grade)
                                <option value="{{ $grade }}" {{ request('quality_grade') == $grade ? 'selected' : '' }}>
                                    {{ strtoupper($grade) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Preparation Style Filter (depends on Preparation) --}}
                    <div class="col-md-2">
                        <label class="form-label">Preparation Style</label>
                        <select name="preparation_style" class="form-select">
                            <option value="">All Styles</option>
                            @foreach($preparationStyles as $style)
                                <option value="{{ $style }}" {{ request('preparation_style') == $style ? 'selected' : '' }}>
                                    {{ ucfirst($style) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stock Status Filter --}}
                    <div class="col-md-2">
                        <label class="form-label">Stock Status</label>
                        <select name="stock_status" class="form-select">
                            <option value="">All Status</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    {{-- Filter Buttons --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Display --}}
    <div class="row">
        @forelse($products as $product)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm product-card">
                    @if($product->product_image)
                        <img src="{{ asset('storage/products/' . $product->product_image) }}" 
                             alt="{{ $product->name }}" 
                             class="card-img-top product-image" 
                             style="object-fit:cover;height:200px;width:100%;">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light product-image" style="height:200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                        
                        {{-- Meat Classification Badges --}}
                        <div class="mb-2">
                            <span class="badge bg-secondary">{{ $product->category->name ?? 'N/A' }}</span>
                            @if($product->meatCut)
                                <span class="badge bg-info text-dark">{{ $product->meatCut->name }}</span>
                                @if($product->meatCut->meat_type)
                                    <span class="badge bg-primary">{{ ucfirst($product->meatCut->meat_type) }}</span>
                                @endif
                            @endif
                        </div>
                        
                        {{-- Meat Classification Indicator --}}
                        @if($product->meatCut)
                            <div class="classification-section mb-2">
                                <small class="text-muted fw-bold">CLASSIFICATION:</small>
                                <div class="meat-indicator mt-1">
                                    @if($product->meatCut->animal_type)
                                        <span class="badge bg-dark">{{ ucfirst($product->meatCut->animal_type) }}</span>
                                    @endif
                                    @if($product->meatCut->quality)
                                        <span class="badge bg-success">{{ ucfirst($product->meatCut->quality) }}</span>
                                    @endif
                                    @if($product->meatCut->preparation_type)
                                        <span class="badge bg-warning text-dark">{{ ucfirst($product->meatCut->preparation_type) }}</span>
                                    @endif
                                    @if($product->meatCut->meat_subtype)
                                        <span class="badge bg-secondary">{{ ucfirst($product->meatCut->meat_subtype) }}</span>
                                    @endif
                                    @if($product->meatCut->quality_grade)
                                        <span class="badge bg-primary">{{ strtoupper($product->meatCut->quality_grade) }}</span>
                                    @endif
                                    @if($product->meatCut->preparation_style)
                                        <span class="badge bg-info text-dark">{{ ucfirst(str_replace('_', ' ', $product->meatCut->preparation_style)) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="product-details">
                            <p class="mb-1"><strong>Code:</strong> {{ $product->code }}</p>
                            <p class="mb-1"><strong>Price:</strong> ₱{{ number_format($product->price_per_kg, 2) }}</p>
                            <p class="mb-1">
                                <strong>Stock:</strong> 
                                <span class="badge 
                                    @if($product->quantity <= 0) bg-danger
                                    @elseif($product->quantity <= $product->quantity_alert) bg-warning text-dark
                                    @else bg-success
                                    @endif">
                                    {{ $product->quantity }} {{ $product->unit->name ?? 'pcs' }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Cost/Unit Price:</strong> ₱{{ number_format($product->buying_price ?? 0, 2) }}</p>
                            @if($product->quantity_alert)
                                <p class="mb-1"><small class="text-muted">Alert Level: {{ $product->quantity_alert }}</small></p>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex justify-content-between">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h3 class="mt-2">No products found</h3>
                <p class="text-muted">Try adjusting your search or filter criteria</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-1"></i>Add your first Product
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

@push('page-scripts')
<script>
    // Auto-submit form when any select changes (optional feature)
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('product-filter-form');
        const selects = filterForm.querySelectorAll('select');
        
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // Uncomment the next line if you want filters to auto-apply
                // filterForm.submit();
            });
        });
    });
</script>
@endpush

@push('page-styles')
<style>
    .product-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid rgba(0,0,0,.125);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .product-image {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .card-title {
        color: #333;
        font-weight: 600;
    }
    
    .product-details p {
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .summary-card {
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }
    
    .summary-card:hover {
        transform: translateY(-3px);
    }
    
    .summary-card .card-body {
        padding: 1.25rem;
    }
    
    .summary-card h5 {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .summary-card h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .card-footer .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .meat-indicator .badge {
        font-size: 0.65rem;
        padding: 0.2em 0.4em;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
    }
    
    .classification-section {
        border-top: 1px dashed #dee2e6;
        padding-top: 0.5rem;
    }
    
    .classification-section .text-muted.fw-bold {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }
</style>
@endpush