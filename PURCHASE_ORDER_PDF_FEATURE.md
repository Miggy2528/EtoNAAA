# Purchase Order PDF Download Feature

## Overview
This feature adds a professional, printable PDF download capability to the Purchase Order page at `/suppliers/{id}/purchase-order`. Instead of just taking a screenshot, users can now generate a personalized PDF document with all purchase order details.

## What's New

### 1. **Download PDF Button**
- Added a prominent "Download PDF" button next to the Print button
- Red colored button with PDF icon for easy identification
- Located in the card header for quick access

### 2. **Professional PDF Template**
Created a custom PDF template (`purchase-order-pdf.blade.php`) with:
- **Company Branding**: Yannis Meat Shop logo and contact information
- **Supplier Information**: Complete supplier details
- **Order Information**: PO number, date, prepared by, delivery terms
- **Product Table**: Detailed list with product names, codes, categories, quantities, and pricing
- **Financial Summary**: Subtotal, tax (12%), and total amount
- **Additional Notes**: Custom instructions or special requirements
- **Signature Section**: Spaces for prepared by, approved by, and supplier signature
- **Professional Styling**: Clean, organized layout optimized for A4 paper

### 3. **Dynamic Data Collection**
The PDF includes:
- All products with quantities > 0
- Accurate pricing calculations (subtotal, tax, total)
- Custom notes entered by the user
- Auto-generated PO number
- Current date and preparer's name

## Technical Implementation

### Files Modified/Created

1. **Controller**: `app/Http/Controllers/SupplierController.php`
   - Added `downloadPurchaseOrderPdf()` method
   - Validates product data, quantities, and pricing
   - Generates PDF using DomPDF library
   - Returns downloadable PDF file

2. **Route**: `routes/web.php`
   - Added POST route: `/suppliers/{supplier}/purchase-order/pdf`
   - Route name: `suppliers.purchase-order.pdf`

3. **View**: `resources/views/suppliers/purchase-order.blade.php`
   - Added "Download PDF" button
   - Added `downloadPDF()` JavaScript function
   - Modified form fields with IDs for data collection
   - Collects and submits data via hidden form

4. **PDF Template**: `resources/views/suppliers/purchase-order-pdf.blade.php`
   - Complete standalone HTML document
   - Optimized CSS for PDF rendering
   - Professional business document layout
   - Includes all necessary purchase order details

## How to Use

1. Navigate to: `http://127.0.0.1:8000/suppliers/3/purchase-order` (or any supplier ID)
2. Select products and enter quantities
3. Add any additional notes in the text area
4. Click the **"Download PDF"** button
5. PDF will be generated and downloaded automatically
6. File name format: `purchase-order-PO-YYYYMMDD-XXXX.pdf`

## Features

### PDF Content Includes:
- ✅ Company header with contact information
- ✅ Purchase Order number and date
- ✅ Supplier complete details (name, shop, email, phone, address)
- ✅ Order information (prepared by, items count, payment terms)
- ✅ Detailed product list with:
  - Product name and code
  - Category
  - Unit of measurement
  - Quantity ordered
  - Unit price
  - Line total
- ✅ Financial summary (subtotal, tax, grand total)
- ✅ Additional notes/instructions
- ✅ Signature blocks for authorization
- ✅ Professional footer with business message

### Styling Features:
- Clean, professional layout
- Color-coded sections (primary color: #8B0000)
- Alternating row colors for easy reading
- Proper spacing and typography
- Print-optimized formatting
- A4 portrait orientation

## Validation

The system validates:
- At least one product with quantity > 0 must be selected
- All product IDs must exist in the database
- Quantities must be positive integers
- Prices must be valid numbers
- PO number must be present
- Calculations (subtotal, tax, total) must be accurate

## Benefits

1. **Professional Documentation**: Creates business-ready purchase orders
2. **No Screenshots Needed**: Generates actual PDF documents
3. **Customizable**: Can add notes and special instructions
4. **Accurate Data**: Pulls live product and pricing information
5. **Easy Distribution**: PDF can be emailed or printed easily
6. **Record Keeping**: Downloadable files for archival purposes
7. **Signature Ready**: Includes signature blocks for formal approval

## Technical Notes

- Uses Laravel DomPDF package (barryvdh/laravel-dompdf)
- PDF generation happens server-side
- Data is sent via POST to prevent URL length issues
- Opens in new tab/window for download
- Supports all products assigned to the supplier
- Calculates tax at 12% automatically
- Includes product categories and units from database

## Future Enhancements (Optional)

- Email PDF directly to supplier
- Save PDF to server for record keeping
- Add company logo image
- Multiple tax rate options
- Custom terms and conditions
- Multi-currency support
- Batch PDF generation for multiple POs
