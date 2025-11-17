<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    /**
     * Display supplier's procurement deliveries
     */
    public function index(): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return view('supplier.deliveries.index', [
                'procurements' => collect([]),
                'supplier' => null
            ]);
        }

        // Get procurements with product details, ordered by most recent
        $procurements = $supplier->procurements()
            ->with(['product.unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('supplier.deliveries.index', compact('procurements', 'supplier'));
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
}
