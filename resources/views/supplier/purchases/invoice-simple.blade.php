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
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #333;
                margin: 0;
                padding: 20px;
            }
            
            .invoice-container {
                max-width: 800px;
                margin: 0 auto;
                border: 1px solid #ccc;
                padding: 20px;
            }
            
            .header {
                text-align: center;
                border-bottom: 2px solid #8B0000;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            
            .header h1 {
                color: #8B0000;
                margin: 0 0 5px 0;
                font-size: 24px;
            }
            
            .header p {
                margin: 0;
                color: #666;
            }
            
            .meta-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
            }
            
            .meta-info div {
                flex: 1;
            }
            
            .section-title {
                color: #8B0000;
                border-bottom: 1px solid #ccc;
                padding-bottom: 5px;
                margin: 20px 0 15px 0;
                font-size: 16px;
                font-weight: bold;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            
            table th {
                background-color: #8B0000;
                color: white;
                padding: 8px;
                text-align: left;
            }
            
            table td {
                padding: 8px;
                border-bottom: 1px solid #ccc;
            }
            
            table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .total-row {
                background-color: #8B0000 !important;
                color: white !important;
                font-weight: bold;
            }
            
            .status-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 12px;
                font-weight: bold;
                font-size: 12px;
                background-color: #f0f0f0;
                color: #333;
            }
            
            .footer {
                text-align: center;
                margin-top: 30px;
                padding-top: 15px;
                border-top: 1px solid #ccc;
                color: #666;
                font-size: 11px;
            }
            
            /* Removed peso-symbol CSS as we're now using the actual peso symbol directly */
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <!-- Invoice Header -->
            <div class="header">
                <h1>Yannis Meat Shop</h1>
                <p>Premium Quality Meat Products</p>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h2>Purchase Order Invoice #{{ $purchase->purchase_no }}</h2>
                    <p style="margin: 0;">Date: {{ $purchase->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}</p>
                </div>
                <div>
                    @php
                        $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                        $statusLabel = match($statusValue) {
                            0 => 'Pending',
                            1 => 'Approved',
                            2 => 'For Delivery',
                            3 => 'Complete',
                            4 => 'Received',
                            default => 'Pending'
                        };
                    @endphp
                    <span class="status-badge">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <!-- Invoice Information -->
            <div style="display: flex; margin-bottom: 30px;">
                <div style="flex: 1; padding-right: 20px;">
                    <h3>Supplier Information</h3>
                    <p><strong>{{ $purchase->supplier->name }}</strong></p>
                    <p>{{ $purchase->supplier->shopname }}</p>
                    <p>{{ $purchase->supplier->email }}</p>
                    <p>{{ $purchase->supplier->phone }}</p>
                    <p>{{ $purchase->supplier->address }}</p>
                </div>
                <div style="flex: 1;">
                    <h3>From</h3>
                    <p><strong>Yannis Meat Shop</strong></p>
                    <p>+63 09082413347</p>
                    <p>YannisMeatshop@gmail.com</p>
                    <p>Banay banay, 17 Aragon Compound, Cabuyao City, 4025 Laguna</p>
                </div>
            </div>
            
            <h3 class="section-title" style="page-break-before: always;">Order Summary</h3>
            <table>
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
                            <small>Code: {{ $item->product->code }}</small>
                        </td>
                        <td class="text-center">
                            ₱{{ number_format($item->unitcost, 2) }}
                            <small>per {{ $item->product->unit->short_code ?? 'kg' }}</small>
                        </td>
                        <td class="text-center">
                            {{ $item->quantity }} {{ $item->product->unit->short_code ?? 'kg' }}
                        </td>
                        <td class="text-center">
                            ₱{{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">
                            <strong>Subtotal</strong>
                        </td>
                        <td class="text-center">
                            <strong>₱{{ number_format($purchase->total_amount, 2) }}</strong>
                        </td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">
                            <strong>Total Amount</strong>
                        </td>
                        <td class="text-center">
                            <strong>₱{{ number_format($purchase->total_amount, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            <h3 class="section-title">Additional Information</h3>
            <div style="display: flex;">
                <div style="flex: 1; padding-right: 20px;">
                    <h4>Created By</h4>
                    <p>
                        <strong>{{ $purchase->createdBy->name ?? 'N/A' }}</strong>
                    </p>
                </div>
                
                <div style="flex: 1;">
                    <h4>Order Details</h4>
                    <p><strong>{{ $purchase->details->count() }} Items</strong></p>
                </div>
            </div>
            
            <div class="footer">
                <p>Thank you for your business with Yannis Meat Shop!</p>
                <p>This is a computer-generated invoice. No signature required.</p>
            </div>
        </div>
    </body>
</html>