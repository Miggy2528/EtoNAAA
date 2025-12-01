@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-edit me-2"></i>Edit Invoice
                </h2>
                <div class="text-muted mt-1">Order #{{ $purchase->purchase_no }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('supplier.purchases.show', $purchase->id) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                        Back to Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <form id="invoiceEditForm" action="{{ route('supplier.purchases.preview-invoice', $purchase->id) }}" method="POST">
            @csrf
            <div class="row row-deck row-cards">
                <!-- Invoice Editing Area -->
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-invoice me-2"></i>Invoice Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control" name="edited_data[invoice_number]" value="{{ $purchase->purchase_no }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Invoice Date</label>
                                    <input type="date" class="form-control" name="edited_data[invoice_date]" value="{{ $purchase->created_at->format('Y-m-d') }}">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Supplier Name</label>
                                    <input type="text" class="form-control" name="edited_data[supplier_name]" value="{{ $purchase->supplier->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Supplier Shop</label>
                                    <input type="text" class="form-control" name="edited_data[supplier_shop]" value="{{ $purchase->supplier->shopname }}">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Supplier Email</label>
                                    <input type="email" class="form-control" name="edited_data[supplier_email]" value="{{ $purchase->supplier->email }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Supplier Phone</label>
                                    <input type="text" class="form-control" name="edited_data[supplier_phone]" value="{{ $purchase->supplier->phone }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Supplier Address</label>
                                <textarea class="form-control" name="edited_data[supplier_address]" rows="3">{{ $purchase->supplier->address }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-boxes me-2"></i>Order Items
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->details as $index => $detail)
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
                                        <td>
                                            <input type="number" class="form-control" name="edited_data[items][{{ $index }}][unit_price]" 
                                                   value="{{ $detail->unitcost }}" step="0.01" min="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="form-control" name="edited_data[items][{{ $index }}][quantity]" 
                                                   value="{{ $detail->quantity }}" min="0">
                                        </td>
                                        <td class="text-end fw-bold">
                                            ₱<span class="item-total" data-index="{{ $index }}">{{ number_format($detail->total, 2) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total Amount:</td>
                                        <td class="text-end fw-bold text-primary fs-3">₱<span id="grandTotal">{{ number_format($purchase->total_amount, 2) }}</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Preview & Actions -->
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" name="action" value="preview">
                                    <i class="fas fa-eye me-2"></i>Preview Invoice
                                </button>
                                <button type="button" class="btn btn-success" onclick="saveChanges()">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                                <a href="{{ route('supplier.purchases.download-invoice', $purchase->id) }}" class="btn btn-info">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle me-2"></i>Instructions
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-edit text-primary me-2"></i> Edit invoice details as needed
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-eye text-primary me-2"></i> Preview changes before finalizing
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-save text-primary me-2"></i> Save changes to update the invoice
                                </li>
                                <li>
                                    <i class="fas fa-download text-primary me-2"></i> Download the final PDF invoice
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Calculate item totals and grand total when inputs change
    document.addEventListener('input', function(e) {
        if (e.target.name.includes('[unit_price]') || e.target.name.includes('[quantity]')) {
            updateTotals();
        }
    });
    
    function updateTotals() {
        let grandTotal = 0;
        
        // Update each item total
        document.querySelectorAll('tbody tr').forEach(row => {
            const unitPriceInput = row.querySelector('input[name*="[unit_price]"]');
            const quantityInput = row.querySelector('input[name*="[quantity]"]');
            const totalSpan = row.querySelector('.item-total');
            
            if (unitPriceInput && quantityInput && totalSpan) {
                const unitPrice = parseFloat(unitPriceInput.value) || 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const total = unitPrice * quantity;
                
                totalSpan.textContent = total.toFixed(2);
                grandTotal += total;
            }
        });
        
        // Update grand total
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
    }
    
    function saveChanges() {
        // In a real implementation, you would send the form data to the server to save changes
        alert('In a complete implementation, this would save your changes to the database.');
        // For now, we'll just submit the form to preview
        document.getElementById('invoiceEditForm').submit();
    }
    
    // Initialize totals on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateTotals();
    });
</script>
@endsection