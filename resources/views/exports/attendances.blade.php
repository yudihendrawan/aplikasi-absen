<table>
    <thead>
        <tr>
            <th>Sales</th>
            <th>Toko</th>
            <th>Tanggal Kunjungan</th>
            <th>Waktu Hadir</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Tagihan Realita</th>
            <th>Catatan</th>
            <th>Lokasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attendances as $item)
            <tr>
                <td>{{ $item->storeVisit->schedule->sales->name ?? '-' }}</td>
                <td>{{ $item->storeVisit->store->name ?? '-' }}</td>
                <td>{{ $item->storeVisit->schedule->visit_date ?? '-' }}</td>
                <td>{{ $item->attended_at }}</td>
                <td>{{ $item->check_in_time }}</td>
                <td>{{ $item->check_out_time }}</td>
                <td>{{ $item->actual_invoice_amount }}</td>
                <td>{{ $item->note }}</td>
                <td>{{ $item->latitude }}, {{ $item->longitude }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
