<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Shopping Cart</title>

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

        /* Navbar Styles */
        .navbar {
            background-color: #8B0000 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }

        .navbar .nav-link {
            color: white !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .navbar .nav-link:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }

        .dropdown-menu {
            background-color: white;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .dropdown-item {
            color: #333;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Buttons */
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

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn-outline-danger {
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--card-hover-shadow);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            font-weight: 600;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .quantity-input {
            width: 100px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
            font-size: 1rem;
            text-align: center;
        }

        .quantity-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.25);
        }

        .summary-card {
            position: sticky;
            top: 20px;
            border-radius: 12px;
        }

        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .product-title {
            font-weight: 600;
            color: #212529;
            margin-bottom: 3px;
            font-size: 1rem;
        }

        .product-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .price-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .unit-price {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .price-per-unit {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .item-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .item-total-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-input {
            width: 70px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 6px 8px;
            font-size: 0.9rem;
            text-align: center;
        }

        .empty-cart {
            background: white;
            border-radius: 12px;
            padding: 50px 30px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .empty-cart i {
            font-size: 4rem;
            color: #ced4da;
            margin-bottom: 20px;
        }

        .empty-cart h5 {
            color: #6c757d;
            margin-bottom: 15px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }

        .summary-total {
            border-top: 2px solid #eee;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .summary-total .amount {
            color: var(--primary-color);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-checkout {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-checkout:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: white;
        }

        .btn-continue-shopping {
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
        }

        .btn-clear-cart {
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
        }

        .stock-indicator {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .in-stock {
            background-color: #d4edda;
            color: #155724;
        }

        .low-stock {
            background-color: #fff3cd;
            color: #856404;
        }

        .out-of-stock {
            background-color: #f8d7da;
            color: #721c24;
        }

        .update-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .update-btn:hover {
            background-color: var(--secondary-color);
        }

        .remove-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
                <a class="nav-link" href="{{ route('customer.products') }}">
                    <i class="fas fa-store me-1"></i>Products
                </a>
                <a class="nav-link active" href="{{ route('customer.cart') }}">
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
                    <ul class="dropdown-menu dropdown-menu-end">
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
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($cartItems->count() > 0)
                            @foreach($cartItems as $item)
                                <div class="cart-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 text-center">
                                            @if($item->options->image)
                                                <img src="{{ Storage::url($item->options->image) }}" 
                                                     alt="{{ $item->name ?? 'Product' }}" class="cart-item-image">
                                            @else
                                                <div class="cart-item-image d-flex align-items-center justify-content-center bg-light">
                                                    <i class="fas fa-image text-muted fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <h6 class="product-title">{{ $item->name ?? 'Product' }}</h6>
                                            <div class="product-meta">
                                                <div>Code: {{ $item->options->code }}</div>
                                                <div>Unit: {{ $item->options->unit }}</div>
                                            </div>
                                            <div class="mt-1">
                                                @if($item->options->stock > 5)
                                                    <span class="stock-indicator in-stock">
                                                        <i class="fas fa-check-circle me-1"></i>In Stock
                                                    </span>
                                                @elseif($item->options->stock > 0)
                                                    <span class="stock-indicator low-stock">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Low Stock ({{ $item->options->stock }} left)
                                                    </span>
                                                @else
                                                    <span class="stock-indicator out-of-stock">
                                                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="price-info">
                                                <div class="unit-price">₱{{ number_format($item->price, 2) }}</div>
                                                <div class="price-per-unit">
                                                    per {{ $item->options->unit ?? 'kg' }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="quantity-controls">
                                                <form action="{{ route('customer.cart.update', $item->rowId) }}" method="POST" class="d-flex align-items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number" name="quantity" value="{{ $item->qty }}" 
                                                           min="1" max="{{ $item->options->stock }}" 
                                                           class="quantity-input">
                                                    <button type="submit" class="btn btn-sm btn-success update-btn" title="Update quantity" style="padding: 4px 8px; font-size: 0.75rem;">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="item-total-container">
                                                <div class="item-total">₱{{ number_format($item->subtotal, 2) }}</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-1 text-center">
                                            <form action="{{ route('customer.cart.remove', $item->rowId) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn remove-btn" 
                                                        onclick="return confirm('Remove this item from cart?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <form action="{{ route('customer.cart.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-clear-cart btn-outline-danger" 
                                            onclick="return confirm('Clear entire cart?')">
                                        <i class="fas fa-trash me-1"></i>Clear Cart
                                    </button>
                                </form>
                                
                                <a href="{{ route('customer.products') }}" class="btn btn-continue-shopping btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i>Continue Shopping
                                </a>
                            </div>
                        @else
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h5>Your Cart is Empty</h5>
                                <p class="text-muted mb-4">Add some products to your cart to get started.</p>
                                <a href="{{ route('customer.products') }}" class="btn btn-primary">
                                    <i class="fas fa-store me-1"></i>Browse Products
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card summary-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($cartItems->count() > 0)
                            <div class="summary-item">
                                <span>Subtotal ({{ $cartItems->sum('qty') }} {{ $cartItems->sum('qty') > 1 ? 'items' : 'item' }}):</span>
                                <span>₱{{ number_format($cartSubtotal, 2) }}</span>
                            </div>
                            <div class="summary-total">
                                <span>Total:</span>
                                <span class="amount">₱{{ number_format($cartSubtotal, 2) }}</span>
                            </div>
                            
                            <a href="{{ route('customer.checkout') }}" class="btn btn-checkout w-100 mt-4">
                                <i class="fas fa-credit-card me-1"></i>Proceed to Checkout
                            </a>
                            
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No items in cart</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    
    <script>
        // Auto-submit cart update when quantity changes
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.quantity-input');
            
            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Find the closest form and submit it
                    const form = this.closest('form');
                    if (form) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>