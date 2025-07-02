<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
        <tr>
            <th class="px-4 py-3">Sales</th>
            <th class="px-4 py-3">Toko</th>
            <th class="px-4 py-3">Tanggal Absen</th>
            <th class="px-4 py-3">Jam Masuk</th>
            <th class="px-4 py-3">Jam Keluar</th>
            <th class="px-4 py-3">Tagihan</th>
            <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($attendances as $attendance)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <td class="px-4 py-3">{{ $attendance->sales->name ?? '-' }}</td>
                <td class="px-4 py-3">{{ $attendance->storeVisits->store->name ?? '-' }}</td>
                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($attendance->attended_at)->translatedFormat('d F Y') }}
                </td>
                <td class="px-4 py-3">{{ $attendance->check_in_time ?? '-' }}</td>
                <td class="px-4 py-3">{{ $attendance->check_out_time ?? '-' }}</td>
                <td class="px-4 py-3">Rp {{ number_format($attendance->actual_invoice_amount ?? 0, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" data-modal-target="attendanceModal-{{ $attendance->id }}"
                        data-modal-toggle="attendanceModal-{{ $attendance->id }}"
                        class="text-indigo-600 hover:underline">
                        Lihat Detail
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-3 text-center text-gray-500 dark:text-gray-300">Tidak ada data
                    absensi.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $attendances->links() }}
</div>

{{-- Modal Detail Absensi --}}
@foreach ($attendances as $attendance)
    <div id="attendanceModal-{{ $attendance->id }}" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-[90vw] h-auto">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Detail Absensi - {{ $attendance->sales->name ?? '-' }}
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto"
                        data-modal-hide="attendanceModal-{{ $attendance->id }}">
                        ✕
                    </button>
                </div>
                <div class="p-6 space-y-3 text-sm text-gray-700 dark:text-gray-200">
                    <p><strong>Toko:</strong> {{ $attendance->storeVisits->store->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong>
                        {{ \Carbon\Carbon::parse($attendance->attended_at)->translatedFormat('d F Y') }}</p>
                    <p><strong>Jam Masuk:</strong> {{ $attendance->check_in_time ?? '-' }}</p>
                    <p><strong>Jam Keluar:</strong> {{ $attendance->check_out_time ?? '-' }}</p>
                    <p><strong>Estimasi Tagihan:</strong> Rp
                        {{ number_format($attendance->storeVisits->expected_invoice_amount ?? 0, 0, ',', '.') }}</p>
                    <p><strong>Tagihan Real:</strong> Rp
                        {{ number_format($attendance->actual_invoice_amount ?? 0, 0, ',', '.') }}</p>
                    <p><strong>Lokasi:</strong> {{ $attendance->latitude ?? '-' }},
                        {{ $attendance->longitude ?? '-' }}</p>
                    <p><strong>Catatan:</strong> {{ $attendance->note ?? '-' }}</p>
                    @if (!empty($attendance->bukti_path))
                        <p><strong>Bukti Foto:</strong></p>
                        <img src="{{ asset('storage/' . $attendance->bukti_path) }}" alt="Bukti"
                            class="w-40 h-auto rounded shadow">
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
