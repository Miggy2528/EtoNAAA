<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Purchase;
use App\Enums\PurchaseStatus;
use Illuminate\Support\Facades\DB;

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
                'purchases' => collect([]),
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
            'status' => 'required|in:0,2,3', // 0=Pending, 2=For Delivery, 3=Complete
        ]);

        $newStatus = (int) $validated['status'];

        $oldLabel = $purchase->status->label();
        $newLabel = PurchaseStatus::from($newStatus)->label();

        // Update the purchase status
        $purchase->update([
            'status' => $newStatus,
            'notes' => ($purchase->notes ?? '') . "\n\n[Status Update - " . now()->format('Y-m-d H:i') . "]: Status changed from {$oldLabel} to {$newLabel} by supplier."
        ]);

        return redirect()->route('supplier.purchases.show', $purchase->id)
            ->with('success', "Order status updated to {$newLabel} successfully.");
    }

    /**
     * Get purchase statistics
     */
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
}
