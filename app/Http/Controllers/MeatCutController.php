<?php

namespace App\Http\Controllers;

use App\Models\MeatCut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeatCutController extends Controller
{
    public function index(Request $request)
    {
        $query = MeatCut::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('meat_type', 'like', "%{$search}%")
                  ->orWhere('quality_grade', 'like', "%{$search}%");
            });
        }

        // Filter by meat type
        if ($request->filled('meat_type')) {
            $query->where('meat_type', $request->meat_type);
        }

        // Filter by quality grade
        if ($request->filled('cut_type')) {
            $query->where('quality_grade', $request->cut_type);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability);
        }

        $meatCuts = $query->orderBy('meat_type')->orderBy('name')->paginate(10);
        
        // Get distinct values for filters
        $meatTypes = MeatCut::distinct()->pluck('meat_type');
        $cutTypes = MeatCut::distinct()->pluck('quality_grade');

        return view('meat-cuts.index', compact('meatCuts', 'meatTypes', 'cutTypes'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', '');
        
        // Pre-populate data based on type
        $prefillData = [];
        
        if ($type === 'by-product') {
            $prefillData['is_by_product'] = true;
            $prefillData['is_processing_meat'] = false;
        } elseif ($type === 'processing') {
            $prefillData['is_processing_meat'] = true;
            $prefillData['is_by_product'] = false;
        }
        
        return view('meat-cuts.create', compact('prefillData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'meat_type' => 'required|string|max:255',
            'meat_subtype' => 'nullable|string|max:255',
            'quality_grade' => 'required|string|max:255',
            'quality' => 'nullable|string|max:255',
            'preparation_type' => 'nullable|string|max:255',
            'preparation_style' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'default_price_per_kg' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'minimum_stock_level' => 'required|integer|min:0',
            'is_available' => 'boolean',
            'is_by_product' => 'boolean',
            'is_processing_meat' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('meat-cuts', 'public');
            $validated['image_path'] = $path;
        }

        MeatCut::create($validated);

        return redirect()->route('meat-cuts.index')
            ->with('success', 'Meat cut created successfully.');
    }

    public function edit(MeatCut $meatCut)
    {
        return view('meat-cuts.edit', compact('meatCut'));
    }

    public function update(Request $request, MeatCut $meatCut)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'meat_type' => 'required|string|max:255',
            'meat_subtype' => 'nullable|string|max:255',
            'quality_grade' => 'required|string|max:255',
            'quality' => 'nullable|string|max:255',
            'preparation_type' => 'nullable|string|max:255',
            'preparation_style' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'default_price_per_kg' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'is_by_product' => 'boolean',
            'is_processing_meat' => 'boolean',
            'minimum_stock_level' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($meatCut->image_path) {
                Storage::disk('public')->delete($meatCut->image_path);
            }
            $path = $request->file('image')->store('meat-cuts', 'public');
            $validated['image_path'] = $path;
        }

        $meatCut->update($validated);

        return redirect()->route('meat-cuts.index')
            ->with('success', 'Meat cut updated successfully.');
    }

    public function destroy(MeatCut $meatCut)
    {
        if ($meatCut->image_path) {
            Storage::disk('public')->delete($meatCut->image_path);
        }

        $meatCut->delete();

        return redirect()->route('meat-cuts.index')
            ->with('success', 'Meat cut deleted successfully.');
    }

    // Controller methods for meat cut management
}