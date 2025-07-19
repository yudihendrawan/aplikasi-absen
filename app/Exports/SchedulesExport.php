<?php

namespace App\Exports;

use App\Models\Schedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class SchedulesExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Schedule::with([
            'sales',
            'storeVisits.store',
        ]);

        if ($search = $this->request->input('search')) {
            $query->whereHas(
                'sales',
                fn($q) =>
                $q->where('name', 'like', "%$search%")
            );
        }

        if ($start = $this->request->input('start_date')) {
            $query->whereDate('visit_date', '>=', $start);
        }

        if ($end = $this->request->input('end_date')) {
            $query->whereDate('visit_date', '<=', $end);
        }

        return view('exports.schedules', [
            'schedules' => $query->get()
        ]);
    }
}
