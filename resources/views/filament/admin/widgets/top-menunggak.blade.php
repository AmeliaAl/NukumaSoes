<x-filament-widgets::widget>
    <x-filament::section heading="Top 5 Entitas Menunggak">

        @if ($rows->isEmpty())
            <p style="text-align:center; color:#9ca3af; padding:2rem 0; font-size:0.875rem;">
                Tidak ada piutang yang menunggak.
            </p>
        @else
            <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                <thead>
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:8px 16px; text-align:left; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">No</th>
                        <th style="padding:8px 16px; text-align:left; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Nama</th>
                        <th style="padding:8px 16px; text-align:left; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Tipe</th>
                        <th style="padding:8px 16px; text-align:right; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Sisa Piutang</th>
                        <th style="padding:8px 16px; text-align:center; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Umur</th>
                        <th style="padding:8px 16px; text-align:center; font-size:0.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em;">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $row)
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
                            <td style="padding:10px 16px; text-align:right; font-weight:700; color:#dc2626;">
                                Rp {{ number_format($row->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td style="padding:10px 16px; text-align:center; color:#374151; font-weight:600;">
                                {{ $row->umur_piutang }} hari
                            </td>
                            <td style="padding:10px 16px; text-align:center;">
                                @if ($row->kategori === 'Lancar')
                                    <span style="display:inline-block; padding:2px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; background:#dcfce7; color:#15803d;">Lancar</span>
                                @elseif ($row->kategori === 'Waspada')
                                    <span style="display:inline-block; padding:2px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; background:#fef9c3; color:#a16207;">Waspada</span>
                                @elseif ($row->kategori === 'Perlu Perhatian')
                                    <span style="display:inline-block; padding:2px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; background:#ffedd5; color:#c2410c;">Perlu Perhatian</span>
                                @else
                                    <span style="display:inline-block; padding:2px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; background:#fee2e2; color:#dc2626;">Risiko Tinggi</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
