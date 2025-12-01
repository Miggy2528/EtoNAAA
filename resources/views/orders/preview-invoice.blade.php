<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            Preview Invoice #{{ $order->invoice_no }} - Yannis Meat Shop
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
            
            .payment-info-box {
                background-color: var(--light-bg);
                border-radius: 10px;
                padding: 25px;
                border-left: 4px solid var(--primary-color);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
            
            .proof-of-payment {
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }
            
            .invoice-footer {
                margin-top: 40px;
                padding-top: 25px;
                border-top: 1px solid #dee2e6;
                text-align: center;
                color: var(--light-text);
                font-size: 14px;
            }
            
            .btn-print, .btn-download, .btn-back {
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
            
            .btn-back {
                background-color: #6c757d;
                color: white;
            }
            
            .btn-back:hover {
                background-color: #5a6268;
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(108, 117, 125, 0.2);
            }
            
            .invoice-btn-section {
                text-align: center;
                padding: 30px;
                background-color: #f8f9fa;
            }
            
            .text-muted {
                color: var(--light-text);
            }
            
            /* CSS to replace dollar sign with peso symbol */
            .peso-symbol:before {
                content: "₱";
            }
            
            @media print {
                body {
                    background: white;
                    padding: 0;
                    margin: 0;
                }
                
                .invoice-content {
                    box-shadow: none;
                    border-radius: 0;
                    padding: 20px;
                }
                
                .invoice-btn-section {
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
                .invoice-details .row > div {
                    margin-bottom: 20px;
                }
                
                .table.invoice-table {
                    font-size: 14px;
                }
                
                .table.invoice-table td, .table.invoice-table th {
                    padding: 10px;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-content">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="invoice-logo">
                            <h1>Yannis Meat Shop</h1>
                            <p class="tagline">Premium Quality Meat Products</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="invoice-number">
                            <h2>Invoice #<span class="text-danger">{{ $order->invoice_no }}</span></h2>
                            <p class="text-muted mb-0">Date: {{ $order->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
                            
            <!-- Invoice Information -->
            <div class="invoice-details">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-block">
                            <h5 class="info-label">Bill To:</h5>
                            @if(isset($editedData))
                                <p class="info-value"><strong>{{ $editedData['customer_name'] ?? $order->customer->name }}</strong></p>
                                <p class="info-value">{{ $editedData['customer_email'] ?? $order->customer_email }}</p>
                                <p class="info-value">{{ $editedData['customer_phone'] ?? $order->contact_phone }}</p>
                                @if(!empty($editedData['receiver_name']) || !empty($order->receiver_name))
                                <p class="info-value"><strong>Receiver: {{ $editedData['receiver_name'] ?? $order->receiver_name }}</strong></p>
                                @endif
                                <p class="info-value">{{ $editedData['delivery_address'] ?? $order->delivery_address }}</p>
                            @else
                                <p class="info-value"><strong>{{ $order->customer->name }}</strong></p>
                                <p class="info-value">{{ $order->customer_email }}</p>
                                <p class="info-value">{{ $order->contact_phone }}</p>
                                @if(!empty($order->receiver_name))
                                <p class="info-value"><strong>Receiver: {{ $order->receiver_name }}</strong></p>
                                @endif
                                <p class="info-value">{{ $order->delivery_address }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-block">
                            <h5 class="info-label">From:</h5>
                            <p class="info-value"><strong>Yannis Meat Shop</strong></p>
                            <p class="info-value">+63 09082413347</p>
                            <p class="info-value">YannisMeatshop@gmail.com</p>
                            <p class="info-value">Banay banay, 17 Aragon Compound, Cabuyao City, 4025 Laguna</p>
                        </div>
                        
                        <div class="info-block mt-4">
                            <h5 class="info-label">Order Status:</h5>
                            @php
                                $statusClass = match($order->order_status) {
                                    \App\Enums\OrderStatus::PENDING => 'status-pending',
                                    \App\Enums\OrderStatus::FOR_DELIVERY => 'status-for-delivery',
                                    \App\Enums\OrderStatus::COMPLETE => 'status-complete',
                                    \App\Enums\OrderStatus::CANCELLED => 'status-cancelled',
                                    default => 'status-pending'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $order->order_status->label() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
                            
            <h3 class="section-title">Order Summary</h3>
            <div class="table-responsive">
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
                        @if(isset($editedData) && isset($editedData['items']))
                            @foreach ($order->details as $index => $item)
                                @php
                                    $editedItem = $editedData['items'][$index] ?? [];
                                    $unitPrice = $editedItem['unit_price'] ?? $item->unitcost;
                                    $quantity = $editedItem['quantity'] ?? $item->quantity;
                                    $total = $unitPrice * $quantity;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong><br>
                                        <small class="text-muted">Code: {{ $item->product->code }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="peso-symbol"></span>{{ number_format($unitPrice, 2) }}
                                        <small class="text-muted d-block">per kg</small>
                                    </td>
                                    <td class="text-center">
                                        {{ $quantity }} kg
                                    </td>
                                    <td class="text-center">
                                        <span class="peso-symbol"></span>{{ number_format($total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach ($order->details as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong><br>
                                        <small class="text-muted">Code: {{ $item->product->code }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="peso-symbol"></span>{{ number_format($item->unitcost, 2) }}
                                        <small class="text-muted d-block">per kg</small>
                                    </td>
                                    <td class="text-center">
                                        {{ $item->quantity }} kg
                                    </td>
                                    <td class="text-center">
                                        <span class="peso-symbol"></span>{{ number_format($item->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        @if(isset($editedData) && isset($editedData['items']))
                            @php
                                $totalAmount = 0;
                                foreach ($order->details as $index => $item) {
                                    $editedItem = $editedData['items'][$index] ?? [];
                                    $unitPrice = $editedItem['unit_price'] ?? $item->unitcost;
                                    $quantity = $editedItem['quantity'] ?? $item->quantity;
                                    $totalAmount += $unitPrice * $quantity;
                                }
                            @endphp
                            <tr>
                                <td colspan="3" class="text-end">
                                    <strong>Subtotal</strong>
                                </td>
                                <td class="text-center">
                                    <strong><span class="peso-symbol"></span>{{ number_format($totalAmount, 2) }}</strong>
                                </td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" class="text-end">
                                    <strong>Total Amount</strong>
                                </td>
                                <td class="text-center">
                                    <strong><span class="peso-symbol"></span>{{ number_format($totalAmount, 2) }}</strong>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="3" class="text-end">
                                    <strong>Subtotal</strong>
                                </td>
                                <td class="text-center">
                                    <strong><span class="peso-symbol"></span>{{ number_format($order->total, 2) }}</strong>
                                </td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" class="text-end">
                                    <strong>Total Amount</strong>
                                </td>
                                <td class="text-center">
                                    <strong><span class="peso-symbol"></span>{{ number_format($order->total, 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
                            
            @if($order->payment_type === 'gcash' && ($order->proof_of_payment || $order->gcash_reference))
            <h3 class="section-title">Payment Information</h3>
            <div class="payment-info-box">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-block">
                            <h5 class="info-label">Payment Method</h5>
                            <p class="info-value">GCash Payment</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-block">
                            <h5 class="info-label">GCash Reference</h5>
                            <p class="info-value">{{ $order->gcash_reference ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                @if($order->proof_of_payment)
                <div class="info-block mt-3">
                    <h5 class="info-label">Proof of Payment</h5>
                    <div class="proof-of-payment text-center">
                        <img src="{{ asset('storage/' . $order->proof_of_payment) }}" 
                             alt="Proof of Payment" 
                             class="img-fluid" 
                             style="max-height: 200px;">
                    </div>
                </div>
                @endif
            </div>
            @endif
                            
            <div class="invoice-footer">
                <p>Thank you for your business with Yannis Meat Shop!</p>
                <p>This is a computer-generated invoice. No signature required.</p>
            </div>
        </div>
                        
        <div class="invoice-btn-section clearfix d-print-none text-center mt-4 mb-4">
            <a href="javascript:window.print()" class="btn btn-print">
                <i class="fa fa-print me-2"></i>Print Invoice
            </a>
            <a href="{{ route('orders.edit-invoice', $order->id) }}" class="btn btn-back ms-3">
                <i class="fa fa-arrow-left me-2"></i>Back to Edit
            </a>
            <a href="{{ route('order.downloadInvoice', $order->id) }}" class="btn btn-download ms-3" target="_blank">
                <i class="fa fa-download me-2"></i>Download PDF
            </a>
        </div>
        <script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/app.js') }}"></script>
    </body>
</html>