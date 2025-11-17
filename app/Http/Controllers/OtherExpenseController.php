<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtherExpense;

class OtherExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = OtherExpense::latest()->paginate(10);
        return view('expenses.other.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.other.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        OtherExpense::create($validated);

        return redirect()->route('expenses.other.index')->with('success', 'Expense created successfully');
    }
}
