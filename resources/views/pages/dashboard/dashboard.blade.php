<x-layouts.app :title="__('Dashboard')">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Jumlah Toko</h3>
            <p class="text-3xl font-bold">{{ $totalStores }}</p>
        </div>
        <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Jadwal Hari Ini</h3>
            <p class="text-3xl font-bold">{{ $todaySchedulesCount }}</p>
        </div>
        <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Izin Hari Ini</h3>
            <p class="text-3xl font-bold">{{ $activeLeavesToday }}</p>
        </div>
        <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kunjungan Hari Ini</h3>
            <p class="text-3xl font-bold">{{ $attendancesToday }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Perbandingan Invoice Hari Ini</h3>
            <canvas id="invoiceChart" class="mt-4"></canvas>
        </div>

        <div class="rounded-xl border bg-white p-4 shadow dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Izin Hari Ini</h3>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700 mt-2">
                @forelse ($leavesToday as $leave)
                    <li class="py-2">
                        <strong>{{ $leave->user->name ?? '-' }}</strong>: {{ $leave->name ?? '-' }}<br>
                        <small class="text-gray-500">{{ $leave->start_date->format('d M') }} -
                            {{ $leave->end_date->format('d M') }}</small>
                    </li>
                @empty
                    <li class="py-2 text-gray-500">Tidak ada data izin hari ini</li>
                @endforelse
            </ul>
        </div>
    </div>

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
                    @foreach ($visitsToday as $schedule)
                        @foreach ($schedule->storeVisits as $visit)
                            <tr>
                                <td>{{ $schedule->user->name ?? '-' }}</td>
                                <td>{{ $visit->store->name ?? '-' }}</td>
                                <td>{{ optional($visit->checkin_time)->format('H:i') }} -
                                    {{ optional($visit->checkout_time)->format('H:i') }}</td>
                                <td>{{ optional($visit->attendance?->check_in_time)->format('H:i') ?? '-' }}</td>
                                <td>{{ optional($visit->attendance?->check_out_time)->format('H:i') ?? '-' }}</td>
                                <td>Rp{{ number_format($visit->expected_invoice_amount, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($visit->attendance?->actual_invoice_amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

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
