<div id="leave-table">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
            <tr>
                <th class="px-6 py-3">Karyawan</th>
                <th class="px-6 py-3">Nama Izin</th>
                <th class="px-6 py-3">Alasan</th>
                <th class="px-6 py-3">Deskripsi</th>
                <th class="px-6 py-3">Tanggal Mulai</th>
                <th class="px-6 py-3">Tanggal Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaves as $leave)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">{{ $leave->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $leave->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $leave->reason }}</td>
                    <td class="px-6 py-4">{{ $leave->description }}</td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d F Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                        Tidak ada data tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 pagination">
        {{ $leaves->links() }}
    </div>
</div>
