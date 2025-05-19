<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */ public function index(Request $request)
    {
        $query = Schedule::with(['user', 'store', 'creator']);

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"))
                ->orWhereHas('store', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }

        $sortableColumns = ['date', 'check_in', 'check_out', 'time_tolerance'];
        $sortBy = $request->input('sort_by');
        $sortDir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        if ($sortBy && in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest('date');
        }

        $schedules = $query->paginate(10)->withQueryString();
        if ($request->ajax()) {
            return view('pages.schedules.table', compact('schedules'))->render();
        }

        return view('pages.schedules.index', compact('schedules'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
