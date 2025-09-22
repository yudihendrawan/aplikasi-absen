<table>
    <thead>
        <tr>
            <th>Sales</th>
            <th>Toko</th>
            <th>Tanggal</th>
            <th>Jadwal Check-in</th>
            <th>Jadwal Check-in</th>
            <th>Absen Check-in</th>
            <th>Absen Check-out</th>
            <th>Tagihan Estimasi</th>
            <th>Tagihan Realita</th>
            <th>Bukti</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schedules as $schedule)
            @foreach ($schedule->storeVisits as $visit)
                <tr>
                    <td>{{ $schedule->sales->name ?? '-' }}</td>
                    <td>{{ $visit->store->name ?? '-' }}</td>
                    <td>{{ $schedule->visit_date }}</td>
                    <td>{{ $visit->checkin_time?->format('H:i') ?? '-' }}</td>
                    <td>{{ $visit->checkout_time?->format('H:i') ?? '-' }}</td>

                    <td>{{ $visit->attendance->check_in_time ?? '-' }}</td>
                    <td>{{ $visit->attendance->check_out_time ?? '-' }}</td>
                    <td>{{ $visit->expected_invoice_amount }}</td>
                    <td>{{ $visit->attendance->actual_invoice_amount ?? '-' }}</td>
                    <td>
                        @if ($visit->attendance)
                            {{-- Link semua media --}}
                            @foreach ($visit->attendance->getMedia('checkins') as $media)
                                <a href="{{ $media->getUrl() }}">Check-in</a>
                            @endforeach

                            @foreach ($visit->attendance->getMedia('checkouts') as $media)
                                <a href="{{ $media->getUrl() }}">Check-out</a>
                            @endforeach

                            @foreach ($visit->attendance->getMedia('bukti_invoice') as $media)
                                <a href="{{ $media->getUrl() }}">Invoice</a>
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
