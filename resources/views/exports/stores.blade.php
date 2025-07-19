<table>
    <thead>
        <tr>
            <th>Nama Toko</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Latitude</th>
            <th>Longitude</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stores as $store)
            <tr>
                <td>{{ $store->name }}</td>
                <td>{{ $store->address }}</td>
                <td>{{ $store->phone }}</td>
                <td>{{ $store->latitude }}</td>
                <td>{{ $store->longitude }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
