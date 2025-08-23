<table>
    <thead>
        <tr>
            <th>Dispositivo</th>
            <th>Ambiente</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($devices as $device)
            <tr>
                <td>{{ $device->name }}</td>
                <td>{{ $device->environment->name ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
