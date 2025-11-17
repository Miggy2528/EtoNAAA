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
        $expenses = UtilityExpense::latest()->paginate(10);
        return view('expenses.utilities.index', compact('expenses'));
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
}
