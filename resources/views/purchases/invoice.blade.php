<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            Purchase Order #{{ $purchase->purchase_no }} - Yannis Meat Shop
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <!-- External CSS libraries -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/fonts/font-awesome/css/font-awesome.min.css') }}">
        <!-- Google fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Custom Stylesheet -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
        <style>
            :root {
                --primary-color: #8B0000;
                --secondary-color: #4A0404;
                --accent-color: #FF4136;
                --light-bg: #f8f9fa;
                --dark-text: #212529;
                --medium-text: #495057;
                --light-text: #6c757d;
            }
            
            body {
                background-color: #f5f5f5;
                font-family: 'Poppins', sans-serif;
                color: var(--dark-text);
            }
            
            .invoice-content {
                background-color: white;
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                margin: 30px auto;
                padding: 30px;
            }
            
            .invoice-header {
                border-bottom: 3px solid var(--primary-color);
                padding-bottom: 25px;
                margin-bottom: 30px;
            }
            
            .invoice-logo h1 {
                color: var(--primary-color);
                font-weight: 700;
                margin-bottom: 5px;
                font-size: 28px;
            }
            
            .invoice-logo .tagline {
                color: var(--light-text);
                font-size: 14px;
                font-weight: 400;
            }
            
            .invoice-number h2 {
                color: var(--dark-text);
                font-weight: 600;
                margin-bottom: 5px;
            }
            
            .invoice-number .text-danger {
                color: var(--primary-color) !important;
            }
            
            .invoice-details {
                background-color: var(--light-bg);
                border-radius: 10px;
                padding: 25px;
                margin-bottom: 30px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
            
            .section-title {
                color: var(--primary-color);
                border-bottom: 2px solid #dee2e6;
                padding-bottom: 12px;
                margin-bottom: 25px;
                font-weight: 600;
                font-size: 22px;
            }
            
            .info-block {
                margin-bottom: 20px;
            }
            
            .info-label {
                font-weight: 600;
                color: var(--medium-text);
                margin-bottom: 5px;
                font-size: 15px;
            }
            
            .info-value {
                color: var(--dark-text);
                font-size: 15px;
            }
            
            .table.invoice-table {
                border: 1px solid #dee2e6;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
            
            .table.invoice-table thead th {
                background-color: var(--primary-color);
                color: white;
                border: none;
                font-weight: 600;
                padding: 15px;
                text-transform: uppercase;
                font-size: 14px;
                letter-spacing: 0.5px;
            }
            
            .table.invoice-table tbody tr {
                transition: background-color 0.2s ease;
            }
            
            .table.invoice-table tbody tr:nth-child(even) {
                background-color: var(--light-bg);
            }
            
            .table.invoice-table tbody tr:hover {
                background-color: #fff3f3;
            }
            
            .table.invoice-table td, .table.invoice-table th {
                padding: 15px;
                vertical-align: middle;
            }
            
            .total-row {
                background-color: var(--primary-color) !important;
                color: white !important;
                font-weight: 600;
                font-size: 18px;
            }
            
            .total-row td {
                border-top: 2px solid rgba(255, 255, 255, 0.2) !important;
            }
            
            .status-badge {
                display: inline-block;
                padding: 8px 20px;
                border-radius: 25px;
                font-weight: 600;
                font-size: 14px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }
            
            .status-pending {
                background-color: #fff3cd;
                color: #856404;
            }
            
            .status-approved {
                background-color: #d4edda;
                color: #155724;
            }
            
            .status-for-delivery {
                background-color: #cfe2ff;
                color: #084298;
            }
            
            .status-complete {
                background-color: #d1ecf1;
                color: #0c5460;
            }
            
            .status-received {
                background-color: #d4edda;
                color: #155724;
            }
            
            .payment-info-box {
                background-color: var(--light-bg);
                border-radius: 10px;
                padding: 25px;
                border-left: 4px solid var(--primary-color);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
            
            .invoice-footer {
                margin-top: 40px;
                padding-top: 25px;
                border-top: 1px solid #dee2e6;
                text-align: center;
                color: var(--light-text);
                font-size: 14px;
            }
            
            .btn-print, .btn-download {
                border-radius: 50px;
                font-weight: 500;
                padding: 12px 30px;
                transition: all 0.3s ease;
                font-size: 16px;
                border: none;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }
            
            .btn-print {
                background-color: #343a40;
            }
            
            .btn-print:hover {
                background-color: #212529;
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            }
            
            .btn-download {
                background-color: var(--primary-color);
            }
            
            .btn-download:hover {
                background-color: var(--secondary-color);
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(139, 0, 0, 0.2);
            }
            
            @media print {
                .d-print-none {
                    display: none !important;
                }
                
                body {
                    background-color: white;
                    padding: 0;
                    margin: 0;
                }
                
                .invoice-content {
                    box-shadow: none;
                    margin: 0;
                    padding: 20px;
                }
                
                .invoice-btn-section {
                    display: none !important;
                }
            }
            
            @media (max-width: 768px) {
                .invoice-content {
                    padding: 20px;
                    margin: 15px;
                }
                
                .text-end {
                    text-align: left !important;
                }
                
                .invoice-details .col-md-6 {
                    margin-bottom: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-16 invoice-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="invoice-inner-9" id="invoice_wrapper">
                            <!-- Invoice Header -->
                            <div class="invoice-header">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="invoice-logo">
                                            <h1>Yannis Meat Shop</h1>
                                            <p class="tagline">Premium Quality Meat Products</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6 text-end">
                                        <div class="invoice-number">
                                            <h2>
                                                Purchase Order #<span class="text-danger">{{ $purchase->purchase_no }}</span>
                                            </h2>
                                            <p class="text-muted mb-2">Date: {{ $purchase->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}</p>
                                            @php
                                                $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                                                $statusClass = '';
                                                $statusLabel = '';
                                                
                                                switch($statusValue) {
                                                    case 0:
                                                        $statusClass = 'status-pending';
                                                        $statusLabel = 'Pending';
                                                        break;
                                                    case 1:
                                                        $statusClass = 'status-approved';
                                                        $statusLabel = 'Approved';
                                                        break;
                                                    case 2:
                                                        $statusClass = 'status-for-delivery';
                                                        $statusLabel = 'For Delivery';
                                                        break;
                                                    case 3:
                                                        $statusClass = 'status-complete';
                                                        $statusLabel = 'Complete';
                                                        break;
                                                    case 4:
                                                        $statusClass = 'status-received';
                                                        $statusLabel = 'Received';
                                                        break;
                                                    default:
                                                        $statusClass = 'status-pending';
                                                        $statusLabel = 'Pending';
                                                }
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Invoice Information -->
                            <div class="invoice-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-block">
                                            <h5 class="info-label">Supplier:</h5>
                                            <p class="info-value"><strong>{{ $purchase->supplier->name }}</strong></p>
                                            <p class="info-value">{{ $purchase->supplier->shopname }}</p>
                                            <p class="info-value">{{ $purchase->supplier->email }}</p>
                                            <p class="info-value">{{ $purchase->supplier->phone }}</p>
                                            <p class="info-value">{{ $purchase->supplier->address }}</p>
                                        </div>
                                        
                                        <div class="info-block mt-4">
                                            <h5 class="info-label">Order Details:</h5>
                                            <p class="info-value"><strong>Order Number:</strong> {{ $purchase->purchase_no }}</p>
                                            <p class="info-value"><strong>Created By:</strong> {{ $purchase->createdBy->name ?? 'N/A' }}</p>
                                            @if($purchase->updatedBy)
                                            <p class="info-value"><strong>Updated By:</strong> {{ $purchase->updatedBy->name }}</p>
                                            @endif
                                            <p class="info-value"><strong>Total Items:</strong> {{ $purchase->details->count() }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="info-block">
                                            <h5 class="info-label">From:</h5>
                                            <p class="info-value"><strong>Yannis Meat Shop</strong></p>
                                            <p class="info-value">+63 09082413347</p>
                                            <p class="info-value">YannisMeatshop@gmail.com</p>
                                            <p class="info-value">Banay banay, 17 Aragon Compound, Cabuyao City, 4025 Laguna</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="order-summary">
                                <h3 class="section-title">Order Summary</h3>
                                <div class="table-outer">
                                    <table class="table invoice-table">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Item Description</th>
                                                <th class="align-middle text-center">Unit Price</th>
                                                <th class="align-middle text-center">Quantity</th>
                                                <th class="align-middle text-center">Total</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($purchase->details as $item)
                                            <tr>
                                                <td class="align-middle">
                                                    <strong>{{ $item->product->name }}</strong><br>
                                                    <small class="text-muted">Code: {{ $item->product->code }}</small>
                                                </td>
                                                <td class="align-middle text-center">
                                                    ₱{{ number_format($item->unitcost, 2) }}
                                                    <small class="text-muted d-block">per {{ $item->product->unit->name ?? 'kg' }}</small>
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ $item->quantity }} {{ $item->product->unit->name ?? 'kg' }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    ₱{{ number_format($item->total, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="3" class="text-end align-middle">
                                                    <strong>Subtotal</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>₱{{ number_format($purchase->total_amount, 2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr class="total-row">
                                                <td colspan="3" class="text-end align-middle">
                                                    <strong>Total Amount</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>₱{{ number_format($purchase->total_amount, 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Notes -->
                            @if($purchase->notes)
                            <div class="payment-info mt-4">
                                <h3 class="section-title">Order Notes</h3>
                                <div class="payment-info-box">
                                    <div class="info-value">
                                        {!! nl2br(e($purchase->notes)) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Invoice Footer -->
                            <div class="invoice-footer">
                                <p>Thank you for your business with Yannis Meat Shop!</p>
                                <p>This is a computer-generated purchase order. No signature required.</p>
                            </div>
                        </div>
                        
                        <!-- Print/Download Buttons -->
                        <div class="invoice-btn-section clearfix d-print-none text-center mt-4 mb-4">
                            <a href="javascript:window.print()" class="btn btn-lg btn-print">
                                <i class="fa fa-print me-2"></i>Print Invoice
                            </a>
                            <a id="invoice_download_btn" class="btn btn-lg btn-download ms-3">
                                <i class="fa fa-download me-2"></i>Download Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/app.js') }}"></script>
    </body>
</html>