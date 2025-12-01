<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\MeatCut;
use App\Models\ProductUpdateLog;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['category', 'unit', 'meatCut']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('meatCut', function($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereColumn('quantity', '<=', 'quantity_alert');
            }
        }

        // Filter by meat cut
        if ($request->filled('meat_cut_id')) {
            $query->where('meat_cut_id', $request->meat_cut_id);
        }

        // New filters: meat type, quality, preparation type (from MeatCut)
        if ($request->filled('meat_type')) {
            $query->whereHas('meatCut', function($mq) use ($request) {
                $mq->where('meat_type', $request->meat_type);
            });
        }
        if ($request->filled('meat_subtype')) {
            $query->whereHas('meatCut', function($mq) use ($request) {
                $mq->where('meat_subtype', $request->meat_subtype);
            });
        }
        if ($request->filled('quality')) {
            $query->whereHas('meatCut', function($mq) use ($request) {
                $mq->where('quality', $request->quality);
            });
        }
        if ($request->filled('quality_grade')) {
            $query->whereHas('meatCut', function($mq) use ($request) {
                $mq->where('quality_grade', $request->quality_grade);
            });
        }
        if ($request->filled('preparation_type')) {
            $query->whereHas('meatCut', function($mq) use ($request) {
                $mq->where('preparation_type', $request->preparation_type);
            });
        }
        if ($request->filled('preparation_style')) {
            $query->whereHas('meatCut', function($mq) use ($request) {
                $mq->where('preparation_style', $request->preparation_style);
            });
        }

        $products = $query->orderBy('name')->paginate(12);
        
        // Get data for filters
        $categories = Category::all(['id', 'name']);
        $meatCuts = MeatCut::all(['id', 'name']);
        $meatTypes = MeatCut::select('meat_type')->whereNotNull('meat_type')->distinct()->orderBy('meat_type')->pluck('meat_type');
        $meatSubtypes = MeatCut::select('meat_subtype')
            ->whereNotNull('meat_subtype')
            ->when($request->meat_type, function($q) use ($request) { $q->where('meat_type', $request->meat_type); })
            ->distinct()->pluck('meat_subtype');
        $qualities = MeatCut::select('quality')->whereNotNull('quality')->distinct()->pluck('quality');
        $qualityGrades = MeatCut::select('quality_grade')
            ->whereNotNull('quality_grade')
            ->distinct()
            ->orderBy('quality_grade')
            ->pluck('quality_grade');
        $preparations = MeatCut::select('preparation_type')->whereNotNull('preparation_type')->distinct()->pluck('preparation_type');
        $preparationStyles = MeatCut::select('preparation_style')
            ->whereNotNull('preparation_style')
            ->when($request->preparation_type, function($q) use ($request) { $q->where('preparation_type', $request->preparation_type); })
            ->distinct()->pluck('preparation_style');

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'meatCuts' => $meatCuts,
            'meatTypes' => $meatTypes,
            'meatSubtypes' => $meatSubtypes,
            'qualities' => $qualities,
            'qualityGrades' => $qualityGrades,
            'preparations' => $preparations,
            'preparationStyles' => $preparationStyles,
        ]);
    }

    public function create(Request $request)
    {
        $categories = Category::all(['id', 'name']);
        $units = Unit::all(['id', 'name']);
        $meatCuts = MeatCut::all(['id', 'name', 'default_price_per_kg', 'animal_type', 'meat_type', 'quality', 'preparation_type', 'meat_subtype', 'quality_grade', 'preparation_style']);

        if ($request->has('category')) {
            $categories = Category::whereSlug($request->get('category'))->get();
        }

        if ($request->has('unit')) {
            $units = Unit::whereSlug($request->get('unit'))->get();
        }

        return view('products.create', [
            'categories' => $categories,
            'units' => $units,
            'meatCuts' => $meatCuts,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $meatCut = MeatCut::find($request->meat_cut_id);
            $data = $request->except('selling_price');
            if ($meatCut) {
                $data['price_per_kg'] = $meatCut->default_price_per_kg;
                // Keep DB consistent if selling_price column exists
                $data['selling_price'] = $data['price_per_kg'];
            }
            $product = Product::create($data);

            // Set who created the product
            $product->updated_by = auth()->id();
            $product->save();

            /**
             * Handle image upload
             */
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

                // Validate file before uploading
                if ($file->isValid()) {
                    $file->storeAs('products/', $filename, 'public');
                    $product->update([
                        'product_image' => $filename
                    ]);
                } else {
                    return back()->withErrors(['product_image' => 'Invalid image file']);
                }
            }

            // Log the product creation
            $this->logProductUpdate($product, 'created');

            return redirect()
                ->back()
                ->with('success', 'Product has been created with code: ' . $product->code);

        } catch (\Exception $e) {
            // Handle any unexpected errors
            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while creating the product: ' . $e->getMessage()]);
        }
    }

    public function show(Product $product)
    {
        // Generate a barcode
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'meatCuts' => MeatCut::all(['id', 'name', 'default_price_per_kg']),
            'product' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            // Log the request data for debugging
            \Log::info('Product update request received', [
                'product_id' => $product->id,
                'request_data' => $request->except('product_image'),
                'has_file' => $request->hasFile('product_image'),
                'user_id' => auth()->id()
            ]);
            
            // Track what changed
            $original = $product->getOriginal();
            $changes = [];
            
            foreach ($request->except('product_image', '_token', '_method', '_debug') as $key => $value) {
                if (array_key_exists($key, $original) && $original[$key] != $value) {
                    $changes[$key] = [
                        'old' => $original[$key],
                        'new' => $value
                    ];
                }
            }

            $data = $request->except(['product_image', 'selling_price', '_debug']);
            if ($request->meat_cut_id) {
                $meatCut = MeatCut::find($request->meat_cut_id);
                if ($meatCut) {
                    $data['price_per_kg'] = $meatCut->default_price_per_kg;
                    // Keep DB consistent if selling_price column exists
                    $data['selling_price'] = $data['price_per_kg'];
                }
            }

            // Log the data that will be updated
            \Log::info('Product update data prepared', $data);
            
            // Update the product with all data except image
            $product->update($data);
            
            // Set who updated the product
            $product->updated_by = auth()->id();
            $product->save();

            // Handle image upload separately to avoid issues with model state
            if ($request->hasFile('product_image')) {
                // Delete old image if exists
                if ($product->product_image) {
                    \Storage::disk('public')->delete('products/' . $product->product_image);
                }

                // Prepare new image
                $file = $request->file('product_image');
                $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

                // Store new image to public storage
                $file->storeAs('products/', $fileName, 'public');

                // Save new image name to database
                $product->product_image = $fileName;
                $product->save();
                
                $changes['product_image'] = ['old' => $product->getOriginal('product_image'), 'new' => $fileName];
            }

            // Log the product update with changes
            $this->logProductUpdate($product, 'updated', $changes);

            \Log::info('Product update successful', ['product_id' => $product->id]);
            
            return redirect()
                ->route('products.index')
                ->with('success', 'Product has been updated!');
        } catch (\Exception $e) {
            \Log::error('Product update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'product_id' => $product->id ?? 'unknown',
                'user_id' => auth()->id() ?? 'unknown'
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        // Log the product deletion before deleting
        $this->logProductUpdate($product, 'deleted');

        /**
         * Delete photo if exists.
         */
        if ($product->product_image) {
            \Storage::disk('public')->delete('products/' . $product->product_image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been deleted!');
    }

    /**
     * Log product update activity
     */
    private function logProductUpdate(Product $product, string $action, array $changes = [])
    {
        ProductUpdateLog::create([
            'product_id' => $product->id,
            'staff_id' => null, // Staff table doesn't exist, using users table instead
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => !empty($changes) ? json_encode($changes) : null,
        ]);
    }
}