<table>
    <thead>
        <tr>
            <th>Nama Sales</th>
            <th>Tanggal Kunjungan</th>
            <th>Catatan</th>
            <th>Toko yang Dikunjungi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schedules as $schedule)
            <tr>
                <td>{{ $schedule->sales->name }}</td>
                <td>{{ $schedule->visit_date }}</td>
                <td>{{ $schedule->notes }}</td>
                {{-- <td>
                    <ul>
                        @foreach ($schedule->storeVisits as $visit)
                            <li>{{ $visit->store->name }}</li>
                        @endforeach
                    </ul>
                </td> --}}
                <td>
                    {{ $schedule->storeVisits->pluck('store.name')->join(', ') }}
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
