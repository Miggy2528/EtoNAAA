@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1 print-title">Purchase Order</h2>
                    <nav aria-label="breadcrumb" class="no-print">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Purchase Order</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to Supplier
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Purchase Order Form</h5>
                        <div>
                            <button type="button" class="btn btn-success" onclick="printPO()">
                                <i class="fas fa-print me-2"></i>Print PO
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- PO Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="mb-3">Supplier Information</h4>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Supplier:</strong></td>
                                    <td>{{ $supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Shop Name:</strong></td>
                                    <td>{{ $supplier->shopname }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $supplier->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $supplier->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $supplier->address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">Purchase Order Details</h4>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>PO Number:</strong></td>
                                    <td>PO-{{ date('Ymd') }}-{{ rand(1000, 9999) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>{{ date('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Prepared By:</strong></td>
                                    <td>{{ auth()->user()->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Page break for print layout -->
                    <div class="page-break-after no-print"></div>
                    <div class="page-break d-none d-print-block"></div>
                    
                    <!-- Products Table -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="mb-3 d-print-none">Products to Order</h4>
                            <h2 class="mb-3 d-none d-print-block print-title">Products to Order and Additional Notes</h2>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped print-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th width="15%">Unit</th>
                                            <th width="12%">Quantity</th>
                                            <th width="15%">Unit Price (₱)</th>
                                            <th width="15%">Total (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="po-products-table">
                                        @forelse($products as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td>{{ $product->unit->name ?? 'N/A' }}</td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control quantity-input" 
                                                       min="1" 
                                                       value="1" 
                                                       data-product-id="{{ $product->id }}"
                                                       data-unit-price="{{ $product->buying_price ?? 0 }}"
                                                       onchange="calculateTotal(this)">
                                            </td>
                                            <td class="text-nowrap">₱{{ number_format($product->buying_price ?? 0, 2) }}</td>
                                            <td class="total-cell text-nowrap">₱{{ number_format($product->buying_price ?? 0, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No products assigned to this supplier</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6" class="text-end">Subtotal:</th>
                                            <th id="subtotal">₱0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-end">Tax (12%):</th>
                                            <th id="tax">₱0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-end">Total Amount:</th>
                                            <th id="total-amount">₱0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="mb-3">Additional Notes</h4>
                            <textarea class="form-control" rows="3" placeholder="Enter any special instructions or notes for this purchase order..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-primary btn-lg" onclick="savePurchaseOrder()">
                                <i class="fas fa-save me-2"></i>Save Purchase Order
                            </button>
                            <button type="button" class="btn btn-success btn-lg ms-2" onclick="submitPurchaseOrder()">
                                <i class="fas fa-paper-plane me-2"></i>Submit PO to Supplier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style media="print">
    @page {
        size: A4 landscape;
        margin: 0;
    }
    
    html, body {
        height: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 10pt;
        line-height: 1.2;
    }
    
    /* Completely override the layout structure for print */
    .flex-grow-1, .d-flex, .container-fluid, .row, .col, main {
        display: block !important;
        height: auto !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        flex: none !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        page-break-inside: avoid !important;
        margin: 0 0 1cm 0 !important;
        padding: 0.5cm !important;
    }
    
    .table {
        font-size: 8pt !important;
        width: 100% !important;
        page-break-inside: avoid !important;
        table-layout: fixed !important;
        margin: 0 !important;
        border-collapse: collapse !important;
    }
    
    .table th, .table td {
        padding: 2px 3px !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        border: 1px solid #ccc !important;
    }
    
    .table-responsive {
        overflow: visible !important;
        width: 100% !important;
        margin: 0 !important;
    }
    
    h1, h2, h3, h4 {
        page-break-after: avoid !important;
        margin: 0 0 0.5cm 0 !important;
    }
    
    thead {
        display: table-header-group !important;
    }
    
    tfoot {
        display: table-footer-group !important;
    }
    
    tr {
        page-break-inside: avoid !important;
    }
    
    /* Specific column widths for better table layout */
    .table th:nth-child(1), .table td:nth-child(1) { width: 5%; }
    .table th:nth-child(2), .table td:nth-child(2) { width: 20%; }
    .table th:nth-child(3), .table td:nth-child(3) { width: 15%; }
    .table th:nth-child(4), .table td:nth-child(4) { width: 10%; }
    .table th:nth-child(5), .table td:nth-child(5) { width: 10%; }
    .table th:nth-child(6), .table td:nth-child(6) { width: 15%; }
    .table th:nth-child(7), .table td:nth-child(7) { width: 15%; }
    
    /* Ensure proper spacing for print */
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    
    .print-table {
        font-size: 8pt !important;
    }
    
    .print-title {
        font-size: 14pt !important;
        margin-bottom: 0.3cm !important;
    }
    
    footer {
        display: none !important;
    }
    
    /* Ensure main content takes full width */
    #content, main, .py-4 {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Page break for separate sections */
    .page-break {
        page-break-before: always;
    }
    
    .page-break-after {
        page-break-after: always;
    }
</style>

<script>
    // Calculate totals when page loads
    document.addEventListener('DOMContentLoaded', function() {
        calculateAllTotals();
    });
    
    // Calculate total for a specific row
    function calculateTotal(element) {
        const row = element.closest('tr');
        const unitPrice = parseFloat(element.dataset.unitPrice) || 0;
        const quantity = parseInt(element.value) || 0;
        const total = unitPrice * quantity;
        
        const totalCell = row.querySelector('.total-cell');
        totalCell.textContent = '₱' + total.toFixed(2);
        totalCell.classList.add('text-nowrap');
        
        calculateAllTotals();
    }
    
    // Calculate all totals (subtotal, tax, total)
    function calculateAllTotals() {
        let subtotal = 0;
        
        // Calculate subtotal
        document.querySelectorAll('#po-products-table tr').forEach(row => {
            const totalCell = row.querySelector('.total-cell');
            if (totalCell) {
                const totalText = totalCell.textContent.replace('₱', '').replace(',', '');
                const total = parseFloat(totalText) || 0;
                subtotal += total;
            }
        });
        
        // Calculate tax (12%)
        const tax = subtotal * 0.12;
        const totalAmount = subtotal + tax;
        
        // Update display
        document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
        document.getElementById('subtotal').classList.add('text-nowrap');
        document.getElementById('tax').textContent = '₱' + tax.toFixed(2);
        document.getElementById('tax').classList.add('text-nowrap');
        document.getElementById('total-amount').textContent = '₱' + totalAmount.toFixed(2);
        document.getElementById('total-amount').classList.add('text-nowrap');
    }
    
    // Print PO function
    function printPO() {
        // Ensure all calculations are up to date
        calculateAllTotals();
        
        // Temporarily adjust table for print
        const table = document.querySelector('.print-table');
        if (table) {
            table.style.fontSize = '8pt';
            table.style.width = '100%';
        }
        
        // Add a small delay to ensure styles are applied
        setTimeout(function() {
            window.print();
            // Revert changes after print if needed
            if (table) {
                table.style.fontSize = '';
                table.style.width = '';
            }
        }, 100);
    }
    
    // Save PO function
    function savePurchaseOrder() {
        // Collect product data
        const products = [];
        document.querySelectorAll('.quantity-input').forEach(input => {
            const productId = input.dataset.productId;
            const quantity = input.value;
            
            if (quantity > 0) {
                products.push({
                    id: productId,
                    quantity: quantity
                });
            }
        });
        
        // Get notes
        const notes = document.querySelector('textarea').value;
        
        // Prepare data
        const data = {
            _token: '{{ csrf_token() }}',
            products: products,
            notes: notes
        };
        
        // Send AJAX request
        fetch('{{ route('suppliers.purchase-order.store', $supplier) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Purchase Order saved successfully! PO Number: ' + data.purchase_no);
                // Optionally redirect to the purchase order details page
                // window.location.href = '/purchases/' + data.purchase_id;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the purchase order.');
        });
    }
    
    // Submit PO function
    function submitPurchaseOrder() {
        if (confirm('Are you sure you want to submit this purchase order to the supplier?')) {
            savePurchaseOrder(); // Save and submit
        }
    }
</script>
@endsection