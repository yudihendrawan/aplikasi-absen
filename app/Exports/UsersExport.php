<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class UsersExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $users = User::query()
            ->withCount(['leaves', 'invoices'])
            ->with(['schedules.storeVisits.attendances']) // eager load nested
            ->when($this->request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->get()
            ->map(function ($user) {
                $attendanceCount = 0;

                foreach ($user->schedules as $schedule) {
                    foreach ($schedule->storeVisits as $visit) {
                        $attendanceCount += $visit->attendances->count();
                    }
                }

                $user->attendances_count = $attendanceCount;
                return $user;
            });

        return view('exports.users', compact('users'));
    }
}
