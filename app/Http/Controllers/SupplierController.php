<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Enums\SupplierType;
use App\Enums\PurchaseStatus;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    public function index()
    {
        $suppliers = Supplier::withCount(['procurements', 'purchases'])
            ->with([
                'purchases',
                'procurements' => function($query) {
                    $query->selectRaw('supplier_id, 
                        COUNT(*) as total_deliveries,
                        SUM(total_cost) as total_spent,
                        SUM(CASE WHEN status = "on-time" THEN 1 ELSE 0 END) as on_time_deliveries,
                        AVG(defective_rate) as avg_defect_rate')
                    ->groupBy('supplier_id');
                }
            ])
            ->latest()
            ->paginate(10);
            
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = Str::slug($request->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/suppliers', $filename);
            $validated['photo'] = $filename;
        }

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        // Load relationships to avoid N+1 queries
        $supplier->load(['purchases', 'products']);
        
        // Load products with their purchases relationship for status display
        $products = $supplier->products()->with(['category', 'purchases' => function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id)->latest();
        }])->get();
        
        // Get procurement statistics
        $procurementStats = DB::table('procurements')
            ->where('supplier_id', $supplier->id)
            ->selectRaw('
                COUNT(*) as total_procurements,
                SUM(total_cost) as total_spent,
                SUM(quantity_supplied) as total_quantity,
                SUM(CASE WHEN status = "on-time" THEN 1 ELSE 0 END) as on_time_count,
                AVG(defective_rate) as avg_defect_rate,
                MAX(delivery_date) as last_delivery
            ')
            ->first();
            
        // Get recent procurements (last 10) with product and unit eager loaded
        $recentProcurements = $supplier->procurements()
            ->with(['product.unit'])
            ->orderBy('delivery_date', 'desc')
            ->limit(10)
            ->get();
            
        return view('suppliers.show', compact('supplier', 'products', 'procurementStats', 'recentProcurements'));
    }

    public function purchaseOrder(Supplier $supplier)
    {
        // Get products assigned to this supplier with category and unit relationships
        $products = $supplier->products()->with(['category', 'unit'])->get();
        
        return view('suppliers.purchase-order', compact('supplier', 'products'));
    }

    public function downloadPurchaseOrderPdf(Request $request, Supplier $supplier)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.unit_price' => 'required|numeric|min:0',
                'products.*.total' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
                'po_number' => 'required|string',
                'subtotal' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0'
            ]);

            // Prepare data for PDF
            $products = collect($validated['products'])->map(function($item) {
                $product = Product::with(['category', 'unit'])->find($item['id']);
                return [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total']
                ];
            });

            $data = [
                'supplier' => $supplier,
                'products' => $products,
                'po_number' => $validated['po_number'],
                'date' => now()->format('F d, Y'),
                'prepared_by' => auth()->user()->name,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'],
                'total_amount' => $validated['total_amount']
            ];

            // Generate PDF
            $pdf = Pdf::loadView('suppliers.purchase-order-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'sans-serif'
                ]);

            $filename = 'purchase-order-' . $validated['po_number'] . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed - return back with errors
            return back()->withErrors($e->errors())->with('error', 'Validation failed. Please check the form data.');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('PDF Generation Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function storePurchaseOrder(Request $request, Supplier $supplier)
    {
        // Validate the request
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Start database transaction
            DB::beginTransaction();

            // Create the purchase order
            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'date' => now(),
                'purchase_no' => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'status' => PurchaseStatus::PENDING, // Use the enum value
                'total_amount' => 0, // Will be updated after calculating
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null
            ]);

            // Calculate total amount and create purchase details
            $totalAmount = 0;
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['id']);
                $quantity = $productData['quantity'];
                $unitCost = $product->buying_price ?? 0;
                $total = $quantity * $unitCost;
                
                PurchaseDetails::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unitcost' => $unitCost,
                    'total' => $total
                ]);
                
                $totalAmount += $total;
            }

            // Update the total amount in the purchase
            $purchase->update(['total_amount' => $totalAmount]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order created successfully!',
                'purchase_id' => $purchase->id,
                'purchase_no' => $purchase->purchase_no
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create purchase order. Please try again.'
            ], 500);
        }
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($supplier->photo) {
                Storage::delete('public/suppliers/' . $supplier->photo);
            }
            
            $photo = $request->file('photo');
            $filename = Str::slug($request->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/suppliers', $filename);
            $validated['photo'] = $filename;
        }

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->photo) {
            Storage::delete('public/suppliers/' . $supplier->photo);
        }
        
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function assignProducts(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        // Update the supplier_id for each selected product
        Product::whereIn('id', $validated['product_ids'])
               ->update(['supplier_id' => $supplier->id]);
        
        // For products that were previously assigned to this supplier but are no longer selected,
        // we need to remove the association
        Product::where('supplier_id', $supplier->id)
               ->whereNotIn('id', $validated['product_ids'])
               ->update(['supplier_id' => null]);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Products assigned successfully.');
    }

    public function deactivate(Supplier $supplier)
    {
        $supplier->update(['status' => 'inactive']);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deactivated successfully.');
    }
}