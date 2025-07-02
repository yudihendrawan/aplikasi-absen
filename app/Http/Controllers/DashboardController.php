<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Schedule;
use App\Models\Store;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        return view('pages.dashboard.dashboard', [
            'totalStores' => Store::count(),
            'todaySchedulesCount' => Schedule::where('visit_date', $today)->count(),
            'activeLeavesToday' => Leave::whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'attendancesToday' => Attendance::whereDate('attended_at', $today)->count(),
            'invoicesToday' => \DB::table('schedule_store_visits')
                ->join('attendances', 'schedule_store_visits.id', '=', 'attendances.schedule_store_visit_id')
                ->whereDate('attendances.attended_at', $today)
                ->selectRaw('SUM(expected_invoice_amount) as expected, SUM(actual_invoice_amount) as actual')
                ->first(),
            'visitsToday' => Schedule::with(['sales', 'storeVisits.store', 'storeVisits.attendance'])
                ->where('visit_date', $today)
                ->get(),
            'leavesToday' => Leave::with('user')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->get(),
        ]);
    }
}
