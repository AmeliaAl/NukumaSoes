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
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 18px;">NUKUMA SOES</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 16px;">BUKU BESAR</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 14px;">PERIODE: {{ strtoupper($namaPeriode) }}</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 14px;">NAMA AKUN: {{ strtoupper($namaAkun ?: 'PILIH AKUN') }}</th>
        </tr>
        <tr>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">TANGGAL</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">KETERANGAN</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">DEBIT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">KREDIT</th>
            <th colspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">SALDO</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; vertical-align: middle;">DEBIT</th>
            <th style="font-weight: bold; text-align: center; vertical-align: middle;">KREDIT</th>
        </tr>
    </thead>
    <tbody>
        @forelse($entries as $entry)
            @if(isset($entry['is_saldo_awal']) && $entry['is_saldo_awal'])
                @continue
            @endif
            @if(isset($entry['is_saldo_akhir']) && $entry['is_saldo_akhir'])
                <tr>
                    <td colspan="4" style="background-color: #e5e7eb; color: #000000; font-weight: bold; text-align: left;">SALDO AKHIR</td>
                    <td style="background-color: #e5e7eb; color: #000000; font-weight: bold; text-align: right;">
                        @if(!$isCreditNormal && $entry['saldo'] >= 0 || $isCreditNormal && $entry['saldo'] < 0)
                            {{ 'Rp ' . number_format(abs($entry['saldo']), 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="background-color: #e5e7eb; color: #000000; font-weight: bold; text-align: right;">
                        @if($isCreditNormal && $entry['saldo'] >= 0 || !$isCreditNormal && $entry['saldo'] < 0)
                            {{ 'Rp ' . number_format(abs($entry['saldo']), 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                    <td>{{ $entry['keterangan'] }}</td>
                    <td style="text-align: right;">{{ $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' }}</td>
                    <td style="text-align: right;">{{ $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' }}</td>
                    <td style="text-align: right;">
                        @if(!$isCreditNormal && $entry['saldo'] >= 0 || $isCreditNormal && $entry['saldo'] < 0)
                            {{ 'Rp ' . number_format(abs($entry['saldo']), 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($isCreditNormal && $entry['saldo'] >= 0 || !$isCreditNormal && $entry['saldo'] < 0)
                            {{ 'Rp ' . number_format(abs($entry['saldo']), 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endif
        @empty
        <tr>
            <td colspan="6" style="text-align: center;">Tidak ada data buku besar.</td>
        </tr>
        @endforelse
    </tbody>
</table>
