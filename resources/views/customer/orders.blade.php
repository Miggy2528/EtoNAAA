<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - My Orders</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    @livewireStyles

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

        /* Navbar */
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff !important;
        }

        .navbar-nav .nav-link {
            color: #f1f1f1 !important;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            transform: translateY(-1px);
            text-decoration: none !important;
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

        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-view {
            border-radius: 6px;
            font-weight: 500;
            padding: 6px 12px;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            transform: translateY(-1px);
        }

        .btn-cancel {
            border-radius: 6px;
            font-weight: 500;
            padding: 6px 12px;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            transform: translateY(-1px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--card-hover-shadow);
        }

        .card-header {
            background-color: var(--primary-color);
            color: #fff;
            border-bottom: none;
            font-weight: 600;
        }

        /* Table */
        .table th {
            background-color: #f1f1f1;
            color: #333;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: #f9ecec;
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-for-delivery {
            background-color: #cfe2ff;
            color: #084298;
        }

        .status-complete {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        /* Layout spacing */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .page-header h5 {
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        /* Order Item Display */
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .order-item:last-child {
            margin-bottom: 0;
        }

        .item-badge {
            background-color: var(--primary-color);
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-right: 8px;
        }

        .empty-orders {
            background: white;
            border-radius: 12px;
            padding: 50px 30px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .empty-orders i {
            font-size: 4rem;
            color: #ced4da;
            margin-bottom: 20px;
        }

        .empty-orders h5 {
            color: #6c757d;
            margin-bottom: 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .btn-view, .btn-cancel {
                padding: 4px 8px;
                font-size: 0.8rem;
            }
            
            .status-badge {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop - Customer Portal
            </a>

            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('customer.products') }}">
                    <i class="fas fa-store me-1"></i>Products
                </a>
                <a class="nav-link" href="{{ route('customer.cart') }}">
                    <i class="fas fa-shopping-cart me-1"></i>Cart
                    <span class="badge bg-danger ms-1">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('customer')->count() }}</span>
                </a>
                <a class="nav-link active" href="{{ route('customer.orders') }}">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>

                @livewire('customer-notification-navbar')

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Customer' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user-edit me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">

        <!-- Back Button -->
        <div class="page-header">
            <button class="btn-back" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <h5><i class="fas fa-shopping-cart me-2"></i>My Orders</h5>
        </div>

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

        <div class="card">
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>
                                            {{ $order->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @foreach($order->details->take(2) as $detail)
                                                <div class="order-item">
                                                    <span class="item-badge">{{ $detail->quantity }}</span>
                                                    <span>{{ $detail->product->name ?? 'Product' }}</span>
                                                </div>
                                            @endforeach
                                            @if($order->details->count() > 2)
                                                <small class="text-muted">+{{ $order->details->count() - 2 }} more</small>
                                            @endif
                                        </td>
                                        <td><strong>₱{{ number_format($order->details->sum('total'), 2) }}</strong></td>
                                        <td>
                                            @php
                                                $statusClass = match($order->order_status) {
                                                    \App\Enums\OrderStatus::PENDING => 'status-pending',
                                                    \App\Enums\OrderStatus::FOR_DELIVERY => 'status-for-delivery',
                                                    \App\Enums\OrderStatus::COMPLETE => 'status-complete',
                                                    \App\Enums\OrderStatus::CANCELLED => 'status-cancelled',
                                                    default => 'status-pending'
                                                };
                                            @endphp
                                            <span class="badge status-badge {{ $statusClass }}">
                                                {{ $order->order_status->label() }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-view btn-outline-primary me-1">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>

                                            @if($order->order_status === \App\Enums\OrderStatus::FOR_DELIVERY)
                                                <form action="{{ route('customer.orders.received', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-view btn-outline-success me-1">
                                                        <i class="fas fa-check me-1"></i>Received
                                                    </button>
                                                </form>
                                            @endif

                                            @if($order->order_status === \App\Enums\OrderStatus::PENDING)
                                                <button type="button" class="btn btn-cancel btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal{{ $order->id }}">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>

                                                <!-- Cancel Modal -->
                                                <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Cancel Order #{{ $order->id }}</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="alert alert-warning">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>Are you sure you want to cancel this order? This action cannot be undone.
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                                                                        <textarea class="form-control" name="reason" rows="3" required placeholder="Please provide a reason..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-orders">
                        <i class="fas fa-shopping-cart"></i>
                        <h5>No Orders Yet</h5>
                        <p class="text-muted mb-4">You haven't placed any orders yet.</p>
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script>
        function goBack() {
            if (document.referrer && document.referrer !== window.location.href) {
                window.history.back();
            } else {
                window.location.href = "{{ route('customer.dashboard') }}";
            }
        }
    </script>
    @livewireScripts
</body>
</html>