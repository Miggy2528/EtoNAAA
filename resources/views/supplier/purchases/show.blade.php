@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Order Details from Admin
                </h2>
                <div class="text-muted mt-1">Order #{{ $purchase->purchase_no }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('supplier.purchases.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                        Back to Orders
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
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Order Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Order Number</label>
                                <div class="fw-bold">{{ $purchase->purchase_no }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Date</label>
                                <div class="fw-bold">{{ $purchase->date ? $purchase->date->format('M d, Y') : $purchase->created_at->format('M d, Y') }}</div>
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
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($statusValue == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($statusValue == 2)
                                        <span class="badge bg-info">For Delivery</span>
                                    @elseif($statusValue == 3)
                                        <span class="badge bg-primary">Complete</span>
                                    @elseif($statusValue == 4)
                                        <span class="badge bg-success">Received</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($purchase->status) }}</span>
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
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order Items</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
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
                                        <div class="fw-bold">{{ $detail->product->name ?? 'N/A' }}</div>
                                        <div class="text-muted small">{{ $detail->product->code ?? '' }}</div>
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
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Update Delivery Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-muted mb-3">Update the order status to notify the admin about delivery progress.</div>
                        <div class="d-grid gap-2">
                            @if($statusValue != 0)
                            <form action="{{ route('supplier.purchases.update-delivery-status', $purchase->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="status" value="0">
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Mark this order as Pending?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                                    Mark as Pending
                                </button>
                            </form>
                            @endif
                            
                            @if($statusValue != 2)
                            <form action="{{ route('supplier.purchases.update-delivery-status', $purchase->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="status" value="2">
                                <button type="submit" class="btn btn-info w-100" onclick="return confirm('Mark this order as For Delivery?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                                    Mark as For Delivery
                                </button>
                            </form>
                            @endif
                            
                            @if($statusValue != 3)
                            <form action="{{ route('supplier.purchases.update-delivery-status', $purchase->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="status" value="3">
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Mark this order as Complete?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    Mark as Complete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Summary</h3>
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
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Communication Note</h3>
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
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Communication History</h3>
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
