<x-filament-panels::page>
    @php $rows = $this->getData(); @endphp

    <x-filament::section>
        @if (empty($rows))
            <p style="text-align:center; color:#9ca3af; padding:2rem 0; font-size:0.875rem;">
                Tidak ada piutang yang mendekati jatuh tempo dalam 7 hari ke depan.
            </p>
        @else
            <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                <thead>
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:8px 16px; text-align:left; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">No</th>
                        <th style="padding:8px 16px; text-align:left; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Nama</th>
                        <th style="padding:8px 16px; text-align:left; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Tipe</th>
                        <th style="padding:8px 16px; text-align:left; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Referensi</th>
                        <th style="padding:8px 16px; text-align:center; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Jatuh Tempo</th>
                        <th style="padding:8px 16px; text-align:center; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Sisa Hari</th>
                        <th style="padding:8px 16px; text-align:right; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Sisa Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $row)
                        @php $row = (object) $row; @endphp
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:10px 16px; color:#d1d5db; font-size:0.8rem; font-weight:600;">{{ $i + 1 }}</td>
                            <td style="padding:10px 16px; color:#111827; font-weight:600;">{{ $row->nama }}</td>
                            <td style="padding:10px 16px;">
                                @if ($row->tipe === 'Pelanggan')
                                    <span style="display:inline-block; padding:2px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; background:#dbeafe; color:#1d4ed8;">Pelanggan</span>
                                @else
                                    <span style="display:inline-block; padding:2px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; background:#fef3c7; color:#b45309;">Mitra</span>
                                @endif
                            </td>
                            <td style="padding:10px 16px; color:#374151; font-family:monospace; font-size:0.8rem;">{{ $row->referensi }}</td>
                            <td style="padding:10px 16px; text-align:center; font-weight:600; color:#b45309;">
                                {{ \Carbon\Carbon::parse($row->jatuh_tempo)->translatedformat('d/m/Y') }}
                            </td>
                            <td style="padding:10px 16px; text-align:center;">
                                <span style="display:inline-block; padding:2px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; background:#fef9c3; color:#a16207;">
                                    {{ $row->sisa_hari }} hari lagi
                                </span>
                            </td>
                            <td style="padding:10px 16px; text-align:right; font-weight:700; color:#dc2626;">
                                Rp {{ number_format($row->sisa_piutang, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid #e5e7eb; background:#f9fafb;">
                        <td colspan="6" style="padding:10px 16px; font-weight:700; color:#374151;">Total</td>
                        <td style="padding:10px 16px; text-align:right; font-weight:700; color:#dc2626;">
                            Rp {{ number_format(collect($rows)->sum('sisa_piutang'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </x-filament::section>
</x-filament-panels::page>
