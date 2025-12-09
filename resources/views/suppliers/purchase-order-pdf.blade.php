<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $po_number }} - Yannis Meat Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* Header Section */
        .header {
            border-bottom: 3px solid #8B0000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-left {
            width: 50%;
        }

        .header-right {
            width: 50%;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
        }

        .company-info {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
        }

        .po-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .po-number {
            font-size: 16px;
            color: #8B0000;
            font-weight: bold;
        }

        .po-date {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: #fff3cd;
            color: #856404;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
        }

        /* Details Section */
        .details-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .details-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .details-col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }

        .info-item {
            margin-bottom: 5px;
            font-size: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 100px;
        }

        .info-value {
            color: #333;
        }

        /* Table Section */
        .table-section {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #8B0000;
            color: white;
        }

        table thead th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table tbody td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .product-name {
            font-weight: bold;
            color: #333;
        }

        .product-code {
            font-size: 9px;
            color: #777;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-label {
            display: table-cell;
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
            color: #555;
            font-size: 11px;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-size: 11px;
            color: #333;
        }

        .summary-total {
            border-top: 2px solid #8B0000;
            padding-top: 10px;
            margin-top: 10px;
        }

        .summary-total .summary-label,
        .summary-total .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #8B0000;
        }

        /* Notes Section */
        .notes-section {
            clear: both;
            background-color: #f8f9fa;
            padding: 15px;
            margin-top: 30px;
            border-left: 4px solid #8B0000;
            border-radius: 3px;
        }

        .notes-title {
            font-size: 12px;
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 8px;
        }

        .notes-content {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
        }

        /* Footer Section */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #777;
            font-size: 9px;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 10px;
            font-weight: bold;
        }

        .signature-label {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="company-name">Yannis Meat Shop</div>
                    <div class="company-tagline">Premium Quality Meat Products</div>
                    <div class="company-info">
                        Banay banay, 17 Aragon Compound<br>
                        Cabuyao City, 4025 Laguna<br>
                        Phone: +63 09082413347<br>
                        Email: YannisMeatshop@gmail.com
                    </div>
                </div>
                <div class="header-right">
                    <div class="po-title">PURCHASE ORDER</div>
                    <div class="po-number">#{{ $po_number }}</div>
                    <div class="po-date">Date: {{ $date }}</div>
                    <span class="status-badge">Pending</span>
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <div class="details-row">
                <div class="details-col">
                    <div class="section-title">Supplier Information</div>
                    <div class="info-item">
                        <span class="info-label">Company Name:</span>
                        <span class="info-value">{{ $supplier->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Shop Name:</span>
                        <span class="info-value">{{ $supplier->shopname }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Person:</span>
                        <span class="info-value">{{ $supplier->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $supplier->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $supplier->phone }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ $supplier->address }}</span>
                    </div>
                </div>
                <div class="details-col">
                    <div class="section-title">Order Information</div>
                    <div class="info-item">
                        <span class="info-label">PO Number:</span>
                        <span class="info-value">{{ $po_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value">{{ $date }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Prepared By:</span>
                        <span class="info-value">{{ $prepared_by }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Items:</span>
                        <span class="info-value">{{ $products->count() }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Terms:</span>
                        <span class="info-value">As per agreement</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Delivery Date:</span>
                        <span class="info-value">To be confirmed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="table-section">
            <div class="section-title">Ordered Products</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Product Description</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 10%;" class="text-center">Unit</th>
                        <th style="width: 10%;" class="text-center">Quantity</th>
                        <th style="width: 12%;" class="text-right">Unit Price</th>
                        <th style="width: 13%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="product-name">{{ $item['product']->name }}</div>
                            <div class="product-code">Code: {{ $item['product']->code }}</div>
                        </td>
                        <td>{{ $item['product']->category->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item['product']->unit->name ?? 'kg' }}</td>
                        <td class="text-center">{{ $item['quantity'] }}</td>
                        <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                        <td class="text-right">₱{{ number_format($item['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-row">
                <div class="summary-label">Subtotal:</div>
                <div class="summary-value">₱{{ number_format($subtotal, 2) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Tax (12%):</div>
                <div class="summary-value">₱{{ number_format($tax, 2) }}</div>
            </div>
            <div class="summary-row summary-total">
                <div class="summary-label">Total Amount:</div>
                <div class="summary-value">₱{{ number_format($total_amount, 2) }}</div>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Notes -->
        @if($notes)
        <div class="notes-section">
            <div class="notes-title">Additional Notes & Instructions</div>
            <div class="notes-content">{!! nl2br(e($notes)) !!}</div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">{{ $prepared_by }}</div>
                <div class="signature-label">Prepared By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">_____________________</div>
                <div class="signature-label">Approved By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">_____________________</div>
                <div class="signature-label">Supplier Signature</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated purchase order and is valid without signature.</p>
            <p>Please confirm receipt and expected delivery date within 24 hours.</p>
            <p>Thank you for your business partnership with Yannis Meat Shop!</p>
        </div>
    </div>
</body>
</html>
