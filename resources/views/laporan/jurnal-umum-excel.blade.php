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
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 16px;">JURNAL UMUM</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 14px;">PERIODE: {{ strtoupper($namaPeriode) }}</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">TANGGAL</th>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">KETERANGAN</th>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">REF</th>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">DEBIT</th>
            <th style="font-weight: bold; text-align: center; background-color: #ffedd5;">KREDIT</th>
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
            <td style="text-align: center;">
                @if($lastGroup !== $entry->created_at->format('Y-m-d H:i:s'))
                    {{ \Carbon\Carbon::parse($entry->tanggal)->format('d/m/Y') }}
                    @php $lastGroup = $entry->created_at->format('Y-m-d H:i:s'); @endphp
                @endif
            </td>
            <td>{{ $entry->kredit > 0 ? '     ' . $entry->keterangan : $entry->keterangan }}</td>
            <td style="text-align: center;">{{ $entry->ref }}</td>
            <td>{{ $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 2, ',', '.') : '' }}</td>
            <td>{{ $entry->kredit > 0 ? 'Rp ' . number_format($entry->kredit, 2, ',', '.') : '' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center;">Tidak ada data jurnal umum.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right; font-weight: bold; background-color: #e5e7eb;">TOTAL</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">Rp {{ number_format($totalDebit, 2, ',', '.') }}</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">Rp {{ number_format($totalKredit, 2, ',', '.') }}</th>
        </tr>
    </tfoot>
</table>
