<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ config('app.name') }} - Yannis Meatshop</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Local CSS files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    
    <!-- Compiled CSS -->
    @vite(['resources/css/app.css'])

    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #4A0404;
            --accent-color: #FF4136;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand, .nav-link {
            color: white !important;
        }
        
        .nav-link:hover {
            color: rgba(255,255,255,0.8) !important;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .sidebar {
            background-color: white;
            box-shadow: 2px 0 4px rgba(0,0,0,0.05);
        }
        
        .page-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .table th {
            font-weight: 600;
            color: #495057;
        }
        
        .badge {
            font-weight: 500;
        }
        
        .alert {
            border-radius: 8px;
        }
        
        footer {
            background-color: white;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-top: auto;
        }
        
        /* Purple theme for Market Analysis */
        .bg-purple {
            background-color: #6f42c1 !important;
        }
        
        .text-purple {
            color: #6f42c1 !important;
        }
        
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        
        .btn-purple:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
            color: white;
        }
        
        .bg-purple-lt {
            background-color: rgba(111, 66, 193, 0.1) !important;
        }
        
        /* Card link hover effect */
        .card-link {
            text-decoration: none;
            color: inherit;
        }
        
        .card-link:hover .card {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Avatar colors */
        .avatar.bg-blue-lt {
            background-color: rgba(33, 118, 210, 0.1) !important;
        }
        
        .avatar.bg-orange-lt {
            background-color: rgba(247, 103, 7, 0.1) !important;
        }
        
        .avatar.bg-green-lt {
            background-color: rgba(47, 179, 68, 0.1) !important;
        }
        
        .avatar.bg-purple-lt {
            background-color: rgba(112, 58, 192, 0.1) !important;
        }
        
        .avatar.bg-azure-lt {
            background-color: rgba(65, 159, 255, 0.1) !important;
        }
        
        /* Supplier Dashboard Specific Styles */
        .supplier-stat-card {
            border-left: 4px solid #8B0000;
            transition: all 0.3s ease;
        }

        .supplier-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .supplier-stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.25rem;
        }

        .revenue-chart-container {
            position: relative;
            height: 300px;
        }

        .quick-action-card {
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .quick-action-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
        }

        .recent-order-item:not(:last-child) {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .delivery-rating-progress {
            height: 8px;
            border-radius: 4px;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .supplier-stat-card {
                margin-bottom: 1rem;
            }
            
            .quick-action-card {
                margin-bottom: 1rem;
            }
        }
    </style>

    @stack('page-styles')
    @livewireStyles
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        @if(Auth::user()->isSupplier())
                            <a class="nav-link" wire:navigate href="{{ route('supplier.dashboard') }}">
                                <i class="fas fa-chart-line me-1"></i> Dashboard
                            </a>
                        @else
                            <a class="nav-link" wire:navigate href="{{ route('dashboard') }}">
                                <i class="fas fa-chart-line me-1"></i> Dashboard
                            </a>
                        @endif
                    </li>
                    @if(!Auth::user()->isSupplier())
                    <li class="nav-item">
                        <a class="nav-link" wire:navigate href="{{ route('meat-cuts.index') }}">
                            <i class="fas fa-drumstick-bite me-1"></i> Meat Cuts
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->isSupplier())
                    <li class="nav-item">
                        <a class="nav-link" wire:navigate href="{{ route('supplier.purchases.index') }}">
                            <i class="fas fa-shopping-cart me-1"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" wire:navigate href="{{ route('supplier.deliveries.index') }}">
                            <i class="fas fa-truck me-1"></i> Deliveries
                        </a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" wire:navigate href="{{ route('orders.index') }}">
                            <i class="fas fa-shopping-cart me-1"></i> Orders
                        </a>
                    </li>
                    @endif
                    @if(!Auth::user()->isSupplier())
                    <li class="nav-item">
                        <a class="nav-link" wire:navigate href="{{ route('products.index') }}">
                            <i class="fas fa-box me-1"></i> Products
                        </a>
                    </li>
                    @endif
                    @if(!Auth::user()->isSupplier())
                    <li class="nav-item">
                        <a class="nav-link" wire:navigate href="{{ route('suppliers.index') }}">
                            <i class="fas fa-truck me-1"></i> Suppliers
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" wire:navigate href="{{ route('reports.index') }}">
                            <i class="fas fa-chart-line me-1"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-h me-1"></i> Others
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" wire:navigate href="{{ route('expenses.index') }}">
                                    <i class="fas fa-money-bill-wave me-1"></i> Expenses
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" wire:navigate href="{{ route('staff.index') }}">
                                    <i class="fas fa-users me-1"></i> Staff
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto">
                    <!-- Notification Navbar -->
                    <li class="nav-item">
                        @if(auth()->user()->isAdmin())
                            @livewire('admin-notification-navbar')
                        @elseif(auth()->user()->isStaff())
                            @livewire('staff-notification-navbar')
                        @endif
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" wire:navigate href="{{ route('profile.settings') }}">
                            <i class="fas fa-cog me-1"></i> Settings
                            </a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid flex-grow-1">
        <div class="row">
            <main class="col py-4">
                <div class="container-fluid">
                    @if(isset($showBackButton) && $showBackButton)
                        <div class="mb-3">
                            <x-back-button />
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <footer class="footer py-3">
        <div class="container text-center">
            <span class="text-muted">© {{ date('Y') }} Yannis Meatshop. All rights reserved.</span>
        </div>
    </footer>

    <!-- Local JS -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Vite JS (includes Chart.js) -->
    @vite(['resources/js/app.js'])

    @stack('page-scripts')
    @livewireScripts
</body>
</html>