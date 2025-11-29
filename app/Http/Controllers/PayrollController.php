<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollRecord;
use App\Models\User;
use App\Models\Staff;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = PayrollRecord::with(['user', 'staff'])->latest()->paginate(10);
        return view('expenses.payroll.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staff = Staff::where('status', 'active')->get();
        $users = User::all();
        return view('expenses.payroll.create', compact('staff', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'nullable|exists:staff,id',
            'user_id' => 'nullable|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'working_days' => 'nullable|integer|min:1|max:31',
            'daily_rate' => 'nullable|numeric|min:0',
            'basic_salary' => 'required|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        $validated['total_salary'] = ($validated['basic_salary'] + ($validated['bonuses'] ?? 0)) - ($validated['deductions'] ?? 0);
        $validated['created_by'] = auth()->id();

        PayrollRecord::create($validated);

        return redirect()->route('expenses.payroll.index')->with('success', 'Payroll record created successfully');
    }
}
