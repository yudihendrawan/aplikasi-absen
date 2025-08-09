<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Schedule;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $user = auth()->user();

        $baseSchedule = Schedule::where('visit_date', $today);

        if ($user->hasRole('sales')) {
            $baseSchedule->where('user_id', $user->id);
        }

        $todaySchedulesCount = DB::table('schedule_store_visits')
            ->join('schedules', 'schedule_store_visits.schedule_id', '=', 'schedules.id')
            ->when($user->hasRole('sales'), fn($q) => $q->where('schedules.user_id', $user->id))
            ->where('schedules.visit_date', $today)
            ->distinct('schedule_store_visits.store_id')
            ->count('schedule_store_visits.store_id');

        $invoices = DB::table('schedule_store_visits')
            ->join('attendances', 'schedule_store_visits.id', '=', 'attendances.schedule_store_visit_id')
            ->join('schedules', 'schedule_store_visits.schedule_id', '=', 'schedules.id')
            ->when($user->hasRole('sales'), fn($q) => $q->where('schedules.user_id', $user->id))
            ->whereDate('attendances.attended_at', $today)
            ->selectRaw('SUM(expected_invoice_amount) as expected, SUM(actual_invoice_amount) as actual')
            ->first();

        $leavesToday = Leave::with('user')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereNotNull('approved_at')
            ->when($user->hasRole('sales'), fn($q) => $q->where('user_id', $user->id))
            ->get();

        $pendingLeaves = [];
        if ($user->hasRole('admin')) {
            $pendingLeaves = Leave::with('user')
                ->whereNull('approved_at')
                ->whereNull('rejected_at')
                ->orderBy('start_date')
                ->get();
        }

        return view('pages.dashboard.dashboard', [
            'totalStores' => Store::count(),
            'todaySchedulesCount' => $todaySchedulesCount,
            'activeLeavesToday' => Leave::whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->when($user->hasRole('sales'), fn($q) => $q->where('user_id', $user->id))
                ->count(),
            'attendancesToday' => Attendance::whereDate('attended_at', $today)
                ->when($user->hasRole('sales'), fn($q) => $q->whereHas('storeVisit.schedule', fn($qq) => $qq->where('user_id', $user->id)))
                ->count(),
            'invoicesToday' => $invoices,
            'visitsToday' => $baseSchedule->with(['sales', 'storeVisits.store', 'storeVisits.attendance'])->get(),
            'leavesToday' => $leavesToday,
            'pendingLeaves' => $pendingLeaves,
        ]);
    }
}
