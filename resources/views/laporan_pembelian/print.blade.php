<!DOCTYPE html>
<html>

<head>

    <title>Laporan Pembelian</title>

    <style>

        @page {
            size: A4 landscape;
            margin: 15px;
        }

        body {

            font-family: sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 5px;

        }

        h2, h4 {

            text-align: center;
            margin: 0;

        }

        table {

            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;

        }

        table, th, td {

            border: 1px solid black;

        }

        th, td {

            padding: 6px;
            text-align: center;
            vertical-align: middle;

        }

        th {
            background: #f0f0f0;
        }

    </style>

</head>

<body>

    <h2>LAPORAN PEMBELIAN</h2>

    <h4>

        Periode:
        {{ $tanggalAwal ?? '-' }}
        s/d
        {{ $tanggalAkhir ?? '-' }}

    </h4>

    <table>

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
                <th>Total Pembelian</th>
                <th>Diskon</th>
                <th>Total Bersih</th>
                <th>Ongkir</th>
                <th>Grand Total</th>

            </tr>

        </thead>

        <tbody>

            @forelse($pembelians as $pembelian)
            @php $rowspan = max(1, $pembelian->details->count()); @endphp
            <tr>
                <td rowspan="{{ $rowspan }}">{{ $loop->iteration }}</td>
                <td rowspan="{{ $rowspan }}">{{ $pembelian->no_pembelian ?? '-' }}</td>
                <td rowspan="{{ $rowspan }}">{{ $pembelian->nomor_permintaan ?? '-' }}</td>
                <td rowspan="{{ $rowspan }}">{{ $pembelian->tanggal }}</td>
                <td rowspan="{{ $rowspan }}">{{ $pembelian->supplier->nama_supplier }}</td>
                
                @if($pembelian->details->count() > 0)
                    <td>{{ $pembelian->details[0]->bahanBaku->nama_bahan ?? '-' }}</td>
                    <td>{{ $pembelian->details[0]->qty }}</td>
                    <td>Rp {{ number_format($pembelian->details[0]->harga, 0, ',', '.') }}</td>
                @else
                    <td>-</td><td>0</td><td>Rp 0</td>
                @endif
                
                <td rowspan="{{ $rowspan }}">Rp {{ number_format($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga, 0, ',', '.') }}</td>
                <td rowspan="{{ $rowspan }}">Rp {{ number_format($pembelian->diskon ?? 0, 0, ',', '.') }}</td>
                <td rowspan="{{ $rowspan }}">Rp {{ number_format($pembelian->total_bersih ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0)), 0, ',', '.') }}</td>
                <td rowspan="{{ $rowspan }}">Rp {{ number_format($pembelian->ongkir ?? 0, 0, ',', '.') }}</td>
                <td rowspan="{{ $rowspan }}">Rp {{ number_format($pembelian->grand_total ?? (($pembelian->total_bersih ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - ($pembelian->diskon ?? 0))) + ($pembelian->ongkir ?? 0)), 0, ',', '.') }}</td>
            </tr>
            @for($i = 1; $i < $rowspan; $i++)
            <tr>
                <td>{{ $pembelian->details[$i]->bahanBaku->nama_bahan ?? '-' }}</td>
                <td>{{ $pembelian->details[$i]->qty }}</td>
                <td>Rp {{ number_format($pembelian->details[$i]->harga, 0, ',', '.') }}</td>
            </tr>
            @endfor

            @empty

            <tr>
                <td colspan="13">Tidak ada transaksi pembelian untuk periode ini.</td>
            </tr>

            @endforelse

        </tbody>

        <tfoot>

            <tr>
                <th colspan="8">TOTAL</th>
                <th>Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</th>
                <th>Rp {{ number_format($summary['diskon'], 0, ',', '.') }}</th>
                <th>Rp {{ number_format($summary['total_bersih'], 0, ',', '.') }}</th>
                <th>Rp {{ number_format($summary['ongkir'], 0, ',', '.') }}</th>
                <th>Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}</th>
            </tr>

        </tfoot>

    </table>

</body>

</html>