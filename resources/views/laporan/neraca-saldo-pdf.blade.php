<!DOCTYPE html>
<html>
<head>
    <title>Neraca Saldo</title>
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
    <h2 style="margin-top: -5px;">NERACA SALDO</h2>
    <p class="text-center" style="font-weight: bold; margin-top: -5px; margin-bottom: 20px;">PERIODE: {{ strtoupper($namaPeriode) }}</p>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>AKUN</th>
                <th>DEBIT</th>
                <th>KREDIT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
            <tr>
                <td class="text-center">{{ $entry['ref'] }}</td>
                <td>{{ $entry['keterangan'] }}</td>
                <td class="text-right">{{ $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="2" class="text-center">TOTAL</th>
                <th class="text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totalKredit, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
