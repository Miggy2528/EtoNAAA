<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Staff::withCount('performances')
            ->withAvg('performances', 'overall_performance');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('position', 'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        // Apply position filter
        if ($request->filled('position')) {
            $query->where('position', $request->input('position'));
        }

        // Apply sorting
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        
        switch ($sort) {
            case 'performance':
                $query->orderBy('performances_avg_overall_performance', $direction);
                break;
            case 'date_hired':
                $query->orderBy('date_hired', $direction);
                break;
            case 'name':
            default:
                $query->orderBy('name', $direction);
                break;
        }

        $staff = $query->get();

        // Get unique departments and positions for filter dropdowns
        $departments = Staff::select('department')->whereNotNull('department')->distinct()->pluck('department');
        $positions = Staff::select('position')->distinct()->pluck('position');

        // Prepare data for performance chart
        $performanceData = $staff->map(function ($member) {
            return [
                'name' => $member->name,
                'performance' => round($member->performances_avg_overall_performance ?? 0, 1)
            ];
        })->sortByDesc('performance')->take(10); // Top 10 performers
        
        // Convert to array to ensure it's properly serialized
        $performanceData = $performanceData->values()->all();

        return view('admin.staff.index', compact('staff', 'departments', 'positions', 'performanceData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'date_hired' => 'nullable|date',
            'status' => 'required|in:Active,Inactive',
        ]);

        Staff::create($validated);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        $staff->load(['performances' => function($query) {
            $query->orderBy('month', 'desc');
        }]);

        // Prepare performance trend data for chart
        $performanceTrend = $staff->performances->map(function ($performance) {
            return [
                'month' => \Carbon\Carbon::parse($performance->month)->format('M Y'),
                'performance' => $performance->overall_performance,
                'attendance' => $performance->attendance_rate,
                'task_completion' => $performance->task_completion_rate,
                'feedback' => $performance->customer_feedback_score * 20 // Convert to percentage
            ];
        })->sortBy('month');
        
        // Convert to array to ensure it's properly serialized
        $performanceTrend = $performanceTrend->values()->all();

        return view('admin.staff.show', compact('staff', 'performanceTrend'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'date_hired' => 'nullable|date',
            'status' => 'required|in:Active,Inactive',
        ]);

        $staff->update($validated);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member has been deleted successfully!');
    }
}