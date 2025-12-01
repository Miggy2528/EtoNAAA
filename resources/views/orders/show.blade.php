@extends('layouts.butcher')

@push('page-styles')
<style>
    .cursor-pointer:hover {
        opacity: 0.8;
        transform: scale(1.02);
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .status-badge {
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: inline-block;
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
    
    .section-card {
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .section-header {
        padding: 1rem 1.25rem;
        margin-bottom: 0;
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        border-top-left-radius: 9px;
        border-top-right-radius: 9px;
        font-weight: 600;
    }
    
    .section-body {
        padding: 1.25rem;
    }
    
    .info-box {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease-in-out;
    }
    
    .info-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .info-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 0.95rem;
        color: #212529;
        word-break: break-word;
    }
    
    .product-image {
        border-radius: 5px;
        transition: all 0.2s ease-in-out;
    }
    
    .product-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .section-header {
            font-size: 1.1rem;
        }
        
        .info-label {
            font-size: 0.7rem;
        }
        
        .info-value {
            font-size: 0.85rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .table th, .table td {
            padding: 0.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .section-body {
            padding: 1rem;
        }
        
        .info-box {
            padding: 0.5rem;
        }
        
        .d-flex.flex-wrap.align-items-center.gap-3 {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-footer .d-flex {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .card-footer .d-flex .text-muted {
            text-align: center;
        }
    }
    
    /* Custom styles for order details */
    .order-header {
        background: linear-gradient(135deg, #8B0000 0%, #4A0404 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .order-summary-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .action-buttons {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="page-body">
    <div class="container-xl">
        {{-- Order Header --}}
        <div class="order-header">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-column flex-md-row">
                <div>
                    <h1 class="mb-2 mb-md-0">
                        <i class="fas fa-receipt me-2"></i>
                        {{ __('Order Details') }}
                    </h1>
                    <p class="mb-0 opacity-75">Detailed information for order #{{ $order->invoice_no }}</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <x-back-button url="{{ route('orders.index') }}" text="Back to Orders" />
                    <!-- Edit Invoice button -->
                    <a href="{{ route('orders.edit-invoice', $order->id) }}" class="btn btn-icon btn-primary">
                        <i class="fas fa-edit text-white"></i>
                    </a>
                    <!-- Remove button -->
                    <form action="{{ route('orders.destroy', $order) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon btn-light" onclick="return confirm('Are you sure you want to remove this order?')" title="Remove Order">
                            <i class="fas fa-trash text-danger"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-hashtag me-2"></i>
                    <span>Invoice: <strong>{{ $order->invoice_no }}</strong></span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user me-2"></i>
                    <span>Customer: <strong>{{ $order->customer->name }}</strong></span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar me-2"></i>
                    <span>Date: <strong>{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</strong></span>
                </div>
                <div>
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
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $order->order_status->label() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card order-summary-card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    {{ __('Order Summary') }}
                </h3>
                <x-action.close route="{{ route('orders.index') }}"/>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Basic Order Info --}}
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="section-card h-100">
                            <h5 class="section-header bg-light">
                                <i class="fas fa-info-circle me-2"></i>
                                Basic Information
                            </h5>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="info-box">
                                            <div class="info-label">{{ __('Order Date') }}</div>
                                            <div class="info-value">
                                                <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                                {{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="info-box">
                                            <div class="info-label">{{ __('Payment Type') }}</div>
                                            <div class="info-value">
                                                <i class="fas fa-money-bill-wave me-1 text-success"></i>
                                                {{ ucfirst($order->payment_type) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="info-box">
                                            <div class="info-label">{{ __('Customer Account') }}</div>
                                            <div class="info-value">
                                                <i class="fas fa-user me-1 text-info"></i>
                                                {{ $order->customer->name }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($order->receiver_name)
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="info-box">
                                            <div class="info-label">{{ __('Receiver Name') }}</div>
                                            <div class="info-value">
                                                <i class="fas fa-user-check me-1 text-warning"></i>
                                                {{ $order->receiver_name }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="info-box">
                                            <div class="info-label">{{ __('Customer Email') }}</div>
                                            <div class="info-value">
                                                <i class="fas fa-envelope me-1 text-danger"></i>
                                                {{ $order->customer_email }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Conditional Payment Info --}}
                    @if($order->payment_type === 'gcash')
                    <div class="col-lg-6">
                        <div class="section-card h-100">
                            <h5 class="section-header bg-light">
                                <i class="fas fa-credit-card me-2"></i>
                                Payment Information
                            </h5>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-box">
                                            <div class="info-label">{{ __('GCash Reference') }}</div>
                                            <div class="info-value">
                                                <i class="fas fa-barcode me-1 text-primary"></i>
                                                {{ $order->gcash_reference ?: 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-box">
                                            <div class="info-label">{{ __('Proof of Payment') }}</div>
                                            <div class="border p-2 rounded bg-light text-center mt-2">
                                                @if ($order->proof_of_payment)
                                                    <img src="{{ asset('storage/' . $order->proof_of_payment) }}" 
                                                         alt="Proof of Payment" 
                                                         class="img-fluid cursor-pointer product-image" 
                                                         style="max-height: 200px; cursor: pointer;" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#proofOfPaymentModal"
                                                         title="Click to view full image">
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-search-plus me-1"></i>
                                                            Click image to enlarge
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="py-3">
                                                        <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted mb-0">{{ __('No image uploaded') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                {{-- Delivery Info --}}
                <div class="section-card">
                    <h5 class="section-header bg-light">
                        <i class="fas fa-truck me-2"></i>
                        {{ __('Delivery Information') }}
                    </h5>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('City') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-city me-1 text-primary"></i>
                                        {{ $order->city }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Postal Code') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-mail-bulk me-1 text-info"></i>
                                        {{ $order->postal_code }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Barangay') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-map-marker-alt me-1 text-success"></i>
                                        {{ $order->barangay }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Street Name') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-road me-1 text-warning"></i>
                                        {{ $order->street_name }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Building') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-building me-1 text-danger"></i>
                                        {{ $order->building ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('House No.') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-home me-1 text-info"></i>
                                        {{ $order->house_no ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Full Delivery Address') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-map-marked-alt me-1 text-primary"></i>
                                        {{ $order->delivery_address }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Contact Number') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-phone me-1 text-success"></i>
                                        {{ $order->contact_phone }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Delivery Notes') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-sticky-note me-1 text-warning"></i>
                                        {{ $order->delivery_notes ?: 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shop Information --}}
                <div class="section-card">
                    <h5 class="section-header bg-light">
                        <i class="fas fa-store me-2"></i>
                        {{ __('From Yannis Meat Shop') }}
                    </h5>
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Address') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                        Katapatn Rd, 17, Cabuyao City, 4025 Laguna
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 col-sm-12 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Phone') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-phone-alt me-1 text-success"></i>
                                        +63 09082413347
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 col-sm-12 mb-3">
                                <div class="info-box">
                                    <div class="info-label">{{ __('Email') }}</div>
                                    <div class="info-value">
                                        <i class="fas fa-envelope me-1 text-primary"></i>
                                        email@example.com
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="section-card">
                    <h5 class="section-header bg-light">
                        <i class="fas fa-boxes me-2"></i>
                        {{ __('Order Items') }}
                    </h5>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Photo</th>
                                        <th>Product Name</th>
                                        <th>Code</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->details as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">
                                            <img src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}" class="img-thumbnail product-image" style="max-height: 80px;">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-drumstick-bite me-2 text-primary"></i>
                                                {{ $item->product->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->product->code }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $item->quantity }} {{ $item->product->unit->name ?? 'kg' }}</span>
                                        </td>
                                        <td class="text-end">₱{{ number_format($item->unitcost, 2) }}/{{ $item->product->unit->name ?? 'kg' }}</td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Subtotal</td>
                                        <td class="text-end fw-bold">₱{{ number_format($order->total - ($order->tax_amount ?? 0), 2) }}</td>
                                    </tr>
                                    @if($order->tax_amount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Tax</td>
                                        <td class="text-end fw-bold">₱{{ number_format($order->tax_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Paid Amount</td>
                                        <td class="text-end fw-bold">₱{{ number_format($order->pay, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Due</td>
                                        <td class="text-end fw-bold">₱{{ number_format($order->due, 2) }}</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="6" class="text-end fw-bold fs-5">Total</td>
                                        <td class="text-end fw-bold fs-5 text-success">₱{{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action --}}
            @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
            <div class="card-footer bg-light">
                <div class="action-buttons">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
                        <div class="text-muted mb-2 mb-md-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Update order status using the buttons below
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-danger btn-sm" onclick="updateStatus({{ $order->id }}, 'Cancelled')">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="updateStatus({{ $order->id }}, 'For Delivery')">
                                <i class="fas fa-truck me-1"></i>For Delivery
                            </button>
                            <button class="btn btn-success btn-sm" onclick="updateStatus({{ $order->id }}, 'Completed')">
                                <i class="fas fa-check me-1"></i>Complete Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if ($order->order_status === \App\Enums\OrderStatus::FOR_DELIVERY)
            <div class="card-footer bg-light">
                <div class="action-buttons">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
                        <div class="text-muted mb-2 mb-md-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Order is out for delivery. Complete when finished.
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-danger btn-sm" onclick="updateStatus({{ $order->id }}, 'Cancelled')">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button class="btn btn-success btn-sm" onclick="updateStatus({{ $order->id }}, 'Completed')">
                                <i class="fas fa-check me-1"></i>Complete Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Cancel Order Modal --}}
@if ($order->order_status === \App\Enums\OrderStatus::PENDING)
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="cancelOrderModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cancel Order #{{ $order->invoice_no }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.cancel', $order) }}" method="POST" id="cancelOrderForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fs-1 me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Important Warning</h6>
                                <p class="mb-0">Are you sure you want to cancel this order? This action cannot be undone and will:</p>
                                <ul class="mb-0 mt-2">
                                    <li>Restore product quantities to inventory</li>
                                    <li>Notify the customer about the cancellation</li>
                                    <li>Update the order status permanently</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 col-sm-12">
                            <strong>Customer Account:</strong> {{ $order->customer->name }}
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <strong>Order Date:</strong> {{ $order->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 col-sm-12">
                            <strong>Payment Type:</strong> {{ ucfirst($order->payment_type) }}
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <strong>Total Amount:</strong> ₱{{ number_format($order->total, 2) }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label fw-bold">
                            Cancellation Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="cancellation_reason" 
                                  name="cancellation_reason" 
                                  rows="4" 
                                  required 
                                  placeholder="Please provide a detailed reason for cancelling this order. This will be sent to the customer..."></textarea>
                        <div class="form-text">This reason will be visible to the customer in their notification.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmCancel">
                        <i class="fas fa-ban me-1"></i>Cancel Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
// Prevent modal conflicts and improve UX
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cancelOrderModal');
    if (modal) {
        // Clear form when modal is hidden
        modal.addEventListener('hidden.bs.modal', function() {
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                const textarea = form.querySelector('textarea[name="cancellation_reason"]');
                if (textarea) {
                    textarea.classList.remove('is-invalid');
                }
            }
        });
        
        // Add confirmation before form submission
        const confirmButton = document.getElementById('confirmCancel');
        if (confirmButton) {
            confirmButton.addEventListener('click', function(e) {
                const textarea = document.getElementById('cancellation_reason');
                if (!textarea.value.trim()) {
                    e.preventDefault();
                    textarea.focus();
                    textarea.classList.add('is-invalid');
                    return false;
                }
                textarea.classList.remove('is-invalid');
            });
        }
    }
});
</script>
@endpush
@endif

{{-- Proof of Payment Modal --}}
@if ($order->proof_of_payment && $order->payment_type === 'gcash')
<div class="modal fade" id="proofOfPaymentModal" tabindex="-1" aria-labelledby="proofOfPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="proofOfPaymentModalLabel">
                    <i class="fas fa-receipt me-2"></i>Proof of Payment - Order #{{ $order->invoice_no }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4" style="background-color: #f8f9fa;">
                <img src="{{ asset('storage/' . $order->proof_of_payment) }}" 
                     alt="Proof of Payment" 
                     class="img-fluid rounded shadow" 
                     style="max-width: 100%; max-height: 80vh; object-fit: contain;">
                <div class="mt-3">
                    <p class="text-muted mb-1"><strong>GCash Reference:</strong> {{ $order->gcash_reference ?? 'N/A' }}</p>
                    <p class="text-muted mb-0"><strong>Order Date:</strong> {{ $order->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}</p>
                </div>
            </div>
            <div class="modal-footer flex-column flex-md-row">
                <a href="{{ asset('storage/' . $order->proof_of_payment) }}" 
                   target="_blank" 
                   class="btn btn-primary mb-2 mb-md-0">
                    <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    function updateStatus(orderId, status) {
        if (!confirm(`Are you sure you want to mark this order as ${status}?`)) {
            return;
        }
        
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order status.');
        });
    }
</script>
@endsection