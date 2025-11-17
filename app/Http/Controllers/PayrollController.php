<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollRecord;
use App\Models\User;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = PayrollRecord::with('user')->latest()->paginate(10);
        return view('expenses.payroll.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staff = User::all();
        return view('expenses.payroll.create', compact('staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'basic_salary' => 'required|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        $validated['total_salary'] = ($validated['basic_salary'] + ($validated['bonuses'] ?? 0)) - ($validated['deductions'] ?? 0);

        PayrollRecord::create($validated);

        return redirect()->route('expenses.payroll.index')->with('success', 'Payroll record created successfully');
    }
}
