<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class AttendancesExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Attendance::with([
            'storeVisit.store',
            'storeVisit.schedule',
            'storeVisit.schedule.sales',
        ]);

        if ($search = $this->request->input('search')) {
            $query->whereHas(
                'storeVisit.schedule.sales',
                fn($q) =>
                $q->where('name', 'like', "%$search%")
            );
        }

        if ($start = $this->request->input('start_date')) {
            $query->whereDate('attended_at', '>=', $start);
        }

        if ($end = $this->request->input('end_date')) {
            $query->whereDate('attended_at', '<=', $end);
        }

        return view('exports.attendances', [
            'attendances' => $query->get()
        ]);
    }
}
