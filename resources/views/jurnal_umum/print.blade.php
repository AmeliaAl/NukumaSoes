<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Jurnal Umum</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 15mm 15mm 15mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header .company {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 2px;
        }
        .header .periode {
            font-size: 10px;
            margin-top: 2px;
            color: #333;
        }

        /* ── Tabel ── */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 6px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        thead th {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 5px 4px;
            background: #e8e8e8;
            color: #000;
            word-wrap: break-word;
        }

        tbody td {
            font-size: 10.5px;
            padding: 4px 5px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        /* Garis bawah tebal sebagai pemisah antar transaksi */
        tr.group-end td {
            border-bottom: 2px solid #000;
        }

        /* Akun kredit: indent ke kanan + italic */
        .kredit-row {
            padding-left: 25px !important;
            font-style: italic;
        }

        .text-right  { text-align: right  !important; }
        .text-left   { text-align: left   !important; }
        .text-center { text-align: center !important; }

        /* Invisible: teks transparan tapi border tetap ada (untuk rowspan simulasi) */
        .invisible { color: transparent; }

        /* Lebar kolom */
        col.c-tgl    { width: 13%; }
        col.c-akun   { width: 47%; }
        col.c-ref    { width: 8%;  }
        col.c-debit  { width: 16%; }
        col.c-kredit { width: 16%; }

        /* Footer total */
        tfoot td, tfoot th {
            font-size: 11px;
            font-weight: bold;
            padding: 5px 5px;
            border-top: 2px solid #000;
        }

        /* Status jurnal */
        .status {
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        .status.seimbang {
            color: #000;
        }
        .status.tidak-seimbang {
            color: #000;
        }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="header">
        <div class="company">Nukuma Soes</div>
        <div class="title">Jurnal Umum</div>
        <div class="periode">
            Periode :
            {{ $periodeAwal  ? \Carbon\Carbon::parse($periodeAwal)->format('d F Y')  : '-' }}
            s/d
            {{ $periodeAkhir ? \Carbon\Carbon::parse($periodeAkhir)->format('d F Y') : '-' }}
        </div>
    </div>

    {{-- ── Tabel ── --}}
    <table>
        <colgroup>
            <col class="c-tgl">
            <col class="c-akun">
            <col class="c-ref">
            <col class="c-debit">
            <col class="c-kredit">
        </colgroup>

        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Akun</th>
                <th>Ref</th>
                <th>Debit</th>
                <th>Kredit</th>
            </tr>
        </thead>

        <tbody>
            @php
                $groupedJurnals = collect($jurnals)->groupBy('no_bukti');
            @endphp

            @forelse($groupedJurnals as $noBukti => $rows)
                @php
                    $rowValues = $rows->values();
                    $firstRow  = $rowValues->first();
                    $rowCount  = $rowValues->count();
                @endphp

                @foreach($rowValues as $rowIdx => $row)
                @php
                    $isFirst = ($rowIdx === 0);
                    $isLast  = ($rowIdx === $rowCount - 1);
                    $isKredit = ($row['kredit'] > 0);
                @endphp
                <tr class="{{ $isLast ? 'group-end' : '' }}">

                    {{-- Tanggal: rowspan, muncul sekali per transaksi --}}
                    @if($isFirst)
                    <td rowspan="{{ $rowCount }}" class="text-center"
                        style="vertical-align:top; padding-top:5px;">
                        {{ \Carbon\Carbon::parse($firstRow['tanggal'])->format('d/m/Y') }}
                    </td>
                    @endif

                    {{-- Akun: debit normal, kredit indent italic --}}
                    <td class="{{ $isKredit ? 'kredit-row' : 'text-left' }}">
                        {{ $row['nama_akun'] }}
                    </td>

                    {{-- Ref: kode akun --}}
                    <td class="text-center" style="color:#333;">
                        {{ $row['kode_akun'] }}
                    </td>

                    {{-- Debit --}}
                    <td class="text-right">
                        @if($row['debit'] > 0)
                            {{ \App\Helpers\FormatHelper::rupiah($row['debit']) }}
                        @else
                            -
                        @endif
                    </td>

                    {{-- Kredit --}}
                    <td class="text-right">
                        @if($row['kredit'] > 0)
                            {{ \App\Helpers\FormatHelper::rupiah($row['kredit']) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach

            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding:10px;">
                        Tidak ada data jurnal.
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- ── Footer Total ── --}}
        <tfoot>
            <tr>
                <td colspan="3" class="text-right" style="background:#e8e8e8;">
                    TOTAL
                </td>
                <td class="text-right" style="background:#e8e8e8;">
                    {{ \App\Helpers\FormatHelper::rupiah($totalDebit) }}
                </td>
                <td class="text-right" style="background:#e8e8e8;">
                    {{ \App\Helpers\FormatHelper::rupiah($totalKredit) }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- ── Status Jurnal ── --}}
    <div class="status {{ $totalDebit == $totalKredit ? 'seimbang' : 'tidak-seimbang' }}">
        @if($totalDebit == $totalKredit)
            Jurnal Seimbang
        @else
            Jurnal Tidak Seimbang &nbsp;|&nbsp;
            Selisih: {{ \App\Helpers\FormatHelper::rupiah(abs($totalDebit - $totalKredit)) }}
        @endif
    </div>

</body>
</html>
