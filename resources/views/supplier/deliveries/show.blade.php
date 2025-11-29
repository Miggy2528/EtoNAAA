@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Delivery Details
                </h2>
                <div class="text-muted mt-1">Procurement #{{ $procurement->id }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('supplier.deliveries.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                        Back to Deliveries
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
            <!-- Delivery Information -->
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Product</label>
                                <div class="fw-bold">{{ $procurement->product->name ?? 'N/A' }}</div>
                                <div class="text-muted small">Code: {{ $procurement->product->code ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Status</label>
                                <div>
                                    @if($procurement->status == 'on-time')
                                        <span class="badge bg-success">On-Time</span>
                                    @elseif($procurement->status == 'delayed')
                                        <span class="badge bg-danger">Delayed</span>
                                    @elseif($procurement->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($procurement->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Quantity Supplied</label>
                                <div class="fw-bold">{{ $procurement->quantity_supplied }} {{ $procurement->product->unit->short_code ?? 'units' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Total Cost</label>
                                <div class="fw-bold text-primary">₱{{ number_format($procurement->total_cost, 2) }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Expected Delivery Date</label>
                                <div class="fw-bold">{{ $procurement->expected_delivery_date ? $procurement->expected_delivery_date->format('M d, Y') : 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Actual Delivery Date</label>
                                <div class="fw-bold">{{ $procurement->delivery_date ? $procurement->delivery_date->format('M d, Y') : 'Pending' }}</div>
                            </div>
                        </div>
                        @if($procurement->defective_rate > 0)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Defective Rate</label>
                                <div class="fw-bold text-danger">{{ number_format($procurement->defective_rate, 2) }}%</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Product Details Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Product Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Product Name</label>
                                    <div class="fw-bold">{{ $procurement->product->name ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Category</label>
                                    <div>{{ $procurement->product->category->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Product Code</label>
                                    <div>{{ $procurement->product->code ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Unit</label>
                                    <div>{{ $procurement->product->unit->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions & Communication -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Quantity</label>
                            <div class="h3">{{ $procurement->quantity_supplied }} {{ $procurement->product->unit->short_code ?? 'units' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Value</label>
                            <div class="h2 text-primary">₱{{ number_format($procurement->total_cost, 2) }}</div>
                        </div>
                        <div>
                            <label class="form-label text-muted">Delivery Status</label>
                            <div>
                                @if($procurement->status == 'on-time')
                                    <span class="badge bg-success fs-4">On-Time</span>
                                @elseif($procurement->status == 'delayed')
                                    <span class="badge bg-danger fs-4">Delayed</span>
                                @else
                                    <span class="badge bg-warning fs-4">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Delivery Status -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Delivery</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('supplier.deliveries.update-status', $procurement->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Delivery Note</label>
                                <textarea name="delivery_notes" class="form-control @error('delivery_notes') is-invalid @enderror" rows="4" placeholder="Add delivery confirmation or notes..."></textarea>
                                @error('delivery_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Update the admin team about this delivery.</small>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Confirm Delivery
                            </button>
                        </form>
                    </div>
                </div>

                @if($procurement->notes)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Notes</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-muted" style="white-space: pre-line;">{{ $procurement->notes }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
