<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Products</title>

    <!-- Local Fonts -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #4A0404;
            --accent-color: #FF4136;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --card-hover-shadow: 0 10px 15px rgba(0, 0, 0, 0.15);
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        /* Navbar customization */
        .navbar {
            background-color: #8B0000 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #f8d7da !important;
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }

        .product-image-container {
            height: 200px;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
        }

        .product-image {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover .product-image {
            transform: scale(1.05);
        }

        .product-card {
            height: 100%;
        }

        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .low-stock {
            background-color: #ffc107;
            color: #212529;
        }

        .out-of-stock {
            background-color: #dc3545;
            color: white;
        }

        .filter-sidebar {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-sidebar h5 {
            color: var(--primary-color);
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.25);
        }

        .btn-filter {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-filter:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            color: white;
        }

        .btn-clear {
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 5px;
        }

        .product-category {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .unit-badge {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        
        .sold-per-info {
            text-align: center;
            margin: 5px 0;
        }
        
        .unit-info-badge {
            background-color: #fff3cd;
            color: #856404;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid #ffeaa7;
        }

        .availability {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .in-stock {
            color: #198754;
        }

        .low-stock-text {
            color: #ffc107;
        }

        .out-of-stock-text {
            color: #dc3545;
        }

        .add-to-cart-btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .pagination {
            margin-top: 30px;
        }

        .page-link {
            color: var(--primary-color);
            border-radius: 8px;
            margin: 0 2px;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .no-products {
            background: white;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .no-products i {
            font-size: 3rem;
            color: #ced4da;
            margin-bottom: 20px;
        }

        .no-products h5 {
            color: #6c757d;
            margin-bottom: 15px;
        }

        .product-count {
            background-color: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop - Customer Portal
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="{{ route('customer.products') }}">
                    <i class="fas fa-store me-1"></i>Products
                </a>
                <a class="nav-link" href="{{ route('customer.cart') }}">
                    <i class="fas fa-shopping-cart me-1"></i>Cart
                    <span class="badge bg-danger ms-1">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('customer')->count() }}</span>
                </a>
              
                <a class="nav-link" href="{{ route('customer.orders') }}">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>

                <!-- Livewire Notification Bell -->
                @livewire('customer-notification-navbar')

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Customer' }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filter-sidebar">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filters
                    </h5>
                    
                    <!-- Search -->
                    <form method="GET" action="{{ route('customer.products') }}">
                        <div class="mb-3 mt-4">
                            <label for="search" class="form-label">Search Products</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Price Range Filter -->
                        <div class="mb-3">
                            <label for="price_range" class="form-label">Price Range</label>
                            <select class="form-select" id="price_range" name="price_range">
                                <option value="">All Prices</option>
                                <option value="0-100" {{ request('price_range') == '0-100' ? 'selected' : '' }}>₱0 - ₱100</option>
                                <option value="101-200" {{ request('price_range') == '101-200' ? 'selected' : '' }}>₱101 - ₱200</option>
                                <option value="201-500" {{ request('price_range') == '201-500' ? 'selected' : '' }}>₱201 - ₱500</option>
                                <option value="501+" {{ request('price_range') == '501+' ? 'selected' : '' }}>₱501 and above</option>
                            </select>
                        </div>

                        <!-- Unit Filter -->
                        <div class="mb-3">
                            <label for="unit" class="form-label">Packaging / Unit</label>
                            <select class="form-select" id="unit" name="unit">
                                <option value="">All Units</option>
                                <option value="kg" {{ request('unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                <option value="piece" {{ request('unit') == 'piece' ? 'selected' : '' }}>Piece</option>
                                <option value="package" {{ request('unit') == 'package' ? 'selected' : '' }}>Package</option>
                                <option value="box" {{ request('unit') == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="dozen" {{ request('unit') == 'dozen' ? 'selected' : '' }}>Dozen</option>
                            </select>
                        </div>

                        <!-- Stock Status Filter -->
                        <div class="mb-3">
                            <label for="stock_status" class="form-label">Availability / Stock Status</label>
                            <select class="form-select" id="stock_status" name="stock_status">
                                <option value="">All Stock Status</option>
                                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-filter w-100">
                            <i class="fas fa-search me-1"></i>Apply Filters
                        </button>
                        
                        @if(request('search') || request('category') || request('price_range') || request('unit') || request('stock_status'))
                            <a href="{{ route('customer.products') }}" class="btn btn-outline-secondary btn-clear w-100 mt-2">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-store me-2"></i>Our Products
                    </h2>
                    <div class="product-count">
                        {{ $products->total() }} products found
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card product-card">
                                    <div class="product-image-container">
                                        @if($product->product_image)
                                            <img src="{{ asset('storage/products/' . $product->product_image) }}" 
                                                 alt="{{ $product->name ?? 'Product' }}" class="product-image">
                                        @else
                                            <div class="product-image d-flex align-items-center justify-content-center h-100">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        @if($product->quantity <= 0)
                                            <span class="badge out-of-stock stock-badge">Out of Stock</span>
                                        @elseif($product->quantity <= 5)
                                            <span class="badge low-stock stock-badge">Low Stock</span>
                                        @endif
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="product-title">{{ $product->name ?? 'Unnamed Product' }}</h5>
                                        <p class="product-category">
                                            {{ $product->code ?? 'N/A' }} • {{ $product->category->name ?? 'Uncategorized' }}
                                        </p>
                                        
                                        <div class="price">
                                            ₱{{ number_format($product->price_per_kg ?? 0, 2) }}
                                        </div>
                                        
                                        <div class="product-meta">
                                            <span class="unit-badge">
                                              Sold per  {{ $product->unit->name ?? 'kg' }}
                                            </span>
                                            <span class="availability {{ $product->quantity > 5 ? 'in-stock' : ($product->quantity > 0 ? 'low-stock-text' : 'out-of-stock-text') }}">
                                                <i class="fas fa-box me-1"></i>
                                                {{ $product->quantity ?? 0 }} left
                                            </span>
                                        </div>
                                        
                                        <div class="mt-auto pt-3">
                                            @if($product->quantity > 0)
                                                <form action="{{ route('customer.cart.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-primary add-to-cart-btn w-100">
                                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary add-to-cart-btn w-100" disabled>
                                                    <i class="fas fa-times me-1"></i>Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @else
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <h5>No Products Found</h5>
                        <p class="text-muted mb-4">Try adjusting your search criteria or browse all products.</p>
                        <a href="{{ route('customer.products') }}" class="btn btn-primary">
                            <i class="fas fa-store me-1"></i>Browse All Products
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
</body>
</html>