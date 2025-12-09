<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Procurement;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DeliveryController extends Controller
{
    /**
     * Display supplier's procurement deliveries with filtering
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return view('supplier.deliveries.index', [
                'procurements' => new LengthAwarePaginator([], 0, 15),
                'supplier' => null,
                'stats' => $this->getEmptyStats()
            ]);
        }

        // Build query with filters
        $query = $supplier->procurements()->with(['product.unit']);

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        // Get procurements with pagination
        $procurements = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get delivery statistics
        $stats = $this->getDeliveryStats($supplier->id);

        return view('supplier.deliveries.index', compact('procurements', 'supplier', 'stats'));
    }

    /**
     * Show single procurement delivery details
     */
    public function show($id): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $procurement = $supplier->procurements()
            ->with(['product.unit'])
            ->findOrFail($id);

        return view('supplier.deliveries.show', compact('procurement', 'supplier'));
    }

    /**
     * Update delivery status (for supplier confirmation)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $procurement = $supplier->procurements()->findOrFail($id);

        $validated = $request->validate([
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        // Update procurement with supplier delivery confirmation
        $procurement->update([
            'notes' => ($procurement->notes ?? '') . "\n\n[Supplier Update - " . now()->format('Y-m-d H:i') . "]: " . ($validated['delivery_notes'] ?? 'Delivery confirmed')
        ]);

        return redirect()->route('supplier.deliveries.show', $procurement->id)
            ->with('success', 'Delivery status updated successfully.');
    }

    /**
     * Get delivery statistics
     */
    private function getDeliveryStats($supplierId)
    {
        $total = Procurement::where('supplier_id', $supplierId)->count();
        $onTime = Procurement::where('supplier_id', $supplierId)->where('status', 'on-time')->count();
        $delayed = Procurement::where('supplier_id', $supplierId)->where('status', 'delayed')->count();
        $pending = Procurement::where('supplier_id', $supplierId)->where('status', 'pending')->count();
        
        return [
            'total' => $total,
            'on_time' => $onTime,
            'delayed' => $delayed,
            'pending' => $pending,
            'on_time_rate' => $total > 0 ? round(($onTime / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get empty statistics
     */
    private function getEmptyStats()
    {
        return [
            'total' => 0,
            'on_time' => 0,
            'delayed' => 0,
            'pending' => 0,
            'on_time_rate' => 0,
        ];
    }
}
