<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Produk Expired</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f3f4f6; text-align: center; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>NUKUMA SOES</h2>
        <h2 style="margin-top: -5px;">RIWAYAT PRODUK EXPIRED</h2>
        <p style="margin-top: -5px;">Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>BATCH</th>
                <th>PRODUK</th>
                <th>RASA</th>
                <th>KATEGORI</th>
                <th>STOK</th>
                <th>NOMINAL</th>
                <th>KEMAS</th>
                <th>EXP</th>
                <th>STATUS</th>
                <th>SISA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $index => $history)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $history->no_batch }}</td>
                <td>{{ $history->nama_produk }}</td>
                <td class="text-center">{{ $history->rasa_produk ?? '-' }}</td>
                <td>{{ $history->kategori }}</td>
                <td class="text-center">{{ $history->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($history->dynamic_nominal, 2, ',', '.') }}</td>
                <td class="text-center">{{ $history->tgl_masuk ? $history->tgl_masuk->format('d/m/Y') : '-' }}</td>
                <td class="text-center text-red-600">{{ $history->tgl_expired ? $history->tgl_expired->format('d/m/Y') : '-' }}</td>
                <td class="text-center">Expired</td>
                <td class="text-center">{{ $history->sisa_hari }} Hr</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">Tidak ada data riwayat produk expired.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
