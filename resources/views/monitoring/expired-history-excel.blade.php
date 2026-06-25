<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>BATCH</th>
            <th>PRODUK</th>
            <th>RASA</th>
            <th>KATEGORI</th>
            <th>STOK</th>
            <th>KERUGIAN PRODUK EXPIRED</th>
            <th>KEMAS</th>
            <th>EXP</th>
            <th>STATUS</th>
            <th>SISA</th>
        </tr>
    </thead>
    <tbody>
        @foreach($histories as $index => $history)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $history->no_batch }}</td>
                <td>{{ $history->nama_produk }}</td>
                <td>{{ $history->rasa_produk ?? '-' }}</td>
                <td>{{ $history->kategori }}</td>
                <td>{{ $history->jumlah }}</td>
                <td>{{ number_format($history->jumlah * ($history->hpp ?? 0), 0, ',', '.') }}</td>
                <td>{{ $history->tgl_masuk ? $history->tgl_masuk->format('d/m/Y') : '-' }}</td>
                <td>{{ $history->tgl_expired ? $history->tgl_expired->format('d/m/Y') : '-' }}</td>
                <td>Expired</td>
                <td>{{ $history->sisa_hari }} Hr</td>
            </tr>
        @endforeach
    </tbody>
</table>
