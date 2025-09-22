<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\ScheduleStoreVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $schedules = Schedule::with(['sales', 'storeVisits.store', 'storeVisits.attendance'])
            ->when($user->hasRole('sales'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        $attendanceEvents = $schedules->flatMap(function ($schedule) {
            return $schedule->storeVisits->map(function ($visit) use ($schedule) {
                $storeName = $visit->store->name;
                $salesName = $schedule->sales->name;

                $jadwal = trim(
                    \Carbon\Carbon::parse($visit->checkin_time)->format('H:i') . ' - ' .
                        \Carbon\Carbon::parse($visit->checkout_time)->format('H:i')
                );

                $real = 'Belum hadir';
                if ($visit->attendance) {
                    $in = $visit->attendance->check_in_time
                        ? Carbon::parse($visit->attendance->check_in_time)->format('H:i')
                        : null;
                    $out = $visit->attendance->check_out_time
                        ? Carbon::parse($visit->attendance->check_out_time)->format('H:i')
                        : null;

                    if ($in && $out) {
                        $real = "$in - $out";
                    } elseif ($in) {
                        $real = $in;
                    }
                }

                $tolerance = $schedule->time_tolerance ?? 0;
                $checkin = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $visit->checkin_time,
                    config('app.timezone')
                );
                $absenUntil = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $schedule->visit_date . ' ' . $checkin->format('H:i:s'),
                    config('app.timezone')
                )->addMinutes($tolerance);

                $canAbsen = now()->endOfDay();
                // $isMangkir = !$visit->attendance && now()->greaterThan($absenUntil);

                return [
                    'title' => $storeName,
                    'start' => \Carbon\Carbon::parse($schedule->visit_date)->toDateString(),
                    'allDay' => true,
                    'extendedProps' => [
                        'sales' => $salesName,
                        'jadwal' => $jadwal,
                        'real' => $real,
                        'id_visit' => $visit->id,
                        'attendance' => $visit->attendance,
                        'can_absen' => $canAbsen,
                        // 'mangkir' => $isMangkir,
                    ],
                ];
                // return [
                //     'checkin' => \Carbon\Carbon::parse($visit->checkin_time),
                //     'tolerance' => $tolerance,
                //     'schedule' => $schedule,
                //     'absen_until' => $absenUntil,
                //     'can_absen' => $canAbsen,
                //     'now' => now(),
                // ];
            });
        })->values();

        // dd($attendanceEvents[6]);
        return view('pages.attendances.index', compact('schedules', 'attendanceEvents'));
    }



    public function createPresence($visit)
    {
        $userId = auth()->id();

        $storeVisit = ScheduleStoreVisit::whereHas('schedule', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['store', 'schedule'])->where('id', $visit)->first();

        if (!$storeVisit) {
            return back()->with('error', 'Tidak ada jadwal kunjungan untuk hari ini.');
        }

        // Waktu saat ini
        $now = now();

        $scheduleStartTime = $storeVisit->checkin_time ? Carbon::parse($storeVisit->checkin_time) : null;

        $hasAttendanceIn = Attendance::where('schedule_store_visit_id', $storeVisit->id)
                        ->whereNotNull('check_in_time')
                        ->exists();

        $hasAttendanceOut = Attendance::where('schedule_store_visit_id', $storeVisit->id)
                        ->whereNotNull('check_out_time')
                        ->exists();
        $canAbsen = $now->greaterThanOrEqualTo($scheduleStartTime);
        return view('pages.attendances.create', compact('storeVisit', 'canAbsen', 'scheduleStartTime','hasAttendanceIn','hasAttendanceOut'));
    }

    public function store(Request $request)
    {
        try {
            //code...
            $request->validate([
                'image_masuk' => 'image|mimes:jpeg,png,jpg|max:2048',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'accuracy' => 'required|numeric',
                'type' => 'required|in:checkIn,checkOut',
                'storeVisitId' => 'required|exists:schedule_store_visits,id',
            ]);

    
            // Validasi akurasi
            if ($request->accuracy > 500) {
                return back()->withErrors(['latitude' => 'Akurasi GPS terlalu rendah (±' . round($request->accuracy) . 'm).']);
            }

        
            // Validasi lokasi toko
            $storeVisit = ScheduleStoreVisit::find($request->storeVisitId);
            $distance = $this->calculateDistance(
                $storeVisit->store->latitude,
                $storeVisit->store->longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > 500) {
                return back()->withErrors(['latitude' => 'Anda berada di luar radius toko (' . round($distance) . 'm).']);
            }

            // Catat absensi
            $attendance = Attendance::create([
                'schedule_store_visit_id' => $storeVisit->id,
                'attended_at' => now(),
                'note' => $request->note,
                'actual_invoice_amount' => $request->type === 'checkIn' ? null : $request->nominal_invoice,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'device_info' => $request->userAgent(),
                'check_' . ($request->type === 'checkIn' ? 'in' : 'out') . '_ip' => $request->ip(),
                'check_' . ($request->type === 'checkIn' ? 'in' : 'out') . '_time' => now()->format('H:i:s'),
            ]);

            if ($request->hasFile('image_masuk')) {
                $collection = $request->type === 'checkIn' ? 'checkins' : 'checkouts';

                $attendance
                    ->addMediaFromRequest('image_masuk')
                    ->withCustomProperties([
                        'type' => $request->type,
                        'accuracy' => $request->accuracy,
                    ])
                    ->toMediaCollection($collection);
            }



            return redirect()->route('attendances.index')
                ->with('success', 'Absensi berhasil dicatat.');
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }


    public function submitCheckOut (Request $request)
    {
     
        // dd($request->all());
        try {
            //code...
             $request->validate([
                'image_pulang' => 'image|mimes:jpeg,png,jpg|max:2048',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'accuracy' => 'required|numeric',
                'type' => 'required|in:checkIn,checkOut',
                'storeVisitId' => 'required|exists:schedule_store_visits,id',
            ]);

            // Validasi akurasi
            if ($request->accuracy > 500) {
                return back()->withErrors(['latitude' => 'Akurasi GPS terlalu rendah (±' . round($request->accuracy) . 'm).']);
            }

            // Validasi lokasi toko
            $storeVisit = ScheduleStoreVisit::find($request->storeVisitId);
            $distance = $this->calculateDistance(
                $storeVisit->store->latitude,
                $storeVisit->store->longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > 100) {
                return back()->withErrors(['latitude' => 'Anda berada di luar radius toko (' . round($distance) . 'm).']);
            }

            // Catat absensi
        $attendance = Attendance::where('schedule_store_visit_id', $request->storeVisitId)->first();

        $attendance->update([
            'actual_invoice_amount' => $request->type === 'checkIn' ? null : $request->nominal_invoice,
            'note' => $request->note,
            'check_out_time' => now()->format('H:i:s'),
        ]);

            if ($request->hasFile('bukti_invoice')) {
                $collection =  'bukti_invoice';

                $attendance
                    ->addMediaFromRequest('bukti_invoice')
                    ->withCustomProperties([
                        'type' => $request->type,
                        'accuracy' => $request->accuracy,
                    ])
                    ->toMediaCollection($collection);
            }
            if ($request->hasFile('image_pulang')) {
                $collection =  'checkouts';

                $attendance
                    ->addMediaFromRequest('image_pulang')
                    ->withCustomProperties([
                        'type' => $request->type,
                        'accuracy' => $request->accuracy,
                    ])
                    ->toMediaCollection($collection);
            }



            return redirect()->route('attendances.index')
                ->with('success', 'Absensi berhasil dicatat.');
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    private function canAbsen($user)
    {
        $todaySchedule = $user->schedules()
            ->whereDate('visit_date', today())
            ->first();

        if (!$todaySchedule) {
            return false;
        }

        $scheduleTime = Carbon::parse($todaySchedule->visit_time);
        return now()->greaterThanOrEqualTo($scheduleTime);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }


    public function calendar()
    {
        $attendances = Attendance::with(['sales', 'storeVisits.store'])->latest()->get();
        return view('attendances.calendar', compact('attendances'));
    }
}
