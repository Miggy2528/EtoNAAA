@extends('layouts.butcher')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <x-alert/>

        <div class="row row-cards">
            <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-lg">
                            <div class="card-header bg-primary-lt">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="card-title mb-1">
                                            <i class="fas fa-shopping-cart me-2 text-primary"></i>
                                            {{ __('Create New Purchase Order') }}
                                        </h3>
                                        <p class="text-muted mb-0">Create a new purchase order for your suppliers</p>
                                    </div>
                                    <div class="card-actions btn-actions">
                                        <a href="{{ url()->previous() ?: route('purchases.index') }}" class="btn btn-ghost-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Back to Purchases
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="row g-4">
                                    <!-- Purchase Information Section -->
                                    <div class="col-12">
                                        <div class="card border-primary border-2">
                                            <div class="card-header bg-primary-lt">
                                                <h4 class="card-title mb-0">
                                                    <i class="fas fa-info-circle me-2 text-primary"></i>Purchase Information
                                                </h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gx-4">
                                                    <div class="col-md-6 col-12">
                                                        <div class="mb-3">
                                                            <label for="date" class="form-label required fw-bold">
                                                                {{ __('Purchase Date') }}
                                                            </label>
                                                            <div class="input-group input-group-lg">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-calendar"></i>
                                                                </span>
                                                                <input name="date" id="date" type="date"
                                                                       class="form-control form-control-lg @error('date') is-invalid @enderror"
                                                                       value="{{ old('date') ?? now()->format('Y-m-d') }}"
                                                                       required>
                                                            </div>
                                                            @error('date')
                                                            <div class="invalid-feedback d-block">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6 col-12">
                                                        <x-tom-select
                                                            label="Select Supplier"
                                                            id="supplier_id"
                                                            name="supplier_id"
                                                            placeholder="Choose a supplier..."
                                                            :data="$suppliers"
                                                            class="form-control-lg"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Products Section -->
                                    <div class="col-12">
                                        <div class="card border-success border-2">
                                            <div class="card-header bg-success-lt">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">
                                                        <i class="fas fa-boxes me-2 text-success"></i>Products
                                                    </h4>
                                                    <span class="badge bg-success-lt text-success fs-6" id="productCount">0 items</span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @livewire('purchase-form')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-light">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span>Please ensure all products are correctly selected before submitting</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ url()->previous() ?: route('purchases.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-lg" id="submitPurchaseBtn">
                                            <i class="fas fa-check-circle me-1"></i> Create Purchase Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page-styles')
<style>
    /* Enhanced TomSelect dropdown styling */
    .ts-dropdown {
        z-index: 1055 !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #dee2e6 !important;
    }
    
    .ts-wrapper.single .ts-control {
        border-color: #ced4da !important;
        border-radius: 0.375rem !important;
    }
    
    .ts-wrapper.single .ts-control:focus {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    
    .ts-wrapper.single .ts-control, 
    .ts-wrapper.single .ts-control input {
        cursor: pointer;
        min-height: calc(1.5em + 1rem + 2px) !important;
    }
    
    .ts-dropdown .optgroup-header {
        font-weight: bold;
        background: #f8f9fa;
        padding: 5px 10px;
    }
    
    /* Fix for dropdown positioning */
    .ts-dropdown.dropdown-input {
        position: absolute !important;
    }
    
    /* Card enhancements */
    .card.border-primary {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    .card.border-success {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    /* Form control enhancements */
    .form-control-lg {
        padding: 0.75rem 1rem !important;
    }
    
    .input-group-text {
        background-color: #f8f9fa !important;
        border-color: #ced4da !important;
    }
    
    /* Button enhancements */
    .btn-ghost-secondary {
        background-color: transparent !important;
        border-color: transparent !important;
        color: #6c757d !important;
    }
    
    .btn-ghost-secondary:hover {
        background-color: #e9ecef !important;
        border-color: #e9ecef !important;
        color: #495057 !important;
    }
</style>
@endpush

@push('page-scripts')
<script>
// Add loading state to submit button and form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('purchaseForm');
    const submitBtn = document.getElementById('submitPurchaseBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating Purchase...';
            submitBtn.disabled = true;
        });
    }
    
    // Update product count when Livewire updates
    window.addEventListener('livewire:load', function() {
        updateProductCount();
    });
    
    function updateProductCount() {
        // This would be updated by Livewire events
        // For now, we'll leave it as a placeholder
    }
});
</script>
@endpush
@endsection