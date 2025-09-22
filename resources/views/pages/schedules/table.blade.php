<div id="schedule-table" class="overflow-visible">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
            <tr>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Toko</th>
                <th class="px-6 py-3">Tanggal</th>
                <th class="px-6 py-3">Toleransi Keterlambatan</th>
                <th class="px-6 py-3">Dibuat oleh</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <?php $totalStoreVisits = $schedule->storeVisits->count(); ?>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">{{ $schedule->sales->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $totalStoreVisits . ' kunjungan' ?? '-' }}</td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($schedule->visit_date)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4">{{ $schedule->time_tolerance }} menit</td>
                    <td class="px-6 py-4">{{ $schedule->creator->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-center relative">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" class="text-black focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v.01M12 12v.01M12 18v.01" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-32 z-50 bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-600 rounded-md shadow-lg">
                                <a href="{{ route('schedules.edit', $schedule->id) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Edit
                                </a>
                                <button type="button" data-modal-target="scheduleModal-{{ $schedule->id }}"
                                    data-modal-toggle="scheduleModal-{{ $schedule->id }}"
                                    class="block px-4 text-left w-full py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Lihat Detail
                                </button>

                                <form method="POST" action="{{ route('schedules.destroy', $schedule->id) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                        Tidak ada jadwal tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 pagination">
        {{ $schedules->links() }}
    </div>

    <!-- Modal -->
    {{-- <div id="scheduleModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="closeScheduleModal()"
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                &times;
            </button>
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Detail Kunjungan Toko</h2>
            <div id="scheduleModalContent">
                <p class="text-gray-600 dark:text-gray-300">Memuat data...</p>
            </div>
        </div>
    </div> --}}
    @foreach ($schedules as $schedule)
        <div id="scheduleModal-{{ $schedule->id }}" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-[90vw] h-[90vh]">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Detail Jadwal - {{ $schedule->sales->name }}
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                            data-modal-hide="scheduleModal-{{ $schedule->id }}">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 011.414
                            1.414L11.414 10l4.293 4.293a1 1 0 01-1.414
                            1.414L10 11.414l-4.293 4.293a1 1 0
                            01-1.414-1.414L8.586 10 4.293
                            5.707a1 1 0 010-1.414z" clip-rule="evenodd">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        @if ($schedule->storeVisits->isEmpty())
                            <p class="text-gray-500 dark:text-gray-300">Tidak ada kunjungan toko.</p>
                        @else
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-300">
                                <thead class="bg-gray-100 dark:bg-gray-700 text-nowrap">
                                    <tr>
                                        <th class="px-4 py-2">Toko</th>
                                        <th class="px-4 py-2">Jadwal Check-in</th>
                                        <th class="px-4 py-2">Jadwal Check-out</th>
                                        <th class="px-4 py-2">Absen Check-in </th>
                                        <th class="px-4 py-2">Absen Check-out</th>
                                        <th class="px-4 py-2">Estimasi Tagihan</th>
                                        <th class="px-4 py-2">Tagihan yang Terbayar</th>
                                        <th class="px-4 py-2">Catatan</th>
                                        <th class="px-4 py-2">Bukti</th> {{-- tambahin --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedule->storeVisits as $visit)
                                        <tr class="border-b dark:border-gray-600">
                                            <td class="px-4 py-2">{{ $visit->store->name }}</td>
                                            <td class="px-4 py-2">{{ $visit->checkin_time ?? '-' }}</td>
                                            <td class="px-4 py-2">{{ $visit->checkout_time ?? '-' }}</td>
                                            <td class="px-4 py-2">{{ $visit->attendance->check_in_time ?? '-' }}</td>
                                            <td class="px-4 py-2">{{ $visit->attendance->check_out_time ?? '-' }}</td>
                                            <td class="px-4 py-2">Rp
                                                {{ number_format($visit->expected_invoice_amount ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2">Rp
                                                {{ number_format($visit->attendance->actual_invoice_amount ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2">{{ $visit->attendance->note ?? '-' }}</td>
                                            <td class="px-4 py-2 space-x-2">
                                                @if ($visit->attendance)
                                                    @foreach ($visit->attendance->getMedia('checkins') as $media)
                                                        <a href="{{ $media->getUrl() }}" target="_blank"
                                                            class="text-blue-500 underline">Check-in</a>
                                                    @endforeach

                                                    @foreach ($visit->attendance->getMedia('checkouts') as $media)
                                                        <a href="{{ $media->getUrl() }}" target="_blank"
                                                            class="text-green-500 underline">Check-out</a>
                                                    @endforeach

                                                    @foreach ($visit->attendance->getMedia('bukti_invoice') as $media)
                                                        <a href="{{ $media->getUrl() }}" target="_blank"
                                                            class="text-purple-500 underline">Invoice</a>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach



</div>

<script>
    function showScheduleModal(scheduleId) {
        const modal = document.getElementById('scheduleModal');
        const content = document.getElementById('scheduleModalContent');

        modal.classList.remove('hidden');
        content.innerHTML = `<p class="text-gray-600 dark:text-gray-300">Memuat data...</p>`;

        fetch(`/schedules/${scheduleId}/visits`)
            .then(res => res.text())
            .then(html => {
                content.innerHTML = html;
            });
    }

    function closeScheduleModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
    }
</script>
