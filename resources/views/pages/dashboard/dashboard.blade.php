<x-layouts.app :title="__('Dashboard')">
    @role('admin')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @endrole
            @role('admin')
                <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Jumlah Toko</h3>
                    <p class="text-3xl font-bold">{{ $totalStores ?? '-' }}</p>
                </div>
            @endrole
            <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Jadwal Hari Ini</h3>
                <p class="text-3xl font-bold">{{ $todaySchedulesCount ?? '-' }}</p>
            </div>
            <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Izin Hari Ini</h3>
                <p class="text-3xl font-bold">{{ $activeLeavesToday ?? '-' }}</p>
            </div>
            <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kunjungan Hari Ini</h3>
                <p class="text-3xl font-bold">{{ $attendancesToday ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Perbandingan Invoice Hari Ini</h3>
                @if (($invoicesToday->expected ?? 0) == 0 && ($invoicesToday->actual ?? 0) == 0)
                    <p class="text-gray-500 mt-2">Tidak ada data invoice hari ini.</p>
                @else
                    <canvas id="invoiceChart" class="mt-4"></canvas>
                @endif

            </div>

            <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Izin Hari Ini</h3>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700 mt-2">
                    @forelse ($leavesToday as $leave)
                        <li class="py-2">
                            <strong>{{ $leave->user->name ?? '-' }}</strong>: {{ $leave->name ?? '-' }}<br>
                            <small class="text-gray-500">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} -
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('d M') }}
                            </small>

                        </li>
                    @empty
                        <li class="py-2 text-gray-500">Tidak ada data izin hari ini</li>
                    @endforelse
                </ul>
            </div>
        </div>
        @role('admin')
            <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800 mt-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Izin Menunggu Persetujuan
                </h3>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700 mt-2">
                    @forelse ($pendingLeaves as $leave)
                        <li class="py-2 flex items-center justify-between">
                            <div>
                                <strong>{{ $leave->user->name ?? '-' }}</strong>:
                                {{ $leave->name ?? '-' }}<br>
                                <small class="text-gray-500">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} -
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d M') }}
                                </small>
                            </div>
                            <div class="flex gap-2">
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

                            </div>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500">Tidak ada izin yang menunggu persetujuan</li>
                    @endforelse
                </ul>
            </div>
        @endrole


        <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Kunjungan Sales Hari Ini</h3>
            <div class="overflow-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr>
                            <th>Sales</th>
                            <th>Toko</th>
                            <th>Jadwal</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Est. Invoice</th>
                            <th>Real. Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visitsToday as $schedule)
                            @if ($schedule->storeVisits->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center text-gray-500 py-2">Tidak ada kunjungan toko
                                        untuk
                                        {{ $schedule->user->name ?? '-' }}</td>
                                </tr>
                            @else
                                @foreach ($schedule->storeVisits as $visit)
                                    <tr>
                                        <td>{{ $schedule->sales->name ?? '-' }}</td>
                                        <td>{{ $visit->store->name ?? '-' }}</td>
                                        <td>{{ optional($visit->checkin_time)->format('H:i') }} -
                                            {{ optional($visit->checkout_time)->format('H:i') }}</td>
                                        <td>{{ optional($visit->attendance?->check_in_time)->format('H:i') ?? '-' }}
                                        </td>
                                        <td>{{ optional($visit->attendance?->check_out_time)->format('H:i') ?? '-' }}
                                        </td>
                                        <td>Rp{{ number_format($visit->expected_invoice_amount, 0, ',', '.') }}</td>
                                        <td>Rp{{ number_format($visit->attendance?->actual_invoice_amount ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-gray-500 py-2">Tidak ada kunjungan hari ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>


        @role('admin')
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
        @endrole

        {{-- ChartJS --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('invoiceChart').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Invoice'],
                        datasets: [{
                                label: 'Estimasi',
                                data: [{{ $invoicesToday->expected ?? 0 }}],
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            },
                            {
                                label: 'Realisasi',
                                data: [{{ $invoicesToday->actual ?? 0 }}],
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                            },
                        },
                    },
                });
            });
        </script>
</x-layouts.app>
