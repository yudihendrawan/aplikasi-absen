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

                $real = $visit->attendance
                    ? trim(
                        \Carbon\Carbon::parse($visit->attendance->check_in_time)->format('H:i') . ' - ' .
                            \Carbon\Carbon::parse($visit->attendance->check_out_time)->format('H:i')
                    )
                    : 'Belum hadir';

                return [
                    'title' => $storeName,
                    'start' => \Carbon\Carbon::parse($schedule->visit_date)->toDateString(),
                    'allDay' => true,
                    'extendedProps' => [
                        'sales' => $salesName,
                        'jadwal' => $jadwal,
                        'real' => $real,
                    ],
                    'backgroundColor' => $visit->attendance ? '#22c55e' : '#f59e0b',
                ];
            });
        })->values();


        return view('pages.attendances.index', compact('schedules', 'attendanceEvents'));
    }



    public function create()
    {
        $userId = auth()->id();

        $storeVisit = ScheduleStoreVisit::whereHas('schedule', function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->whereDate('visit_date', '>=', now()->toDateString());
        })->with(['store', 'schedule'])->first();

        // dd($storeVisit);
        if (!$storeVisit) {
            return back()->with('error', 'Tidak ada jadwal kunjungan untuk hari ini.');
        }

        // Waktu saat ini
        $now = now();
        $scheduleStartTime = Carbon::parse($storeVisit->checkin_time); // field checkin_time dari schedule_store_visits

        $canAbsen = $now->greaterThanOrEqualTo($scheduleStartTime);

        return view('pages.attendances.create', compact('storeVisit', 'canAbsen', 'scheduleStartTime'));
    }

    public function store(Request $request)
    {
        // Validasi input
        // $validated = $request->validate([
        //     'note' => 'nullable|string|max:500',
        //     'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        //     'latitude' => 'required|numeric',
        //     'longitude' => 'required|numeric',
        //     'location_hash' => 'required|string',
        //     'accuracy' => 'required|numeric|max:100'
        // ]);

        // Validasi hash lokasi
        $salt = config('app.key');
        $timestamp = floor(time() / (60)); // Timestamp per menit
        $expectedHash = base64_encode(
            $request->latitude . ':' .
                $request->longitude . ':' .
                $salt . ':' .
                $timestamp
        );

        dd($request->all());
        dd(hash_equals($request->location_hash_masuk, $expectedHash));

        if (!hash_equals($request->location_hash, $expectedHash)) {
            Log::warning('Invalid location hash detected', [
                'ip' => $request->ip(),
                'user' => $request->user()->id,
                'expected' => $expectedHash,
                'received' => $request->location_hash
            ]);
            return back()->withErrors(['latitude' => 'Data lokasi tidak valid.'])->withInput();
        }

        // Validasi akurasi
        if ($request->accuracy > 50) {
            return back()->withErrors(['latitude' => 'Akurasi GPS terlalu rendah (±' . round($request->accuracy) . 'm).'])->withInput();
        }

        // Validasi waktu absen
        if (!$this->canAbsen($request->user())) {
            return back()->withErrors(['time' => 'Belum waktunya absen.'])->withInput();
        }

        // Validasi lokasi toko
        $storeVisit = $request->user()->storeVisits()->latest()->firstOrFail();
        $distance = $this->calculateDistance(
            $storeVisit->store->latitude,
            $storeVisit->store->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > 100) {
            return back()->withErrors(['latitude' => 'Anda berada di luar radius toko (' . round($distance) . 'm).'])->withInput();
        }

        // Simpan bukti foto
        $buktiPath = $request->file('bukti')->store('absensi', 'public');

        // Catat absensi
        Attendance::create([
            'schedule_store_visit_id' => $storeVisit->id,
            'attended_at' => now(),
            'note' => $request->note,
            'actual_invoice_amount' => null,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'device_info' => $request->userAgent(),
            'check_in_ip' => $request->ip(),
            'check_in_time' => now()->format('H:i:s'),
            'bukti_path' => $buktiPath,
        ]);

        return redirect()->route('attendances.index')
            ->with('success', 'Absensi berhasil dicatat.');
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
