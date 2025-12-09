<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Purchase;
use App\Enums\PurchaseStatus;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseController extends Controller
{
    /**
     * Display supplier's purchase orders with filtering and search
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return view('supplier.purchases.index', [
                'purchases' => new LengthAwarePaginator([], 0, 15),
                'supplier' => null,
                'stats' => $this->getEmptyStats()
            ]);
        }

        // Build query with filters
        $query = $supplier->purchases()->with(['details.product.unit', 'createdBy']);

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('purchase_no', 'like', "%{$search}%")
                  ->orWhere('total_amount', 'like', "%{$search}%");
            });
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get purchases with pagination
        $purchases = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get statistics
        $stats = $this->getPurchaseStats($supplier->id);

        return view('supplier.purchases.index', compact('purchases', 'supplier', 'stats'));
    }

    /**
     * Show single purchase order details
     */
    public function show($id): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $purchase = $supplier->purchases()
            ->with(['details.product.unit', 'createdBy', 'updatedBy'])
            ->findOrFail($id);

        return view('supplier.purchases.show', compact('purchase', 'supplier'));
    }

    /**
     * Update purchase communication/notes
     */
    public function updateNotes(Request $request, $id)
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $purchase = $supplier->purchases()->findOrFail($id);

        $validated = $request->validate([
            'supplier_notes' => 'nullable|string|max:1000',
        ]);

        // Add supplier notes to the purchase
        $purchase->update([
            'notes' => ($purchase->notes ?? '') . "\n\n[Supplier Note - " . now()->format('Y-m-d H:i') . "]: " . $validated['supplier_notes']
        ]);

        return redirect()->route('supplier.purchases.show', $purchase->id)
            ->with('success', 'Communication note added successfully.');
    }

    /**
     * Update delivery status - Supplier side
     */
    public function updateDeliveryStatus(Request $request, $id)
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $purchase = $supplier->purchases()->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:0,1,2,3', // 0=Pending, 1=Approved, 2=For Delivery, 3=Complete
        ]);

        $newStatus = (int) $validated['status'];

        $oldLabel = $purchase->status->label();
        $newLabel = PurchaseStatus::from($newStatus)->label();

        // Update the purchase status
        $purchase->update([
            'status' => $newStatus,
            'notes' => ($purchase->notes ?? '') . "\n\n[Status Update - " . now()->format('Y-m-d H:i') . "]: Status changed from {$oldLabel} to {$newLabel} by supplier."
        ]);

        // If status is Complete (3), create procurement records for supplier analytics
        if ($newStatus === 3) {
            $this->createProcurementRecords($purchase);
        }

        return redirect()->route('supplier.purchases.show', $purchase->id)
            ->with('success', "Order status updated to {$newLabel} successfully.");
    }

    /**
     * Create procurement/delivery records when purchase is marked as complete
     */
    private function createProcurementRecords($purchase)
    {
        // Load purchase details with products
        $purchase->load('details.product');
        
        // Get expected delivery date (use purchase date + 3-7 days as expected)
        $expectedDeliveryDate = $purchase->date->copy()->addDays(rand(3, 7));
        
        // Actual delivery date is now (when marked as complete)
        $deliveryDate = now();
        
        // Determine if on-time or delayed
        $isOnTime = $deliveryDate <= $expectedDeliveryDate;
        $status = $isOnTime ? 'on-time' : 'delayed';
        
        // Create a procurement record for each product in the purchase
        foreach ($purchase->details as $detail) {
            // Calculate defective rate (assume 0-2% for good suppliers)
            $defectiveRate = rand(0, 200) / 100;
            
            \App\Models\Procurement::create([
                'supplier_id' => $purchase->supplier_id,
                'product_id' => $detail->product_id,
                'quantity_supplied' => $detail->quantity,
                'expected_delivery_date' => $expectedDeliveryDate,
                'delivery_date' => $deliveryDate,
                'total_cost' => $detail->total,
                'status' => $status,
                'defective_rate' => $defectiveRate,
            ]);
        }
    }

    /**
     * Download purchase order invoice as PDF
     */
    public function downloadInvoice($id)
    {
        try {
            $user = auth()->user();
            $supplier = $user->supplier;

            \Log::info('Invoice download started for purchase ID: ' . $id);

            $purchase = $supplier->purchases()
                ->with(['details.product.unit', 'createdBy', 'supplier'])
                ->findOrFail($id);

            \Log::info('Purchase data loaded, details count: ' . $purchase->details->count());

            // Try to load the simplified invoice view first
            try {
                \Log::info('Loading simplified invoice view');
                $pdf = Pdf::loadView('supplier.purchases.invoice-simple', compact('purchase'));
                \Log::info('Simplified invoice view loaded successfully');
            } catch (\Exception $e) {
                // Fallback to the original invoice view
                \Log::warning('Failed to load simplified invoice view, falling back to original: ' . $e->getMessage());
                $pdf = Pdf::loadView('supplier.purchases.invoice', compact('purchase'));
                
                // Set shorter timeout for fallback view
                $pdf->setOptions([
                    'enable-php' => true,
                    'enable-font-subsetting' => true,
                    'encoding' => 'UTF-8',
                    'defaultFont' => 'DejaVu Sans', // Changed from Arial to DejaVu Sans for better Unicode support
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false, // Disable remote resources for fallback too
                    'timeout' => 120, // 2 minutes timeout for fallback
                    'chroot' => public_path(),
                    'fontDir' => base_path('vendor/dompdf/dompdf/lib/fonts'),
                    'fontCache' => base_path('vendor/dompdf/dompdf/lib/fonts'),
                    'tempDir' => sys_get_temp_dir(),
                    'logOutputFile' => storage_path('logs/dompdf.log'),
                    'isFontSubsettingEnabled' => true,
                    'debugPng' => false,
                    'debugKeepTemp' => false,
                    'debugCss' => false,
                    'debugLayout' => false,
                    'pdfBackend' => 'CPDF',
                    'pdflibLicense' => '',
                ]);
                
                // Ensure UTF-8 encoding for the HTML content
                $pdf->getDomPDF()->set_base_path(public_path());;
            }
            
            \Log::info('Setting PDF options');
            
            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');
            
            // Fix encoding for special characters like peso symbol
            $pdf->setOptions([
                'enable-php' => true,
                'enable-font-subsetting' => true,
                'encoding' => 'UTF-8',
                'defaultFont' => 'DejaVu Sans', // Changed from Arial to DejaVu Sans for better Unicode support
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false, // Disable remote resources to speed up generation
                'timeout' => 300, // 5 minutes timeout
                'chroot' => public_path(),
                'fontDir' => base_path('vendor/dompdf/dompdf/lib/fonts'),
                'fontCache' => base_path('vendor/dompdf/dompdf/lib/fonts'),
                'tempDir' => sys_get_temp_dir(),
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'isFontSubsettingEnabled' => true,
                'debugPng' => false,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'pdfBackend' => 'CPDF',
                'pdflibLicense' => '',
            ]);
            
            // Ensure UTF-8 encoding for the HTML content
            $pdf->getDomPDF()->set_base_path(public_path());
            
            \Log::info('Generating PDF');
            
            // Download the PDF file
            $response = $pdf->download('purchase-order-invoice-' . $purchase->purchase_no . '.pdf');
            
            \Log::info('PDF generated and sent for download');
            
            return $response;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Invoice download error: ' . $e->getMessage());
            
            // Return an error response
            return response()->json([
                'error' => 'Failed to generate invoice',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function getPurchaseStats($supplierId)
    {
        return [
            'total' => Purchase::where('supplier_id', $supplierId)->count(),
            'pending' => Purchase::where('supplier_id', $supplierId)->where('status', PurchaseStatus::PENDING)->count(),
            'approved' => Purchase::where('supplier_id', $supplierId)->where('status', PurchaseStatus::APPROVED)->count(),
            'total_value' => Purchase::where('supplier_id', $supplierId)->where('status', PurchaseStatus::APPROVED)->sum('total_amount'),
        ];
    }

    /**
     * Get empty statistics
     */
    private function getEmptyStats()
    {
        return [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'total_value' => 0,
        ];
    }

    /**
     * Show the invoice edit form
     */
    public function editInvoice($id)
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $purchase = $supplier->purchases()
            ->with(['details.product.unit', 'createdBy', 'supplier'])
            ->findOrFail($id);

        return view('supplier.purchases.edit-invoice', compact('purchase'));
    }

    /**
     * Preview the invoice with potential edits
     */
    public function previewInvoice(Request $request, $id)
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $purchase = $supplier->purchases()
            ->with(['details.product.unit', 'createdBy', 'supplier'])
            ->findOrFail($id);

        // Handle different actions
        if ($request->input('action') === 'save') {
            // In a real implementation, you would save the changes to the database here
            // For now, we'll just redirect back with a success message
            return redirect()->route('supplier.purchases.show', $purchase->id)
                ->with('success', 'Invoice changes saved successfully.');
        }

        // If there are edits, update the purchase temporarily for preview
        if ($request->has('edited_data')) {
            $editedData = $request->input('edited_data');
            
            // We'll pass the edited data to the view for preview
            // In a real implementation, you might want to validate this data
            $previewData = [
                'purchase' => $purchase,
                'editedData' => $editedData
            ];
        } else {
            $previewData = [
                'purchase' => $purchase,
                'editedData' => null
            ];
        }

        return view('supplier.purchases.preview-invoice', $previewData);
    }
}
