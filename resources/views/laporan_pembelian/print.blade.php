<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 8mm 12mm 8mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 8.5px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header h2 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p { font-size: 8.5px; margin: 0; }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table, th, td { border: 1px solid #333; }

        thead th {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            background: #f0f0f0;
            word-wrap: break-word;
        }

        tbody td {
            font-size: 8px;
            padding: 3px 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        tfoot th {
            font-size: 8.5px;
            font-weight: bold;
            padding: 4px 3px;
            background: #e8e8e8;
            border-top: 2px solid #000;
        }

        .text-right  { text-align: right  !important; }
        .text-left   { text-align: left   !important; }
        .text-center { text-align: center !important; }
        .bold        { font-weight: bold; }

        col.c-no      { width: 3.5%; }
        col.c-nopb    { width: 6.5%; }
        col.c-nopr    { width: 6.5%; }
        col.c-tgl     { width: 7%;   }
        col.c-sup     { width: 10%;  }
        col.c-bahan   { width: 10%;  }
        col.c-qty     { width: 4%;   }
        col.c-isi     { width: 5%;   }
        col.c-sat     { width: 5%;   }
        col.c-harga   { width: 9%;   }
        col.c-sub     { width: 9%;   }
        col.c-diskon  { width: 8%;   }
        col.c-ongkir  { width: 8%;   }
        col.c-grand   { width: 9%;   }
    </style>
</head>
<body>

<div class="header">
    <h2>Laporan Pembelian</h2>
    <p>Periode:
        {{ $tanggalAwal  ? \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y')  : '-' }}
        s/d
        {{ $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') : '-' }}
    </p>
</div>

<table>
    <colgroup>
        <col class="c-no"><col class="c-nopb"><col class="c-nopr"><col class="c-tgl">
        <col class="c-sup"><col class="c-bahan"><col class="c-qty"><col class="c-isi">
        <col class="c-sat"><col class="c-harga"><col class="c-sub">
        <col class="c-diskon"><col class="c-ongkir"><col class="c-grand">
    </colgroup>

    <thead>
        <tr>
            <th>No</th>
            <th>No<br>Pembelian</th>
            <th>No<br>Permintaan</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Bahan Baku</th>
            <th>Qty<br>Pembelian</th>
            <th>Isi/<br>Kemasan</th>
            <th>Satuan</th>
            <th>Harga<br>Satuan</th>
            <th>Subtotal</th>
            <th>Diskon</th>
            <th>Ongkir</th>
            <th>Grand Total</th>
        </tr>
    </thead>

    <tbody>
        @forelse($pembelians as $pembelian)
        @php
            $details  = $pembelian->details;
            $rowspan  = max(1, $details->count());
            $diskon   = $pembelian->diskon  ?? 0;
            $ongkir   = $pembelian->ongkir  ?? 0;
            $grandTot = $pembelian->grand_total
                        ?? (($pembelian->subtotal ?: $pembelian->qty * $pembelian->harga) - $diskon + $ongkir);
        @endphp

        {{-- Baris pertama — kolom grup pakai rowspan --}}
        <tr>
            <td rowspan="{{ $rowspan }}" class="text-center">{{ $loop->iteration }}</td>
            <td rowspan="{{ $rowspan }}" class="text-center">{{ $pembelian->no_pembelian ?? '-' }}</td>
            <td rowspan="{{ $rowspan }}" class="text-center">{{ $pembelian->nomor_permintaan ?? '-' }}</td>
            <td rowspan="{{ $rowspan }}" class="text-center">
                {{ $pembelian->tanggal ? \Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y') : '-' }}
            </td>
            <td rowspan="{{ $rowspan }}" class="text-left">{{ $pembelian->supplier->nama_supplier }}</td>

            {{-- Item pertama --}}
            @if($details->count() > 0)
                <td class="text-left">{{ $details[0]->bahanBaku->nama_bahan ?? '-' }}</td>
                <td class="text-center">{{ $details[0]->qty }}</td>
                <td class="text-center">{{ $details[0]->isi_per_kemasan ?? '-' }}</td>
                <td class="text-center">{{ $details[0]->bahanBaku->satuan ?? '-' }}</td>
                <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($details[0]->harga) }}</td>
                <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($details[0]->subtotal) }}</td>
            @else
                <td>-</td><td class="text-center">-</td><td>-</td>
                <td class="text-right">-</td><td class="text-right">-</td>
            @endif

            {{-- Diskon, Ongkir, Grand Total — rowspan, hanya di baris pertama --}}
            <td rowspan="{{ $rowspan }}" class="text-right">
                {{ \App\Helpers\FormatHelper::rupiah($diskon) }}
            </td>
            <td rowspan="{{ $rowspan }}" class="text-right">
                {{ \App\Helpers\FormatHelper::rupiah($ongkir) }}
            </td>
            <td rowspan="{{ $rowspan }}" class="text-right bold">
                {{ \App\Helpers\FormatHelper::rupiah($grandTot) }}
            </td>
        </tr>

        {{-- Baris selanjutnya — hanya kolom item bahan baku --}}
        @for($i = 1; $i < $rowspan; $i++)
        <tr>
            <td class="text-left">{{ $details[$i]->bahanBaku->nama_bahan ?? '-' }}</td>
            <td class="text-center">{{ $details[$i]->qty }}</td>
            <td class="text-center">{{ $details[$i]->isi_per_kemasan ?? '-' }}</td>
            <td class="text-center">{{ $details[$i]->bahanBaku->satuan ?? '-' }}</td>
            <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($details[$i]->harga) }}</td>
            <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($details[$i]->subtotal) }}</td>
        </tr>
        @endfor

        @empty
        <tr>
            <td colspan="14" class="text-center">Tidak ada data.</td>
        </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <th colspan="10" class="text-right">TOTAL</th>
            <th class="text-right">{{ \App\Helpers\FormatHelper::rupiah($summary['subtotal']) }}</th>
            <th class="text-right">{{ \App\Helpers\FormatHelper::rupiah($summary['diskon']) }}</th>
            <th class="text-right">{{ \App\Helpers\FormatHelper::rupiah($summary['ongkir']) }}</th>
            <th class="text-right">{{ \App\Helpers\FormatHelper::rupiah($summary['grand_total']) }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
