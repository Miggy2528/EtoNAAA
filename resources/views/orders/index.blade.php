@extends('layouts.butcher')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                {{ __('Order Management') }}
                            </h3>
                            <div class="card-actions">
                                <a href="{{ route('orders.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>
                                    Create Order
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($orders->isEmpty())
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="fas fa-box-open" style="font-size: 3rem; color: #6c757d;"></i>
                                </div>
                                <p class="empty-title h4">No orders found</p>
                                <p class="empty-subtitle text-muted">
                                    Try adjusting your search or filter to find what you're looking for.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>
                                        Create your first order
                                    </a>
                                </div>
                            </div>
                        @else
                            <x-alert/>
                            <livewire:tables.order-table />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection