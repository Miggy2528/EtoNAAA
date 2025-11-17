<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Customer Landing</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: #8B0000;   /* Deep red */
            --secondary-color: #4A0404; /* Darker red */
            --accent-color: #FF4136;    /* Accent */
            --text-muted: #6c757d;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #212529;
            background-color: #ffffff;
        }

        .navbar {
            background: rgba(75, 4, 4, 0.92);
            backdrop-filter: saturate(180%) blur(8px);
        }
        .navbar-brand { color: #fff !important; font-weight: 600; }
        .navbar-nav .nav-link { color: #fff !important; opacity: 0.9; }
        .navbar-nav .nav-link:hover { opacity: 1; }

        .hero {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            background: linear-gradient(rgba(75,4,4,0.85), rgba(75,4,4,0.75)), url("{{ asset('assets/img/backgrounds/bg-pattern-shapes.png') }}");
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        .hero .container { position: relative; z-index: 2; }
        .hero-title { font-weight: 800; letter-spacing: 0.2px; }
        .hero-subtitle { color: #f8d7da; }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 0.8rem 1.25rem;
            font-weight: 600;
        }
        .btn-primary:hover { background-color: var(--secondary-color); border-color: var(--secondary-color); }
        .btn-outline-light { border-radius: 10px; }

        .feature-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.08);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .feature-card:hover { transform: translateY(-6px); box-shadow: 0 1rem 2rem rgba(0,0,0,0.12); }
        .feature-icon {
            width: 56px; height: 56px; border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            background: #fff1f1; color: var(--primary-color);
        }

        .testimonial {
            border-left: 4px solid var(--primary-color);
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.06);
        }

        .cta-band {
            background: linear-gradient(135deg, var(--primary-color), #c00000);
            color: #fff;
            border-radius: 14px;
            box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.12);
        }

        .image-card {
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.12);
            background: #fff;
        }
        .hero-image {
            width: 100%;
            height: 320px;
            object-fit: cover;
            display: block;
        }
        .carousel-control-prev-icon, .carousel-control-next-icon { filter: invert(1); }

        footer { color: var(--text-muted); }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.login') }}">Sign In</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.register') }}">Create Account</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="display-4 hero-title mb-3">Premium Cuts, Seamless Ordering</h1>
                    <p class="lead hero-subtitle mb-4">A refined shopping experience crafted for our valued customers. Browse fresh inventory, track orders, and enjoy reliable delivery — all in one elegant portal.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('customer.login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-basket me-2"></i> Start Shopping
                        </a>
                        <a href="{{ route('customer.register') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i> Create Account
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 mt-4 mt-lg-0">
                    @php
                    $slides = [
                        asset('assets/img/hero/b1.jpg'),
                        asset('assets/img/hero/b2.jpg'),
                        asset('assets/img/hero/wmremove-transformed.jpeg'),
                    ];
                    @endphp
                    <div id="customerHeroCarousel" class="carousel slide image-card" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($slides as $idx => $src)
                                <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                    <img src="{{ $src }}" class="d-block w-100 hero-image" alt="Customer landing hero image {{ $idx + 1 }}">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#customerHeroCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#customerHeroCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Elegant. Efficient. Reliable.</h2>
                <p class="text-muted">Experience customer-first design and a smooth shopping journey.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="feature-icon mx-auto mb-3"><i class="fas fa-box"></i></div>
                        <h5>Curated Inventory</h5>
                        <p class="text-muted">Explore premium cuts and essentials, updated daily.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="feature-icon mx-auto mb-3"><i class="fas fa-lock"></i></div>
                        <h5>Secure Checkout</h5>
                        <p class="text-muted">Protected payments and private account management.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="feature-icon mx-auto mb-3"><i class="fas fa-truck"></i></div>
                        <h5>Reliable Delivery</h5>
                        <p class="text-muted">Timely deliveries with status updates at every step.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="feature-icon mx-auto mb-3"><i class="fas fa-user-cog"></i></div>
                        <h5>Personal Dashboard</h5>
                        <p class="text-muted">Manage orders, preferences, and profile effortlessly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- CTA Band -->
    <section class="py-5">
        <div class="container">
            <div class="cta-band p-5 text-center">
                <h3 class="fw-bold mb-2">Ready to experience effortless shopping?</h3>
                <p class="mb-4">Sign in or create a new account to get started.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('customer.login') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </a>
                    <a href="{{ route('customer.register') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4">
        <div class="container text-center">
            <small>© {{ date('Y') }} Yannis Meatshop. All rights reserved.</small>
        </div>
    </footer>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
