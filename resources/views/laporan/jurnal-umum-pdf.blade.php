<!DOCTYPE html>
<html>
<head>
    <title>Jurnal Umum</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #ffedd5; text-align: center; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row th { background-color: #e5e7eb; }
    </style>
</head>
<body>

    @php
        $parts = explode('-', $periode);
        $bulanNum = (int) ($parts[1] ?? date('m'));
        $tahunNum = $parts[0] ?? date('Y');
        $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
    @endphp
    <h2>NUKUMA SOES</h2>
    <h2 style="margin-top: -5px;">JURNAL UMUM</h2>
    <p class="text-center" style="font-weight: bold; margin-top: -5px; margin-bottom: 20px;">PERIODE: {{ strtoupper($namaPeriode) }}</p>

    <table>
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>AKUN</th>
                <th>REF</th>
                <th>DEBIT</th>
                <th>KREDIT</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $lastGroup = null; 
                $totalDebit = 0;
                $totalKredit = 0;
            @endphp
            @forelse($entries as $entry)
            @php
                $totalDebit += $entry->debit;
                $totalKredit += $entry->kredit;
            @endphp
            <tr>
                <td class="text-center">
                    @if($lastGroup !== $entry->created_at->format('Y-m-d H:i:s'))
                        {{ \Carbon\Carbon::parse($entry->tanggal)->format('d/m/Y') }}
                        @php $lastGroup = $entry->created_at->format('Y-m-d H:i:s'); @endphp
                    @endif
                </td>
                <td style="{{ $entry->kredit > 0 ? 'padding-left: 50px;' : 'padding-left: 8px;' }}">{{ $entry->keterangan }}</td>
                <td class="text-center">{{ $entry->ref }}</td>
                <td class="text-right">{{ $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 2, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $entry->kredit > 0 ? 'Rp ' . number_format($entry->kredit, 2, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data jurnal umum.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="3" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($totalDebit, 2, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totalKredit, 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
