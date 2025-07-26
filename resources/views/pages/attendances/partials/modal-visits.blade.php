@if ($schedule->storeVisits->isEmpty())
    <p class="text-gray-600 dark:text-gray-300">Tidak ada kunjungan toko pada jadwal ini.</p>
@else
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-300">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-700">
                <th class="px-4 py-2">Toko</th>
                <th class="px-4 py-2">Check-in</th>
                <th class="px-4 py-2">Check-out</th>
                <th class="px-4 py-2">Estimasi Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedule->storeVisits as $visit)
                <tr class="border-t dark:border-gray-600">
                    <td class="px-4 py-2">{{ $visit->store->name }}</td>
                    <td class="px-4 py-2">{{ $visit->checkin_time ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $visit->checkout_time ?? '-' }}</td>
                    <td class="px-4 py-2">Rp {{ number_format($visit->expected_invoice_amount ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
