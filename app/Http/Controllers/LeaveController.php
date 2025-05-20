<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Leave::with(['user']);

        if ($search = $request->input('search')) {
            $query->where("name","like","%$search%")->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$search%"));
               
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        $sortableColumns = ['start_date'];
        $sortBy = $request->input('sort_by');
        $sortDir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        if ($sortBy && in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest('start_date');
        }

        $leaves = $query->paginate(10)->withQueryString();
        if ($request->ajax()) {
            return view('pages.leaves.table', compact('leaves'))->render();
        }

        return view('pages.leaves.index', compact('leaves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.leaves.index', compact('leaves'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'presents_id' => 'nullable|exists:presents,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i',
            'time_tolerance' => 'required|date_format:H:i',
        ]);

        Leave::create($request->all());

        return redirect()->route('leaves.index')->with('success', 'Leave created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Leave $leave, string $id)
    {
        $leave->load(['user', 'store', 'creator']);
        return view('pages.leaves.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leave $leave)
    {
        $leave->load(['user', 'store', 'creator']);
        return view('pages.leaves.edit', compact('leave'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'presents_id' => 'nullable|exists:presents,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i',
            'time_tolerance' => 'required|date_format:H:i',
        ]);

        $leave->update($request->all());

        return redirect()->route('leaves.index')->with('success', 'Leave updated successfully.');
    }

 
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()->route('leaves.index')->with('success', 'Leave deleted successfully.');
    }
}
