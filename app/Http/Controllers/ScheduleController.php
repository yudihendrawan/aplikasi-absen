<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Schedule;
use App\Models\ScheduleStoreVisit;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        // $this->middleware('role:admin')->only(['index', 'show']);
        $this->middleware('role:admin');
        // $this->middleware('role:manager')->only(['create', 'store', 'edit', 'update']);

        // $this->middleware('role:admin')->only(['destroy']);
    }
    public function index(Request $request)
    {
        $query = Schedule::with(['sales',  'storeVisits', 'creator']);

        if ($search = $request->input('search')) {
            $query->whereHas('sales', fn($q) => $q->where('name', 'like', "%$search%"))
                ->orWhereHas('store', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('visit_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('visit_date', '<=', $endDate);
        }

        $sortableColumns = ['visit_date', 'time_tolerance'];
        $sortBy = $request->input('sort_by');
        $sortDir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        if ($sortBy && in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest('visit_date');
        }

        $schedules = $query->paginate(10)->withQueryString();
        if ($request->ajax()) {
            return view('pages.schedules.table', compact('schedules'))->render();
        }

        return view('pages.schedules.index', compact('schedules'));
    }

    public function showVisits(Schedule $schedule)
    {
        $schedule->load(['storeVisits.store']);

        return view('pages.schedules.partials.modal-visits', compact('schedule'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sales = User::role(['sales'])->get();
        $stores = Store::all();
        return view('pages.schedules.create', compact('stores', 'sales'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'stores' => 'required|array|min:1',
            'time_tolerance' => 'required|integer|min:0',
            'stores.*.store_id' => 'required|exists:stores,id',
            'stores.*.expected_invoice_amount' => 'nullable|numeric|min:0',
            'stores.*.checkin_time' => 'required|date_format:H:i',
            'stores.*.checkout_time' => 'required|date_format:H:i|after_or_equal:stores.*.checkin_time',
        ]);

        $duplicateSchedule = Schedule::where('user_id', $request->user_id)
            ->whereNotNull('approved_at')
            ->whereDate('visit_date', $request->visit_date)
            ->exists();

        if ($duplicateSchedule) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sales sudah memiliki jadwal kunjungan di tanggal tersebut.');
        }


        $leaveExists = Leave::where('user_id', $request->user_id)
            ->whereDate('start_date', '<=', $request->visit_date)
            ->whereDate('end_date', '>=', $request->visit_date)
            ->exists();


        if ($leaveExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sales sudah mengajukan cuti pada tanggal tersebut.');
        }
        DB::beginTransaction();
        try {
            // Simpan jadwal utama
            $schedule = Schedule::create([
                'user_id' => $request->user_id,
                'visit_date' => $request->visit_date,
                'time_tolerance' => $request->time_tolerance,
                'created_by' => auth()->user()->id
            ]);

            // Simpan daftar kunjungan toko
            foreach ($request->stores as $store) {
                $checkin = $request->visit_date . ' ' . $store['checkin_time'] . ':00';
                $checkout = $request->visit_date . ' ' . $store['checkout_time'] . ':00';
                ScheduleStoreVisit::create([
                    'schedule_id' => $schedule->id,
                    'store_id' => $store['store_id'],
                    'checkin_time' => $checkin,
                    'checkout_time' => $checkout,
                    'expected_invoice_amount' => $store['expected_invoice_amount'] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (app()->environment('local')) {
                dd($th->getMessage());
            }
            return redirect()->back()->with('error', 'terjadi kesalahan.');
        }
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
        $schedule = Schedule::with(['sales', 'storeVisits.store'])->findOrFail($id);
        $sales = User::role(['sales'])->get();
        $stores = Store::all();


        return view('pages.schedules.edit', compact('schedule', 'sales', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'stores' => 'required|array|min:1',
            'time_tolerance' => 'required|integer|min:0',
            'stores.*.store_id' => 'required|exists:stores,id',
            'stores.*.expected_invoice_amount' => 'nullable|numeric|min:0',
            'stores.*.checkin_time' => 'required|date_format:H:i',
            'stores.*.checkout_time' => 'required|date_format:H:i|after_or_equal:stores.*.checkin_time',
        ]);

        $schedule = Schedule::findOrFail($id);

        // Cek duplikat jadwal (kecuali schedule ini sendiri)
        $duplicate = Schedule::where('user_id', $request->user_id)
            ->whereDate('visit_date', $request->visit_date)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sales sudah memiliki jadwal kunjungan di tanggal tersebut.');
        }

        // Cek cuti
        $leaveExists = Leave::where('user_id', $request->user_id)
            ->whereDate('start_date', '<=', $request->visit_date)
            ->whereDate('end_date', '>=', $request->visit_date)
            ->exists();

        if ($leaveExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sales sudah mengajukan cuti pada tanggal tersebut.');
        }

        DB::beginTransaction();
        try {
            // Update field utama
            $schedule->update([
                'user_id' => $request->user_id,
                'visit_date' => $request->visit_date,
                'time_tolerance' => $request->time_tolerance,
                'updated_by' => auth()->user()->id,
            ]);

            // Hapus kunjungan lama
            $schedule->storeVisits()->delete();

            // Buat kunjungan baru
            foreach ($request->stores as $store) {
                $checkin  = $request->visit_date . ' ' . $store['checkin_time'] . ':00';
                $checkout = $request->visit_date . ' ' . $store['checkout_time'] . ':00';

                ScheduleStoreVisit::create([
                    'schedule_id'             => $schedule->id,
                    'store_id'                => $store['store_id'],
                    'checkin_time'            => $checkin,
                    'checkout_time'           => $checkout,
                    'expected_invoice_amount' => $store['expected_invoice_amount'] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'Jadwal berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (app()->environment('local')) {
                dd($th->getMessage());
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui jadwal.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $schedule)
    {
        $schedule = Schedule::find($schedule)->delete();

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil di hapus.');
    }
}
