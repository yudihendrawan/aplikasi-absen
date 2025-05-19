<div id="schedule-table">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
            <tr>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Toko</th>
                <th class="px-6 py-3">Tanggal</th>
                <th class="px-6 py-3">Jam Masuk</th>
                <th class="px-6 py-3">Jam Keluar</th>
                <th class="px-6 py-3">Toleransi</th>
                <th class="px-6 py-3">Dibuat oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">{{ $schedule->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $schedule->store->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4">{{ $schedule->check_in }}</td>
                    <td class="px-6 py-4">{{ $schedule->check_out }}</td>
                    <td class="px-6 py-4">{{ $schedule->time_tolerance }} menit</td>
                    <td class="px-6 py-4">{{ $schedule->creator->name ?? '-' }}</td>
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
</div>
