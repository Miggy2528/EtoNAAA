@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Supplier Portal
                </h2>
                <div class="text-muted mt-1">Welcome back, {{ $user->name }}!</div>
                @if($supplier)
                    <div class="text-muted">{{ $supplier->shopname ?? $supplier->name }}</div>
                @endif
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('supplier.purchases.index') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" /><rect x="9" y="3" width="6" height="4" rx="2" /></svg>
                        View Orders from Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pending Orders</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['pending_purchases'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Awaiting approval</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Completed Orders</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['completed_purchases'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Successfully delivered</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Revenue</div>
                        </div>
                        <div class="h1 mb-3">₱{{ number_format($stats['total_revenue'], 2) }}</div>
                        <div class="d-flex mb-2">
                            <div>From approved orders</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="text-muted">Total Procurements</div>
                                    <div class="h2">{{ $stats['total_procurements'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="text-muted">On-Time Delivery</div>
                                    <div class="h2">{{ number_format($stats['on_time_percentage'], 1) }}%</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-muted mb-2">Delivery Rating</div>
                            <div class="d-flex align-items-center">
                                <div class="h1 mb-0 me-3">{{ number_format($stats['delivery_rating'], 2) }}</div>
                                <div class="text-muted">/ 5.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('supplier.purchases.index') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" /><rect x="9" y="3" width="6" height="4" rx="2" /></svg>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">View Orders from Admin</div>
                                        <div class="text-muted">Track and manage orders placed by admin</div>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('supplier.deliveries.index') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="3" width="18" height="18" rx="2" /><path d="M7 7h10v10h-10z" /></svg>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">Track Deliveries</div>
                                        <div class="text-muted">Monitor order deliveries and fulfillment</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Welcome to Supplier Portal</h3>
                    </div>
                    <div class="card-body">
                        <p>This is your supplier dashboard where you can view orders from the admin, track deliveries, and monitor your business performance.</p>
                        <p class="mb-0">Use the quick actions above to manage your orders and deliveries efficiently.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
