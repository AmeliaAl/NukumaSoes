<table>
    <thead>
        @php
            $parts = explode('-', $periode);
            $bulanNum = (int) ($parts[1] ?? date('m'));
            $tahunNum = $parts[0] ?? date('Y');
            $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
            $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
        @endphp
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold; font-size: 16px;">NUKUMA SOES</th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold; font-size: 14px;">LAPORAN LABA RUGI</th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold; font-size: 12px;">PERIODE: {{ strtoupper($namaPeriode) }}</th>
        </tr>
        <tr>
            <th colspan="3"></th>
        </tr>
    </thead>
    <tbody>
        <!-- PENDAPATAN -->
        <tr>
            <td colspan="3" style="font-weight: bold;">Pendapatan</td>
        </tr>
        @foreach($pendapatan as $item)
        <tr>
            <td>- {{ $item['keterangan'] }}</td>
            <td></td>
            <td style="text-align: right;">{{ $item['jumlah'] }}</td>
        </tr>
        @endforeach
        
        <tr>
            <td colspan="3"></td>
        </tr>

        <!-- BEBAN -->
        <tr>
            <td colspan="3" style="font-weight: bold;">Beban</td>
        </tr>
        @foreach($beban as $index => $item)
        <tr>
            <td>- {{ $item['keterangan'] }}</td>
            <td style="text-align: right;">{{ $item['jumlah'] }}</td>
            <td></td>
        </tr>
        @endforeach

        <!-- TOTALS & LABA -->
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000;"></td>
            <td style="text-align: right;">-{{ $totalBeban }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: center; text-transform: uppercase;">{{ $labaRugi >= 0 ? 'LABA' : 'RUGI' }}</td>
            <td></td>
            <td style="font-weight: bold; text-align: right; border-bottom: 1px solid #000; border-top: 1px solid #000;">{{ abs($labaRugi) }}</td>
        </tr>
    </tbody>
</table>
