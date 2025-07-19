<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>No. Telepon</th>
            <th>Jumlah Absensi</th>
            <th>Jumlah Cuti</th>
            <th>Jumlah Tagihan</th>
            <th>Tanggal Daftar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->attendances_count }}</td>
                <td>{{ $user->leaves_count }}</td>
                <td>{{ $user->invoices_count }}</td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
