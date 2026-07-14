<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Buku Besar</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 10mm 15mm 10mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9.5px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }
        .header .company {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 2px;
        }
        .header .sub {
            font-size: 9px;
            margin-top: 2px;
            color: #333;
        }

        /* ── Akun info ── */
        .akun-info {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 4px 10px;
            margin-bottom: 8px;
            font-size: 9.5px;
            font-weight: bold;
        }

        /* ── Tabel ── */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table, th, td { border: 1px solid #000; }

        thead th {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 4px 3px;
            background: #e8e8e8;
            word-wrap: break-word;
        }

        tbody td {
            font-size: 9px;
            padding: 3px 3px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        tfoot td {
            font-size: 9.5px;
            font-weight: bold;
            padding: 4px 3px;
            background: #e8e8e8;
            border-top: 2px solid #000;
        }

        .text-right  { text-align: right  !important; }
        .text-left   { text-align: left   !important; }
        .text-center { text-align: center !important; }
        .bold        { font-weight: bold; }
        .bg-light    { background: #f8f8f8; }

        /* ── Lebar kolom ── */
        col.c-tgl    { width: 9%;  }
        col.c-ket    { width: 32%; }
        col.c-debit  { width: 14%; }
        col.c-kredit { width: 14%; }
        col.c-sd     { width: 14%; }
        col.c-sk     { width: 14%; }
    </style>
</head>
<body>

    {{-- Header Perusahaan --}}
    <div class="header">
        <div class="company">Nukuma Soes</div>
        <div class="title">Buku Besar</div>
        <div class="sub">
            Periode:
            {{ $periodeAwal  ? \Carbon\Carbon::parse($periodeAwal)->format('d/m/Y')  : '-' }}
            s/d
            {{ $periodeAkhir ? \Carbon\Carbon::parse($periodeAkhir)->format('d/m/Y') : '-' }}
        </div>
    </div>

    {{-- Info Akun --}}
    <div class="akun-info">
        Akun: {{ $akun->no_akun ?? '-' }} &mdash; {{ $akun->nama_akun ?? '-' }}
    </div>

    {{-- Tabel --}}
    <table>
        <colgroup>
            <col class="c-tgl">
            <col class="c-ket">
            <col class="c-debit">
            <col class="c-kredit">
            <col class="c-sd">
            <col class="c-sk">
        </colgroup>

        <thead>
            <tr>
                <th rowspan="2" style="vertical-align:middle;">Tanggal</th>
                <th rowspan="2" style="vertical-align:middle;">Keterangan</th>
                <th rowspan="2" style="vertical-align:middle;">Debit</th>
                <th rowspan="2" style="vertical-align:middle;">Kredit</th>
                <th colspan="2">Saldo</th>
            </tr>
            <tr>
                <th>Debit</th>
                <th>Kredit</th>
            </tr>
        </thead>

        <tbody>

            {{-- Baris SALDO AWAL --}}
            <tr class="bg-light">
                <td colspan="2" class="text-center bold">SALDO AWAL</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                @if($saldoAwalNominal > 0)
                    <td class="text-right bold">{{ \App\Helpers\FormatHelper::rupiah($saldoAwalNominal) }}</td>
                    <td class="text-center">-</td>
                @elseif($saldoAwalNominal < 0)
                    <td class="text-center">-</td>
                    <td class="text-right bold">{{ \App\Helpers\FormatHelper::rupiah(abs($saldoAwalNominal)) }}</td>
                @else
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                @endif
            </tr>

            {{-- Baris Transaksi --}}
            @forelse($jurnals as $jurnal)
            <tr>
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($jurnal['tanggal'])->format('d/m/Y') }}
                </td>
                <td class="text-left">{{ $jurnal['keterangan'] }}</td>

                {{-- Debit --}}
                <td class="text-right">
                    @if($jurnal['debit'] > 0)
                        {{ \App\Helpers\FormatHelper::rupiah($jurnal['debit']) }}
                    @else
                        -
                    @endif
                </td>

                {{-- Kredit --}}
                <td class="text-right">
                    @if($jurnal['kredit'] > 0)
                        {{ \App\Helpers\FormatHelper::rupiah($jurnal['kredit']) }}
                    @else
                        -
                    @endif
                </td>

                {{-- Saldo berjalan (sudah include saldo awal dari controller) --}}
                @if($jurnal['saldo'] >= 0)
                    <td class="text-right bold">{{ \App\Helpers\FormatHelper::rupiah($jurnal['saldo']) }}</td>
                    <td class="text-center">-</td>
                @else
                    <td class="text-center">-</td>
                    <td class="text-right bold">{{ \App\Helpers\FormatHelper::rupiah(abs($jurnal['saldo'])) }}</td>
                @endif
            </tr>
            @empty
            @if($saldoAwalNominal == 0)
            <tr>
                <td colspan="6" class="text-center">Tidak ada transaksi untuk periode ini.</td>
            </tr>
            @endif
            @endforelse

        </tbody>

        {{-- Saldo Akhir --}}
        @if(count($jurnals) > 0 || $saldoAwalNominal > 0)
        <tfoot>
            <tr>
                <td colspan="2" class="text-center bold">SALDO AKHIR</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                @if($saldoAkhir >= 0)
                    <td class="text-right bold">{{ \App\Helpers\FormatHelper::rupiah($saldoAkhir) }}</td>
                    <td class="text-center">-</td>
                @else
                    <td class="text-center">-</td>
                    <td class="text-right bold">{{ \App\Helpers\FormatHelper::rupiah(abs($saldoAkhir)) }}</td>
                @endif
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
