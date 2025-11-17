<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display supplier's purchases
     */
    public function index(): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return view('supplier.purchases.index', [
                'purchases' => collect([]),
                'supplier' => null
            ]);
        }

        // Get purchases with details, ordered by most recent
        $purchases = $supplier->purchases()
            ->with(['details.product.unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('supplier.purchases.index', compact('purchases', 'supplier'));
    }

    /**
     * Show single purchase details
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
}
