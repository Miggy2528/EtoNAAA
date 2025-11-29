<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\MeatCut;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the product catalog
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit'])
            ->where('quantity', '>', 0);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Price range filter
        if ($request->has('price_range') && $request->price_range) {
            switch ($request->price_range) {
                case '0-100':
                    $query->where('price_per_kg', '>=', 0)->where('price_per_kg', '<=', 100);
                    break;
                case '101-200':
                    $query->where('price_per_kg', '>=', 101)->where('price_per_kg', '<=', 200);
                    break;
                case '201-500':
                    $query->where('price_per_kg', '>=', 201)->where('price_per_kg', '<=', 500);
                    break;
                case '501+':
                    $query->where('price_per_kg', '>', 501);
                    break;
            }
        }

        // Unit filter
        if ($request->has('unit') && $request->unit) {
            $query->whereHas('unit', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->unit}%");
            });
        }

        // Stock status filter
        if ($request->has('stock_status') && $request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('quantity', '>', 5); // More than 5 items
                    break;
                case 'low_stock':
                    $query->where('quantity', '>', 0)->where('quantity', '<=', 5); // 1-5 items
                    break;
            }
        }

        // Sort by price (default)
        $query->orderBy('price_per_kg', 'asc');

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('customer.products.index', compact('products', 'categories'));
    }

    /**
     * Display a specific product
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit']);
        
        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('quantity', '>', 0)
            ->limit(4)
            ->get();

        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Display products by category
     */
    public function category(Category $category, Request $request)
    {
        $query = Product::with(['category', 'unit'])
            ->where('category_id', $category->id)
            ->where('quantity', '>', 0);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Price range filter
        if ($request->has('price_range') && $request->price_range) {
            switch ($request->price_range) {
                case '0-100':
                    $query->where('price_per_kg', '>=', 0)->where('price_per_kg', '<=', 100);
                    break;
                case '101-200':
                    $query->where('price_per_kg', '>=', 101)->where('price_per_kg', '<=', 200);
                    break;
                case '201-500':
                    $query->where('price_per_kg', '>=', 201)->where('price_per_kg', '<=', 500);
                    break;
                case '501+':
                    $query->where('price_per_kg', '>', 501);
                    break;
            }
        }

        // Unit filter
        if ($request->has('unit') && $request->unit) {
            $query->whereHas('unit', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->unit}%");
            });
        }

        // Stock status filter
        if ($request->has('stock_status') && $request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('quantity', '>', 5); // More than 5 items
                    break;
                case 'low_stock':
                    $query->where('quantity', '>', 0)->where('quantity', '<=', 5); // 1-5 items
                    break;
            }
        }

        // Sort by price (default)
        $query->orderBy('price_per_kg', 'asc');

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('customer.products.category', compact('products', 'category', 'categories'));
    }
}