<?php

namespace App\Http\Controllers\Purchase;

use App\Enums\PurchaseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier')->latest();

        // Filter by supplier if supplier_id is provided
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        return view('purchases.index', [
            'purchases' => $query->get(),
        ]);
    }

    public function approvedPurchases()
    {
        $purchases = Purchase::with(['supplier'])
            ->where('status', PurchaseStatus::APPROVED)->get(); // 1 = approved

        return view('purchases.approved-purchases', [
            'purchases' => $purchases,
        ]);
    }

    public function show(Purchase $purchase)
    {
        $purchase->loadMissing([
            'supplier', 
            'details.product.category', 
            'details.product.unit', 
            'createdBy', 
            'updatedBy'
        ]);

        return view('purchases.show', [
            'purchase' => $purchase
        ]);
    }

    public function edit(Purchase $purchase)
    {
        // N+1 Problem if load 'createdBy', 'updatedBy',
        $purchase->with(['supplier', 'details'])->get();

        return view('purchases.edit', [
            'purchase' => $purchase,
        ]);
    }

    public function create()
    {
        return view('purchases.create', [
            'categories' => Category::select(['id', 'name'])->get(),
            'suppliers' => Supplier::select(['id', 'name'])->get(),
        ]);
    }

    public function store(StorePurchaseRequest $request)
    {
        $purchase = Purchase::create($request->all());

        /*
         * TODO: Must validate that
         */
        if (! $request->invoiceProducts == null) {
            $pDetails = [];

            foreach ($request->invoiceProducts as $product) {
                // Skip rows without a selected product or quantity
                if (!isset($product['product_id']) || !$product['product_id'] || !isset($product['quantity']) || !$product['quantity']) {
                    continue;
                }

                $pDetails['purchase_id'] = $purchase['id'];
                $pDetails['product_id'] = (int) $product['product_id'];
                $pDetails['quantity'] = (int) $product['quantity'];

                // Sanitize numeric strings that may be formatted
                $unitcostRaw = $product['unitcost'] ?? 0;
                $totalRaw = $product['total'] ?? 0;
                $unitcost = (float) str_replace(',', '', $unitcostRaw);
                $total = (float) str_replace(',', '', $totalRaw);

                // Fallback to calculated total if missing
                if ($total <= 0 && $unitcost > 0 && $pDetails['quantity'] > 0) {
                    $total = $unitcost * $pDetails['quantity'];
                }

                $pDetails['unitcost'] = $unitcost;
                $pDetails['total'] = $total;
                $pDetails['created_at'] = Carbon::now();

                //PurchaseDetails::insert($pDetails);
                $purchase->details()->insert($pDetails);
            }
        }

        return redirect()
            ->route('suppliers.show', $purchase->supplier_id)
            ->with('success', 'Purchase has been created and is available in the supplier orders module.');
    }

    public function update(Purchase $purchase, Request $request)
    {
        $products = PurchaseDetails::where('purchase_id', $purchase->id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['quantity' => DB::raw('quantity+'.$product->quantity)]);
        }

        Purchase::findOrFail($purchase->id)
            ->update([
                //'purchase_status' => 1, // 1 = approved, 0 = pending
                'status' => PurchaseStatus::APPROVED,
                'updated_by' => auth()->user()->id,
            ]);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been approved!');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been deleted!');
    }

    /**
     * Mark purchase as received (admin side)
     */
    public function markAsReceived(Purchase $purchase)
    {
        $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
        
        // Only allow marking as received if status is Complete (3)
        if ($statusValue != 3) {
            return redirect()
                ->route('purchases.show', $purchase)
                ->with('error', 'Purchase can only be marked as received when it is in Complete status.');
        }

        $purchase->update([
            'status' => 4, // RECEIVED
            'updated_by' => auth()->user()->id,
            'notes' => ($purchase->notes ?? '') . "\n\n[Admin - " . now()->format('Y-m-d H:i') . "]: Order marked as Received by " . auth()->user()->name
        ]);

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Purchase has been marked as received!');
    }

    /**
     * Mark purchase as complete (admin side)
     */
    public function markAsComplete(Purchase $purchase)
    {
        $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
        
        // Only allow marking as complete if status is Approved (1) or For Delivery (2)
        if ($statusValue != 1 && $statusValue != 2) {
            return redirect()
                ->route('purchases.show', $purchase)
                ->with('error', 'Purchase can only be marked as complete from Approved or For Delivery status.');
        }

        $purchase->update([
            'status' => 3, // COMPLETE
            'updated_by' => auth()->user()->id,
            'notes' => ($purchase->notes ?? '') . "\n\n[Admin - " . now()->format('Y-m-d H:i') . "]: Order marked as Complete by " . auth()->user()->name
        ]);

        // Create procurement records for supplier analytics
        $this->createProcurementRecords($purchase);

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Purchase has been marked as complete!');
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
            // Skip if procurement already exists for this purchase detail
            $existingProcurement = \App\Models\Procurement::where('supplier_id', $purchase->supplier_id)
                ->where('product_id', $detail->product_id)
                ->where('quantity_supplied', $detail->quantity)
                ->where('total_cost', $detail->total)
                ->whereBetween('created_at', [now()->subMinutes(5), now()])
                ->first();
                
            if ($existingProcurement) {
                continue;
            }
            
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

    public function dailyPurchaseReport()
    {
        $purchases = Purchase::with(['supplier'])
            //->where('purchase_status', 1)
            ->where('date', today()->format('Y-m-d'))->get();

        return view('purchases.daily-report', [
            'purchases' => $purchases,
        ]);
    }

    public function getPurchaseReport()
    {
        return view('purchases.report-purchase');
    }

    public function exportPurchaseReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        $purchases = DB::table('purchase_details')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->join('users', 'users.id', '=', 'purchases.created_by')
            ->whereBetween('purchases.purchase_date', [$sDate, $eDate])
            ->where('purchases.purchase_status', '1')
            ->select('purchases.purchase_no', 'purchases.purchase_date', 'purchases.supplier_id', 'products.code', 'products.name', 'purchase_details.quantity', 'purchase_details.unitcost', 'purchase_details.total', 'users.name as created_by')
            ->get();

        dd($purchases);

        $purchase_array[] = [
            'Date',
            'No Purchase',
            'Supplier',
            'Product Code',
            'Product',
            'Quantity',
            'Unitcost',
            'Total',
            'Created By'
        ];

        foreach ($purchases as $purchase) {
            $purchase_array[] = [
                'Date' => $purchase->purchase_date,
                'No Purchase' => $purchase->purchase_no,
                'Supplier' => $purchase->supplier_id,
                'Product Code' => $purchase->product_code,
                'Product' => $purchase->product_name,
                'Quantity' => $purchase->quantity,
                'Unitcost' => $purchase->unitcost,
                'Total' => $purchase->total,
            ];
        }

        $this->exportExcel($purchase_array);
    }

    public function exportExcel($products)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="purchase-report.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Download purchase order invoice as PDF
     */
    public function downloadInvoice(Purchase $purchase)
    {
        // Load the purchase with all necessary relationships
        $purchase->load(['supplier', 'details.product.unit', 'createdBy', 'updatedBy']);

        // Generate PDF using DOMPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('purchases.invoice', compact('purchase'));
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Set options for better rendering
        $pdf->setOptions([
            'enable-php' => true,
            'enable-font-subsetting' => true,
            'encoding' => 'UTF-8',
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'timeout' => 300, // 5 minutes timeout
            'chroot' => public_path(),
        ]);
        
        // Download the PDF file
        return $pdf->download('purchase-order-invoice-' . $purchase->purchase_no . '.pdf');
    }
}
