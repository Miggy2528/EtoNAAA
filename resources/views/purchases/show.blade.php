@extends('layouts.butcher')

@push('page-styles')
<style>
    .purchase-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        margin-bottom: 2rem;
    }
    
    .info-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .product-table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .product-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .product-table thead th {
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
    
    .product-table tbody tr {
        transition: background-color 0.2s;
    }
    
    .product-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .product-table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .product-img-wrapper {
        width: 70px;
        height: 70px;
        margin: 0 auto;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .status-badge-large {
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .total-row {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .action-btn {
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    
    .back-btn {
        background: white;
        border: 2px solid #dee2e6;
        color: #495057;
        padding: 0.5rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .back-btn:hover {
        background: #f8f9fa;
        border-color: #667eea;
        color: #667eea;
        transform: translateX(-5px);
    }
</style>
@endpush

@section('content')
<div class="page-body">
    <div class="container-xl">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('purchases.index') }}" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Purchases
            </a>
        </div>

        <!-- Header Card -->
        <div class="card purchase-header-card">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="mb-2" style="color: white; font-weight: 700;">
                            <i class="fas fa-file-invoice me-2"></i>Purchase Order Details
                        </h2>
                        <p class="mb-0" style="opacity: 0.9;">Purchase No: <strong>{{ $purchase->purchase_no }}</strong></p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="btn-group">
                            @php
                                $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                            @endphp
                            
                            @if ($purchase->status === \App\Enums\PurchaseStatus::PENDING)
                                <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="btn btn-success action-btn me-2"
                                            onclick="return confirm('Are you sure you want to approve this purchase?')">
                                        <i class="fas fa-check-circle me-1"></i>Approve
                                    </button>
                                </form>
                            @endif
                            
                            @if ($statusValue == 1 || $statusValue == 2)
                                <form action="{{ route('purchases.mark-complete', $purchase) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary action-btn me-2"
                                            onclick="return confirm('Mark this purchase as complete?')">
                                        <i class="fas fa-check-double me-1"></i>Mark Complete
                                    </button>
                                </form>
                            @endif
                            
                            @if ($statusValue == 3)
                                <form action="{{ route('purchases.mark-received', $purchase) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success action-btn me-2"
                                            onclick="return confirm('Mark this purchase as received?')">
                                        <i class="fas fa-box-open me-1"></i>Mark Received
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning action-btn me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            
                            <a href="{{ route('purchases.download-invoice', $purchase) }}" class="btn btn-info action-btn">
                                <i class="fas fa-download me-1"></i>Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-alert/>

        <!-- Purchase Information Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background: #e3f2fd;">
                            <i class="fas fa-calendar-alt" style="color: #2196f3; font-size: 1.25rem;"></i>
                        </div>
                        <div class="info-label">Order Date</div>
                        <div class="info-value">{{ $purchase->purchase_date ? $purchase->purchase_date->format('M d, Y') : '-' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background: #f3e5f5;">
                            <i class="fas fa-truck" style="color: #9c27b0; font-size: 1.25rem;"></i>
                        </div>
                        <div class="info-label">Supplier</div>
                        <div class="info-value">{{ $purchase->supplier->name }}</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background: #e8f5e9;">
                            <i class="fas fa-user" style="color: #4caf50; font-size: 1.25rem;"></i>
                        </div>
                        <div class="info-label">Created By</div>
                        <div class="info-value">{{ $purchase->createdBy->name ?? 'System' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card info-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background: #fff3e0;">
                            <i class="fas fa-info-circle" style="color: #ff9800; font-size: 1.25rem;"></i>
                        </div>
                        <div class="info-label">Status</div>
                        <div>
                            @php
                                $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                            @endphp
                            @if($statusValue == 0)
                                <span class="status-badge-large bg-warning text-dark">
                                    <i class="fas fa-clock"></i>Pending
                                </span>
                            @elseif($statusValue == 1)
                                <span class="status-badge-large bg-success">
                                    <i class="fas fa-check"></i>Approved
                                </span>
                            @elseif($statusValue == 2)
                                <span class="status-badge-large bg-info">
                                    <i class="fas fa-shipping-fast"></i>For Delivery
                                </span>
                            @elseif($statusValue == 3)
                                <span class="status-badge-large bg-primary">
                                    <i class="fas fa-check-double"></i>Complete
                                </span>
                            @elseif($statusValue == 4)
                                <span class="status-badge-large bg-success">
                                    <i class="fas fa-box-open"></i>Received
                                </span>
                            @else
                                <span class="status-badge-large bg-secondary">
                                    <i class="fas fa-question"></i>Unknown
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3 class="card-title mb-0" style="color: white;">
                    <i class="fas fa-boxes me-2"></i>Ordered Products
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table product-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th class="text-center" style="width: 100px;">Image</th>
                                <th>Product Name</th>
                                <th class="text-center" style="width: 120px;">Code</th>
                                <th class="text-center" style="width: 120px;">Current Stock</th>
                                <th class="text-center" style="width: 100px;">Qty Ordered</th>
                                <th class="text-end" style="width: 120px;">Unit Price</th>
                                <th class="text-end" style="width: 140px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($purchase->details as $item)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <div class="product-img-wrapper">
                                        <img src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}" 
                                             alt="{{ $item->product->name }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                    <small class="text-muted">{{ $item->product->category->name ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark" style="font-size: 0.875rem; padding: 0.35rem 0.75rem; font-weight: 600;">
                                        {{ $item->product->code }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary" style="font-size: 0.875rem; padding: 0.35rem 0.75rem;">
                                        {{ $item->product->quantity }} {{ $item->product->unit->name ?? 'pcs' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success" style="font-size: 0.875rem; padding: 0.35rem 0.75rem;">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold">
                                    ₱{{ number_format($item->unitcost, 2) }}
                                </td>
                                <td class="text-end fw-bold" style="color: #667eea;">
                                    ₱{{ number_format($item->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No products found in this purchase order</p>
                                </td>
                            </tr>
                        @endforelse
                        @if($purchase->details->count() > 0)
                            <tr class="total-row">
                                <td colspan="7" class="text-end" style="padding: 1.25rem;">
                                    <span style="font-size: 1.125rem;">TOTAL AMOUNT</span>
                                </td>
                                <td class="text-end" style="padding: 1.25rem;">
                                    <span style="font-size: 1.25rem; color: #667eea;">
                                        ₱{{ number_format($purchase->total_amount, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
