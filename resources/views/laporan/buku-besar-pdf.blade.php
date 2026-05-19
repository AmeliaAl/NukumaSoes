<!DOCTYPE html>
<html>
<head>
    <title>Buku Besar</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
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
    <h2 style="margin-top: -5px;">BUKU BESAR</h2>
    <p class="text-center" style="font-weight: bold; margin-top: -5px; margin-bottom: 5px;">PERIODE: {{ strtoupper($namaPeriode) }}</p>
    <p class="text-center" style="font-weight: bold; margin-top: 0px; margin-bottom: 20px;">NAMA AKUN: {{ strtoupper($namaAkun ?: 'PILIH AKUN') }}</p>

    <table>
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>KETERANGAN</th>
                <th>DEBIT</th>
                <th>KREDIT</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                @if(isset($entry['is_saldo_awal']) && $entry['is_saldo_awal'])
                    <tr style="background-color: #f9f9f9; font-style: italic;">
                        <td class="text-center">{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d M Y') }}</td>
                        <td><strong>{{ $entry['keterangan'] }}</strong></td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{ ($entry['saldo'] < 0 ? '-' : '') . 'Rp ' . number_format(abs($entry['saldo']), 0, ',', '.') }}</td>
                    </tr>
                @elseif(isset($entry['is_saldo_akhir']) && $entry['is_saldo_akhir'])
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <td colspan="4" style="text-align: left; border: 1px solid #000;">SALDO AKHIR</td>
                        <td class="text-right" style="border: 1px solid #000;">{{ ($entry['saldo'] < 0 ? '-' : '') . 'Rp ' . number_format(abs($entry['saldo']), 0, ',', '.') }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d M Y') }}</td>
                        <td>{{ $entry['keterangan'] }}</td>
                        <td class="text-right">{{ $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' }}</td>
                        <td class="text-right">{{ $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' }}</td>
                        <td class="text-right">{{ ($entry['saldo'] < 0 ? '-' : '') . 'Rp ' . number_format(abs($entry['saldo']), 0, ',', '.') }}</td>
                    </tr>
                @endif
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data buku besar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
