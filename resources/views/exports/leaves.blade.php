<table>
    <thead>
        <tr>
            <th>Nama Karyawan</th>
            <th>Nama Izin</th>
            <th>Alasan</th>
            <th>Deskripsi</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($leaves as $leave)
            <tr>
                <td>{{ $leave->user->name ?? '-' }}</td>
                <td>{{ $leave->name ?? '-' }}</td>
                <td>{{ $leave->reason }}</td>
                <td>{{ $leave->description }}</td>
                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('Y-m-d') }}</td>
                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
