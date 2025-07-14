<?php

namespace App\Exports;

use App\Models\Leave;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class LeavesExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Leave::with('user');

        if ($search = $this->request->input('search')) {
            $query->where("name", "like", "%$search%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        if ($startDate = $this->request->input('start_date')) {
            $query->whereDate('start_date', '>=', $startDate);
        }

        if ($endDate = $this->request->input('end_date')) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        return view('exports.leaves', [
            'leaves' => $query->get()
        ]);
    }
}
