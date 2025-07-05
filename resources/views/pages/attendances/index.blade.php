<x-layouts.app :title="__('Kalender Absensi Sales')">
    <x-ui.breadcrumb :items="[['label' => 'Kalender Absensi']]" />

    {{-- Kalender Card --}}
    <div class="mb-6 p-4 sm:p-6 bg-white dark:bg-gray-800 border rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Kalender Absensi Sales</h2>
        <div id="attendance-calendar" class="w-full overflow-x-auto"></div>
    </div>

    {{-- Global Detail Modal --}}
    <div id="global-detail-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-4 sm:p-6 relative overflow-y-auto max-h-[90vh]">
            <button onclick="document.getElementById('global-detail-modal').classList.add('hidden')"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">✕</button>
            <h3 class="text-lg font-semibold mb-4 dark:text-white" id="detail-title">Detail Absensi</h3>
            <dl id="detail-body" class="text-sm space-y-2 text-gray-800 dark:text-gray-200"></dl>
        </div>
    </div>

    @php
        $grouped = $schedules
            ->flatMap(fn($s) => $s->storeVisits)
            ->groupBy(
                fn($v) => optional($v->attendance)->attended_at
                    ? \Carbon\Carbon::parse($v->attendance->attended_at)->toDateString()
                    : \Carbon\Carbon::parse($v->schedule->visit_date)->toDateString(),
            );
    @endphp

    {{-- Per-Tanggal Modal --}}
    @foreach ($schedules->flatMap->storeVisits as $visit)
        <div id="modal-visit-{{ $visit->id }}"
            class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-4 sm:p-6 relative">
                <button onclick="this.closest('.fixed').classList.add('hidden')"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">✕</button>
                <h3 class="text-lg font-bold mb-4 dark:text-white">
                    Absensi: {{ \Carbon\Carbon::parse($visit->schedule->visit_date)->translatedFormat('d F Y') }}
                </h3>
                <ul class="space-y-4 text-sm text-gray-800 dark:text-gray-200">
                    <li class="border-b pb-4">
                        <div class="space-y-1 w-full">
                            <div class="font-medium text-blue-600 dark:text-blue-300 truncate w-full">
                                {{ $visit->schedule->sales->name ?? '-' }} - {{ $visit->store->name ?? '-' }}
                            </div>
                            <div class="text-gray-500 dark:text-gray-400 text-xs">
                                Jadwal:
                                {{ \Carbon\Carbon::parse($visit->checkin_time)->format('H:i') ?? '-' }}
                                -
                                {{ \Carbon\Carbon::parse($visit->checkout_time)->format('H:i') ?? '-' }}
                            </div>
                            @if ($visit->attendance)
                                <div class="text-green-600 text-xs">
                                    ✔️ Hadir:
                                    {{ \Carbon\Carbon::parse($visit->attendance->check_in_time)->format('H:i') ?? '-' }}
                                    -
                                    {{ \Carbon\Carbon::parse($visit->attendance->check_out_time)->format('H:i') ?? '-' }}
                                </div>
                                <div class="text-xs">
                                    Tagihan Realita: <strong>Rp
                                        {{ number_format($visit->attendance->actual_invoice_amount ?? 0, 0, ',', '.') }}</strong>
                                </div>
                            @else
                                <div class="text-red-500 text-xs">❌ Belum absen</div>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    @endforeach


    <script>
        window.attendanceEvents = @json($attendanceEvents);
    </script>

    @vite('resources/js/calendar.js')
</x-layouts.app>
