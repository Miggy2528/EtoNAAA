@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-receipt me-2"></i>Order Details from Admin
                </h2>
                <div class="text-muted mt-1">Order #{{ $purchase->purchase_no }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('supplier.purchases.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                        Back to Orders
                    </a>
                    <a href="{{ route('supplier.purchases.edit-invoice', $purchase->id) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                        Edit Invoice
                    </a>
                    <a href="{{ route('supplier.purchases.download-invoice', $purchase->id) }}" class="btn btn-success" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 17v-8a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v8" /><path d="M7 13l3 3l3 -3" /><path d="M12 12l0 -9" /></svg>
                        Download Invoice
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

        <div class="row row-deck row-cards">
            <!-- Purchase Order Information -->
            <div class="col-lg-8">
                <div class="card mb-3 order-summary-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Order Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Order Number</label>
                                <div class="fw-bold">
                                    <span class="avatar bg-primary-lt text-primary me-2">#{{ substr($purchase->purchase_no, -4) }}</span>
                                    {{ $purchase->purchase_no }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Date</label>
                                <div class="fw-bold">
                                    {{ $purchase->date ? $purchase->date->format('M d, Y') : $purchase->created_at->format('M d, Y') }}
                                    <div class="text-muted small">{{ $purchase->date ? $purchase->date->format('h:i A') : $purchase->created_at->format('h:i A') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Status</label>
                                <div>
                                    @php
                                        $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                                    @endphp
                                    @if($statusValue == 0)
                                        <span class="badge bg-warning status-badge">Pending</span>
                                    @elseif($statusValue == 1)
                                        <span class="badge bg-success status-badge">Approved</span>
                                    @elseif($statusValue == 2)
                                        <span class="badge bg-info status-badge">For Delivery</span>
                                    @elseif($statusValue == 3)
                                        <span class="badge bg-primary status-badge">Complete</span>
                                    @elseif($statusValue == 4)
                                        <span class="badge bg-success status-badge">Received</span>
                                    @else
                                        <span class="badge bg-secondary status-badge">{{ ucfirst((string) $purchase->status) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Created By</label>
                                <div class="fw-bold">{{ $purchase->createdBy->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card order-summary-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes me-2"></i>Order Items
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table supplier-order-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Cost</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->details as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar bg-blue-lt text-blue me-2">{{ substr($detail->product->name ?? 'N/A', 0, 1) }}</span>
                                            <div>
                                                <div class="fw-bold">{{ $detail->product->name ?? 'N/A' }}</div>
                                                <div class="text-muted small">{{ $detail->product->code ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₱{{ number_format($detail->unitcost, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-blue-lt">{{ $detail->quantity }} {{ $detail->product->unit->short_code ?? 'pcs' }}</span>
                                    </td>
                                    <td class="text-end fw-bold">₱{{ number_format($detail->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Amount:</td>
                                    <td class="text-end fw-bold text-primary fs-3">₱{{ number_format($purchase->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Communication & Notes -->
            <div class="col-lg-4">
                <!-- Delivery Status Actions -->
                @php
                    $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                @endphp
                @if($statusValue != 4)
                <div class="card mb-3 order-summary-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-truck-loading me-2"></i>Update Delivery Status
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-muted mb-3">Update the order status to notify the admin about delivery progress.</div>
                        
                        <!-- Status Progress Indicator -->
                        <div class="mb-4">
                            <div class="progress" style="height: 8px;">
                                @php
                                    $progressWidth = 0;
                                    if ($statusValue == 0) $progressWidth = 20;
                                    elseif ($statusValue == 1) $progressWidth = 40;
                                    elseif ($statusValue == 2) $progressWidth = 60;
                                    elseif ($statusValue == 3) $progressWidth = 80;
                                    elseif ($statusValue == 4) $progressWidth = 100;
                                @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ $progressWidth }}%" aria-valuenow="{{ $progressWidth }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Pending</small>
                                <small class="text-muted">Approved</small>
                                <small class="text-muted">Delivery</small>
                                <small class="text-muted">Complete</small>
                                <small class="text-muted">Received</small>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <!-- Show "Mark as Approved" if current status is Pending -->
                            @if($statusValue == 0)
                            <form action="{{ route('supplier.purchases.update-delivery-status', $purchase->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="status" value="1">
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this order as Approved?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    Mark as Approved
                                </button>
                            </form>
                            @endif
                            
                            <!-- Show "Mark as For Delivery" if current status is Approved or Pending -->
                            @if($statusValue == 0 || $statusValue == 1)
                            <form action="{{ route('supplier.purchases.update-delivery-status', $purchase->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="status" value="2">
                                <button type="submit" class="btn btn-info w-100" onclick="return confirm('Mark this order as For Delivery?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                                    Mark as For Delivery
                                </button>
                            </form>
                            @endif
                            
                            <!-- Show "Mark as Complete" if current status is Approved or For Delivery -->
                            @if($statusValue == 1 || $statusValue == 2)
                            <form action="{{ route('supplier.purchases.update-delivery-status', $purchase->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="status" value="3">
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Mark this order as Complete?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    Mark as Complete
                                </button>
                            </form>
                            @endif
                            
                            <!-- Show current status info -->
                            <div class="alert alert-info mt-3 mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>Current Status:</strong> 
                                        @if($statusValue == 0)
                                            Pending
                                        @elseif($statusValue == 1)
                                            Approved
                                        @elseif($statusValue == 2)
                                            For Delivery
                                        @elseif($statusValue == 3)
                                            Complete
                                        @elseif($statusValue == 4)
                                            Received
                                        @else
                                            {{ ucfirst((string) $purchase->status) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card mb-3 order-summary-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar me-2"></i>Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Items</label>
                            <div class="h3">{{ $purchase->details->count() }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Quantity</label>
                            <div class="h3">{{ $purchase->details->sum('quantity') }}</div>
                        </div>
                        <div>
                            <label class="form-label text-muted">Grand Total</label>
                            <div class="h2 text-primary">₱{{ number_format($purchase->total_amount, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Add Communication Notes -->
                <div class="card order-summary-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comment-medical me-2"></i>Add Communication Note
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('supplier.purchases.update-notes', $purchase->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Your Note</label>
                                <textarea name="supplier_notes" class="form-control @error('supplier_notes') is-invalid @enderror" rows="4" placeholder="Add a note or message regarding this order..."></textarea>
                                @error('supplier_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">This note will be visible to the admin team.</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                Add Note
                            </button>
                        </form>
                    </div>
                </div>

                @if($purchase->notes)
                <div class="card mt-3 order-summary-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history me-2"></i>Communication History
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-muted" style="white-space: pre-line;">{{ $purchase->notes }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection