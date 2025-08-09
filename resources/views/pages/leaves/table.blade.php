    <div id="leave-table">

        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Nama Izin</th>
                    <th class="px-6 py-3">Alasan</th>
                    <th class="px-6 py-3">Deskripsi</th>
                    <th class="px-6 py-3">Status Deskripsi</th>
                    <th class="px-6 py-3">Tanggal Mulai</th>
                    <th class="px-6 py-3">Tanggal Akhir</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>

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
                            @if ($leave->approved_at)
                                <span class="text-green-600 dark:text-green-400">Disetujui</span>
                            @elseif($leave->rejected_at)
                                {{ $leave->rejection_reason ?? 'Ditolak' }}
                            @else
                                <span class="text-yellow-600 dark:text-yellow-400">Menunggu Persetujuan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($leave->approved_at)
                                <span class="text-green-600 dark:text-green-400">Disetujui</span>
                            @elseif ($leave->rejected_at)
                                <span class="text-red-600 dark:text-red-400">Ditolak</span>
                            @else
                                <span class="text-yellow-600 dark:text-yellow-400">Menunggu Persetujuan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center relative">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open"
                                    class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v.01M12 12v.01M12 18v.01" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute right-0 mt-2 w-32 z-50 bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-600 rounded-md shadow-lg">
                                    <a href="{{ route('leaves.edit', $leave->id) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Edit
                                    </a>
                                    <!-- APPROVE -->
                                    <form method="POST" action="{{ route('leaves.approve', $leave->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full text-left block px-4 py-2 text-sm text-green-600 hover:bg-gray-100 dark:text-green-400 dark:hover:bg-gray-700">
                                            Setujui
                                        </button>
                                    </form>

                                    <!-- REJECT -->
                                    <button type="button"
                                        class="w-full text-left block px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100 dark:text-yellow-400 dark:hover:bg-gray-700"
                                        onclick="openRejectModal({{ $leave->id }})">
                                        Tolak
                                    </button>

                                    <form method="POST" action="{{ route('leaves.destroy', $leave->id) }}"
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

    <!-- Modal Tolak -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-md w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Alasan Penolakan</h2>

            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <textarea name="reason" rows="3" required
                    class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Masukkan alasan penolakan..."></textarea>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 cursor-pointer bg-gray-300 dark:bg-gray-600 rounded-md text-sm"
                        onclick="closeRejectModal()">Batal</button>
                    <button type="submit" style="background-color: red ;color: white;"
                        class="px-4 py-2 bg-red-600  rounded-md text-sm hover:bg-red-700 cursor-pointer">Tolak</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(leaveId) {
            document.body.classList.add('overflow-hidden');

            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/leaves/reject/${leaveId}`;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            document.body.classList.remove('overflow-hidden');

            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
