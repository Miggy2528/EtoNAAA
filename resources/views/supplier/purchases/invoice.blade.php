<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            Purchase Order Invoice #{{ $purchase->purchase_no }} - Yannis Meat Shop
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8 BOM">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline';">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- External CSS libraries -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}" onload="this.media='all'" onerror="this.onerror=null;this.href='{{ asset('assets/invoice/css/bootstrap.min.css') }}'">
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/fonts/font-awesome/css/font-awesome.min.css') }}" onload="this.media='all'" onerror="this.onerror=null;this.href='{{ asset('assets/invoice/fonts/font-awesome/css/font-awesome.min.css') }}'">
        <!-- Google fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" onload="this.media='all'" onerror="this.onerror=null;this.href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'">
        <!-- Custom Stylesheet -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}" onload="this.media='all'" onerror="this.onerror=null;this.href='{{ asset('assets/invoice/css/style.css') }}'">
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
                margin: 0;
                padding: 20px;
            }
            
            .invoice-container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                overflow: hidden;
            }
            
            .invoice-header {
                background-color: var(--primary-color);
                color: white;
                padding: 30px;
            }
            
            .invoice-header h1 {
                font-size: 28px;
                font-weight: 700;
                margin: 0 0 5px 0;
            }
            
            .invoice-header .tagline {
                font-size: 14px;
                opacity: 0.9;
                margin: 0;
            }
            
            .invoice-meta {
                padding: 30px;
                border-bottom: 1px solid #eee;
            }
            
            .invoice-number h2 {
                color: var(--dark-text);
                font-weight: 600;
                margin: 0 0 15px 0;
            }
            
            .invoice-details {
                display: flex;
                padding: 30px;
                gap: 30px;
            }
            
            .invoice-details-col {
                flex: 1;
            }
            
            .info-label {
                font-weight: 600;
                color: var(--medium-text);
                margin-bottom: 8px;
                font-size: 15px;
            }
            
            .info-value {
                color: var(--dark-text);
                font-size: 15px;
                margin-bottom: 15px;
            }
            
            .section-title {
                color: var(--primary-color);
                border-bottom: 2px solid #dee2e6;
                padding-bottom: 12px;
                margin: 0 30px 25px 30px;
                font-weight: 600;
                font-size: 22px;
            }
            
            .table.invoice-table {
                width: 100%;
                border-collapse: collapse;
                margin: 0 30px 30px 30px;
                page-break-inside: avoid;
            }
            
            .table.invoice-table thead th {
                background-color: var(--primary-color);
                color: white;
                font-weight: 600;
                padding: 15px;
                text-transform: uppercase;
                font-size: 14px;
                letter-spacing: 0.5px;
                text-align: center;
            }
            
            .table.invoice-table thead th:first-child {
                text-align: left;
            }
            
            .table.invoice-table tbody td {
                padding: 15px;
                border-bottom: 1px solid #eee;
                vertical-align: middle;
                page-break-inside: avoid;
            }
            
            .table.invoice-table tbody tr {
                page-break-inside: avoid;
            }
            
            .table.invoice-table tbody tr:last-child td {
                border-bottom: none;
            }
            
            .table.invoice-table tfoot td {
                padding: 15px;
                border-top: 1px solid #eee;
                font-weight: 600;
                page-break-inside: avoid;
            }
            
            .table.invoice-table tfoot tr {
                page-break-inside: avoid;
            }
            
            .text-end {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .total-row {
                background-color: var(--primary-color) !important;
                color: white !important;
                font-weight: 600;
                font-size: 18px;
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
            
            .invoice-footer {
                margin: 40px 30px;
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
                text-decoration: none;
                display: inline-block;
            }
            
            .btn-print {
                background-color: #343a40;
                color: white;
            }
            
            .btn-print:hover {
                background-color: #212529;
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            }
            
            .btn-download {
                background-color: var(--primary-color);
                color: white;
            }
            
            .btn-download:hover {
                background-color: var(--secondary-color);
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(139, 0, 0, 0.2);
            }
            
            .invoice-actions {
                text-align: center;
                padding: 30px;
                background-color: #f8f9fa;
            }
            
            .text-muted {
                color: var(--light-text);
            }
            
            /* Removed peso-symbol CSS as we're now using the actual peso symbol directly */
            
            @media print {
                body {
                    background: white;
                    padding: 0;
                    margin: 0;
                }
                
                .invoice-container {
                    box-shadow: none;
                    border-radius: 0;
                }
                
                .invoice-actions {
                    display: none;
                }
                
                .section-title {
                    page-break-after: avoid;
                }
                
                table {
                    page-break-inside: auto;
                }
                
                tr {
                    page-break-inside: avoid;
                    page-break-after: auto;
                }
                
                thead {
                    display: table-header-group;
                }
                
                tfoot {
                    display: table-footer-group;
                }
            }
            
            @media (max-width: 768px) {
                .invoice-details {
                    flex-direction: column;
                    gap: 20px;
                }
                
                .table.invoice-table {
                    margin: 0 20px 20px 20px;
                }
                
                .section-title {
                    margin: 0 20px 20px 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <h1>Yannis Meat Shop</h1>
                <p class="tagline">Premium Quality Meat Products</p>
            </div>
            
            <div class="invoice-meta">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h2>Purchase Order Invoice #<span class="text-danger">{{ $purchase->purchase_no }}</span></h2>
                        <p class="text-muted mb-0">Date: {{ $purchase->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}</p>
                    </div>
                    <div>
                        @php
                            $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                            $statusClass = match($statusValue) {
                                0 => 'status-pending',
                                1 => 'status-approved',
                                2 => 'status-for-delivery',
                                3 => 'status-complete',
                                4 => 'status-received',
                                default => 'status-pending'
                            };
                            $statusLabel = match($statusValue) {
                                0 => 'Pending',
                                1 => 'Approved',
                                2 => 'For Delivery',
                                3 => 'Complete',
                                4 => 'Received',
                                default => 'Pending'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>
                            
                            <!-- Invoice Information -->
                            <div class="invoice-details">
                                <div class="invoice-details-col">
                                    <h3 class="info-label">Supplier Information</h3>
                                    <p class="info-value"><strong>{{ $purchase->supplier->name }}</strong></p>
                                    <p class="info-value">{{ $purchase->supplier->shopname }}</p>
                                    <p class="info-value">{{ $purchase->supplier->email }}</p>
                                    <p class="info-value">{{ $purchase->supplier->phone }}</p>
                                    <p class="info-value">{{ $purchase->supplier->address }}</p>
                                </div>
                                <div class="invoice-details-col">
                                    <h3 class="info-label">From</h3>
                                    <p class="info-value"><strong>Yannis Meat Shop</strong></p>
                                    <p class="info-value">+63 09082413347</p>
                                    <p class="info-value">YannisMeatshop@gmail.com</p>
                                    <p class="info-value">Banay banay, 17 Aragon Compound, Cabuyao City, 4025 Laguna</p>
                                </div>
                            </div>
                            
                            <h3 class="section-title" style="page-break-before: always;">Order Summary</h3>
                            <!-- Force order summary to start on a new page to prevent splitting across pages -->
                            <table class="table invoice-table">
                                <thead>
                                    <tr>
                                        <th>Item Description</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->details as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name }}</strong><br>
                                            <small class="text-muted">Code: {{ $item->product->code }}</small>
                                        </td>
                                        <td class="text-center">
                                            ₱{{ number_format($item->unitcost, 2) }}
                                            <small class="text-muted d-block">per kg</small>
                                        </td>
                                        <td class="text-center">
                                            {{ $item->quantity }} kg
                                        </td>
                                        <td class="text-center">
                                            ₱{{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end">
                                            <strong>Subtotal</strong>
                                        </td>
                                        <td class="text-center">
                                            <strong>₱{{ number_format($purchase->total_amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                    <tr class="total-row">
                                        <td colspan="3" class="text-end">
                                            <strong>Total Amount</strong>
                                        </td>
                                        <td class="text-center">
                                            <strong>₱{{ number_format($purchase->total_amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <h3 class="section-title">Additional Information</h3>
                            <div style="display: flex; gap: 30px; padding: 0 30px;">
                                <div style="flex: 1;">
                                    <h4 class="info-label">Created By</h4>
                                    <p class="info-value">
                                        <strong>{{ $purchase->createdBy->name ?? 'N/A' }}</strong>
                                    </p>
                                </div>
                                
                                <div style="flex: 1;">
                                    <h4 class="info-label">Order Details</h4>
                                    <p class="info-value"><strong>{{ $purchase->details->count() }} Items</strong></p>
                                </div>
                            </div>
                            
                            <div class="invoice-footer">
                                <p>Thank you for your business with Yannis Meat Shop!</p>
                                <p>This is a computer-generated invoice. No signature required.</p>
                            </div>
                        </div>
                        
                        <div class="invoice-actions">
                            <a href="javascript:window.print()" class="btn btn-print">
                                <i class="fa fa-print me-2"></i>Print Invoice
                            </a>
                            <a id="invoice_download_btn" class="btn btn-download ms-3">
                                <i class="fa fa-download me-2"></i>Download PDF
                            </a>
        <script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/app.js') }}"></script>
    </body>
</html>