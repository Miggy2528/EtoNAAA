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

    {{-- Search and Filter Section --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}">
                <div class="row g-3">
                    {{-- Search Bar --}}
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search by product name, code, or meat cut..." 
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
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
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
                <div class="card h-100 shadow-sm">
                    @if($product->product_image)
                        <img src="{{ asset('storage/products/' . $product->product_image) }}" 
                             alt="{{ $product->name }}" 
                             class="card-img-top" 
                             style="object-fit:cover;height:200px;width:100%;">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ $product->name }}</h5>
                        <div class="mb-2">
                            <span class="badge bg-secondary">{{ $product->category->name ?? 'N/A' }}</span>
                            @if($product->meatCut)
                                <span class="badge bg-info text-dark">{{ $product->meatCut->name }}</span>
                            @endif
                        </div>
                        <p class="mb-1"><strong>Code:</strong> {{ $product->code }}</p>
                        <p class="mb-1"><strong>Price:</strong> ₱{{ number_format($product->selling_price, 2) }}</p>
                        <p class="mb-1">
                            <strong>Stock:</strong> 
                            <span class="badge 
                                @if($product->quantity <= 0) bg-danger
                                @elseif($product->quantity <= $product->quantity_alert) bg-warning
                                @else bg-success
                                @endif">
                                {{ $product->quantity }} {{ $product->unit->name ?? 'pcs' }}
                            </span>
                        </p>
                        @if($product->quantity_alert)
                            <p class="mb-1"><small class="text-muted">Alert Level: {{ $product->quantity_alert }}</small></p>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-0 d-flex justify-content-between">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <p class="mt-2">No products found.</p>
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
