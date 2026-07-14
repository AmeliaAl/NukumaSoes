<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Overhead</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 8mm 12mm 8mm; }
        * { box-sizing: border-box; }

        body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; padding: 0; color: #000; }

        .header { text-align: center; margin-bottom: 8px; border-bottom: 2px solid #000; padding-bottom: 6px; }
        .header .company { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header .title   { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .header .sub     { font-size: 8.5px; margin-top: 2px; color: #333; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 6px; }
        table, th, td { border: 1px solid #000; }

        thead th {
            font-size: 8.5px; font-weight: bold; text-align: center;
            vertical-align: middle; padding: 4px 3px;
            background: #e8e8e8; word-wrap: break-word;
        }

        tbody td { font-size: 8.5px; padding: 3px 3px; vertical-align: middle; word-wrap: break-word; }

        tfoot td {
            font-size: 9px; font-weight: bold; padding: 4px 3px;
            background: #e8e8e8; border-top: 2px solid #000;
        }

        .text-right  { text-align: right  !important; }
        .text-left   { text-align: left   !important; }
        .text-center { text-align: center !important; }
        .bold        { font-weight: bold; }

        col.c-no      { width: 4%;  }
        col.c-jenis   { width: 9%;  }
        col.c-periode { width: 15%; }
        col.c-akun    { width: 22%; }
        col.c-ket     { width: 34%; }
        col.c-nominal { width: 12%; }
        col.c-tanggal { width: 0%; }  {{-- hidden via width 0 --}}
    </style>
</head>
<body>

    <div class="header">
        <div class="company">Nukuma Soes</div>
        <div class="title">Laporan Overhead</div>
        <div class="sub">
            Periode:
            {{ $tanggalAwal  ? \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y')  : '-' }}
            s/d
            {{ $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') : '-' }}
            @if($jenisPeriode ?? null)
                &nbsp;|&nbsp; Jenis: {{ ucfirst($jenisPeriode) }}
            @endif
        </div>
    </div>

    <table>
        <colgroup>
            <col class="c-no">
            <col class="c-jenis">
            <col class="c-periode">
            <col class="c-akun">
            <col class="c-ket">
            <col class="c-nominal">
        </colgroup>

        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Periode</th>
                <th>Periode Pembebanan</th>
                <th>Akun Overhead</th>
                <th>Keterangan</th>
                <th>Nominal</th>
            </tr>
        </thead>

        <tbody>
            @php
                $labelPeriode = ['harian'=>'Harian','mingguan'=>'Mingguan','bulanan'=>'Bulanan'];
                $no = 1;
            @endphp

            @forelse($overheads as $overhead)
            @php
                $rowspan    = max(1, $overhead->details->count());
                $jenis      = $overhead->jenis_periode ?? 'harian';
                $periodeStr = \App\Http\Controllers\LaporanOverheadController::formatPeriode($overhead);
            @endphp

            {{-- Baris pertama —  No, Jenis, Periode pakai rowspan --}}
            <tr>
                <td rowspan="{{ $rowspan }}" class="text-center">{{ $no++ }}</td>
                <td rowspan="{{ $rowspan }}" class="text-center">{{ $labelPeriode[$jenis] ?? ucfirst($jenis) }}</td>
                <td rowspan="{{ $rowspan }}" class="text-left">{{ $periodeStr }}</td>

                @if($overhead->details->count() > 0)
                    <td class="text-left">{{ $overhead->details[0]->coa->nama_akun ?? '-' }}</td>
                    <td class="text-left">{{ $overhead->details[0]->keterangan }}</td>
                    <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($overhead->details[0]->nominal) }}</td>
                @else
                    <td class="text-left">{{ $overhead->coa->nama_akun ?? '-' }}</td>
                    <td class="text-left">{{ $overhead->keterangan }}</td>
                    <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($overhead->nominal) }}</td>
                @endif
            </tr>

            {{-- Baris selanjutnya — hanya item detail --}}
            @for($i = 1; $i < $rowspan; $i++)
            <tr>
                <td class="text-left">{{ $overhead->details[$i]->coa->nama_akun ?? '-' }}</td>
                <td class="text-left">{{ $overhead->details[$i]->keterangan }}</td>
                <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($overhead->details[$i]->nominal) }}</td>
            </tr>
            @endfor

            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data overhead.</td>
            </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <td colspan="5" class="text-right bold">TOTAL</td>
                <td class="text-right bold">{{ \App\Helpers\FormatHelper::rupiah($total) }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
