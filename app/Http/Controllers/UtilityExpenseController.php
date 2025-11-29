<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UtilityExpense;

class UtilityExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = UtilityExpense::notVoid()->latest()->paginate(10);
        $voidedExpenses = UtilityExpense::voided()->with('voidedBy')->latest()->paginate(10);
        return view('expenses.utilities.index', compact('expenses', 'voidedExpenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.utilities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'billing_period' => 'required|string',
            'due_date' => 'nullable|date',
            'paid_date' => 'nullable|date',
            'status' => 'required|in:pending,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        UtilityExpense::create($validated);

        return redirect()->route('expenses.utilities.index')->with('success', 'Utility expense created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = UtilityExpense::findOrFail($id);
        return view('expenses.utilities.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = UtilityExpense::findOrFail($id);
        return view('expenses.utilities.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expense = UtilityExpense::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'billing_period' => 'required|string',
            'due_date' => 'nullable|date',
            'paid_date' => 'nullable|date',
            'status' => 'required|in:pending,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.utilities.index')->with('success', 'Utility expense updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = UtilityExpense::findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.utilities.index')->with('success', 'Utility expense deleted successfully');
    }

    /**
     * Void the specified expense (instead of delete)
     */
    public function void(Request $request, UtilityExpense $utilityExpense)
    {
        // Check if already voided
        if ($utilityExpense->is_void) {
            return redirect()->route('expenses.utilities.index')->with('error', 'This expense is already voided');
        }

        $validated = $request->validate([
            'void_reason' => 'required|string|max:500',
        ]);

        $utilityExpense->void($validated['void_reason'], auth()->id());

        return redirect()->route('expenses.utilities.index')->with('success', 'Utility expense voided successfully');
    }
}
