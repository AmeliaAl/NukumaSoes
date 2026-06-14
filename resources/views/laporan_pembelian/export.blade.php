<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>No Pembelian</th>
                <th>Nomor Permintaan</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Bahan Baku</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Diskon</th>
                <th>Total Bersih</th>
                <th>Ongkir</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembelians as $pembelian)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pembelian->no_pembelian ?? '-' }}</td>
                <td>{{ $pembelian->nomor_permintaan ?? '-' }}</td>
                <td>{{ $pembelian->tanggal }}</td>
                <td>{{ $pembelian->supplier->nama_supplier }}</td>
                <td>{{ $pembelian->bahanBaku->nama_bahan }}</td>
                <td>{{ $pembelian->qty }}</td>
                <td>{{ number_format($pembelian->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($pembelian->diskon ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($pembelian->total_bersih ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0)), 0, ',', '.') }}</td>
                <td>{{ number_format($pembelian->ongkir ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($pembelian->grand_total ?? (($pembelian->total_bersih ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0))) + ($pembelian->ongkir ?? 0)), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13">Tidak ada transaksi pembelian untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
