# Supplier Invoice Download Feature

## Overview
This feature adds the ability for suppliers to download purchase order invoices as PDF files from the supplier portal. The implementation follows the existing pattern used in the system for customer invoices but adapts it specifically for supplier purchase orders.

## Implementation Details

### 1. UI Changes
- Added a "Download Invoice" button to the supplier purchase order details page
- Button is positioned next to the "Back to Orders" button in the header
- Uses consistent styling with other action buttons in the system

### 2. Backend Implementation
- Added a new route: `GET /supplier/purchases/{id}/download-invoice`
- Created a new controller method `downloadInvoice` in `Supplier\PurchaseController`
- Uses the existing `barryvdh/laravel-dompdf` package for PDF generation
- Generates a professional-looking invoice PDF with all relevant purchase order information

### 3. Invoice Template
- Created a dedicated invoice template at `resources/views/supplier/purchases/invoice.blade.php`
- Template includes:
  - Company header with logo and contact information
  - Purchase order details (number, date, status)
  - Supplier information
  - Order items with pricing details
  - Total amount calculation
  - Status badge with color coding
  - Print and download functionality

### 4. Technical Details
- The PDF is generated using DOMPDF which converts HTML to PDF
- File name follows the pattern: `purchase-order-invoice-{purchase_no}.pdf`
- Paper size is set to A4 in portrait orientation
- The invoice includes all relevant purchase information:
  - Purchase order number
  - Creation date
  - Supplier details
  - Itemized list of products
  - Unit prices and quantities
  - Total amount
  - Order status

## Files Modified/Added

1. `resources/views/supplier/purchases/show.blade.php` - Added download button
2. `routes/web.php` - Added new route for invoice download
3. `app/Http/Controllers/Supplier/PurchaseController.php` - Added downloadInvoice method
4. `resources/views/supplier/purchases/invoice.blade.php` - New invoice template

## Usage
1. Suppliers can navigate to a purchase order details page
2. Click the "Download Invoice" button
3. The system generates and downloads a PDF invoice for the purchase order

## Features
- Professional invoice design with company branding
- Responsive layout that works on all devices
- Print functionality via the browser's print dialog
- PDF download with automatically generated filename
- Status indicators with color coding
- Complete order information including all line items

## Dependencies
- Uses existing `barryvdh/laravel-dompdf` package for PDF generation
- Leverages existing CSS and JavaScript assets from the customer invoice system
- No additional packages required

## Testing
The feature has been tested and verified to:
- Generate properly formatted PDF invoices
- Include all relevant purchase order information
- Use correct styling and branding
- Download with appropriate filenames
- Work across different browsers