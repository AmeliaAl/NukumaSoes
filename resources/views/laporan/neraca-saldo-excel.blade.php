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
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 18px;">NUKUMA SOES</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 16px;">NERACA SALDO</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 14px;">PERIODE: {{ strtoupper($namaPeriode) }}</th>
        </tr>
        <tr>
            <th colspan="4"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">NO</th>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">AKUN</th>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">DEBIT</th>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">KREDIT</th>
        </tr>
    </thead>
    <tbody>
        @forelse($entries as $entry)
        <tr>
            <td style="text-align: center;">{{ $entry['ref'] }}</td>
            <td>{{ $entry['keterangan'] }}</td>
            <td style="text-align: right;">{{ $entry['debit'] > 0 ? $entry['debit'] : '-' }}</td>
            <td style="text-align: right;">{{ $entry['kredit'] > 0 ? $entry['kredit'] : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align: center;">Tidak ada data untuk periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: center; font-weight: bold; background-color: #e5e7eb;">TOTAL</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">{{ $totalDebit }}</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">{{ $totalKredit }}</th>
        </tr>
    </tfoot>
</table>
