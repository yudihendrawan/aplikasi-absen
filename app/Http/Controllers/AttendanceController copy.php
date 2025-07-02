<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ScheduleStoreVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // $user = auth()->user(); // Ambil user yang sedang login

        // $query = Attendance::with([
        //     'storeVisit.store',
        //     'storeVisit.schedule.sales',
        //     'storeVisit.schedule.creator',
        // ]);

        // // Jika user adalah sales, hanya tampilkan absensinya sendiri
        // if ($user->hasRole('sales')) {
        //     $query->whereHas('storeVisit.schedule', function ($q) use ($user) {
        //         $q->where('user_id', $user->id);
        //     });
        // }

        // // Filter nama sales (khusus admin)
        // if ($search = $request->input('search')) {
        //     $query->whereHas('storeVisit.schedule.sales', function ($q) use ($search) {
        //         $q->where('name', 'like', "%$search%");
        //     });
        // }

        // if ($startDate = $request->input('start_date')) {
        //     $query->whereHas('storeVisit.schedule', function ($q) use ($startDate) {
        //         $q->whereDate('visit_date', '>=', $startDate);
        //     });
        // }

        // if ($endDate = $request->input('end_date')) {
        //     $query->whereHas('storeVisit.schedule', function ($q) use ($endDate) {
        //         $q->whereDate('visit_date', '<=', $endDate);
        //     });
        // }

        // $sortableColumns = ['attended_at', 'check_in_time', 'check_out_time'];
        // $sortBy = $request->input('sort_by');
        // $sortDir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        // if ($sortBy && in_array($sortBy, $sortableColumns)) {
        //     $query->orderBy($sortBy, $sortDir);
        // } else {
        //     $query->latest('attended_at');
        // }

        // $attendances = $query->paginate(10)->withQueryString();

        // if ($request->ajax()) {
        //     return view('pages.attendances.table', compact('attendances'))->render();
        // }

        $attendances = Attendance::with('sales', 'storeVisits.store')->get();

        $attendanceEvents = $attendances->map(function ($a) {
            return [
                'title' => $a->sales->name . ' - ' . $a->storeVisits->store->name,
                'start' => \Carbon\Carbon::parse($a->attended_at)->toDateString(),
                'allDay' => true,
            ];
        })->values();


        return view('pages.attendances.index', compact('attendances', 'attendanceEvents'));
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
