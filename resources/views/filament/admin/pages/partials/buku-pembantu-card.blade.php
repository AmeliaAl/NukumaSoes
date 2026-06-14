<div style="background:#fff;border:1px solid #d1d5db;border-radius:12px;margin-bottom:24px;overflow:hidden;">

    {{-- Header: Nama & Kode --}}
    <div style="padding:12px 20px;background:#f9fafb;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:12px;color:#6b7280;font-weight:500;">Nama :</span>
            <span style="font-size:14px;font-weight:700;color:#111827;">{{ $item['nama'] }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:12px;color:#6b7280;font-weight:500;">{{ $labelKode ?? 'Kode' }} :</span>
            <span style="font-size:13px;font-weight:600;font-family:monospace;color:#92400e;background:#fef3c7;padding:2px 8px;border-radius:6px;">
                {{ $item['kode'] }}
            </span>
        </div>
    </div>

    {{-- Tabel --}}
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;width:100px;">Date</th>
                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;">Description</th>
                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;width:130px;">Ref</th>
                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;width:130px;">Debit</th>
                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;width:130px;">Credit</th>
                    <th colspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;">Balance</th>
                </tr>
                <tr style="background:#f3f4f6;">
                    <th style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;width:120px;">Debit</th>
                    <th style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;width:120px;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($item['rows'] as $i => $row)
                    <tr style="border-top:1px solid #f3f4f6;background:{{ $i % 2 === 0 ? '#fff' : '#fafafa' }};">
                        <td style="padding:9px 14px;color:#6b7280;font-family:monospace;font-size:12px;white-space:nowrap;border:1px solid #f3f4f6;text-align:center;">
                            {{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}
                        </td>
                        <td style="padding:9px 14px;color:#374151;border:1px solid #f3f4f6;">
                            {{ $row['keterangan'] }}
                        </td>
                        <td style="padding:9px 14px;color:#9ca3af;font-family:monospace;font-size:12px;white-space:nowrap;border:1px solid #f3f4f6;text-align:center;">
                            {{ $row['ref'] }}
                        </td>
                        <td style="padding:9px 14px;text-align:right;color:#374151;font-variant-numeric:tabular-nums;border:1px solid #f3f4f6;">
                            {{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '—' }}
                        </td>
                        <td style="padding:9px 14px;text-align:right;color:#374151;font-variant-numeric:tabular-nums;border:1px solid #f3f4f6;">
                            {{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '—' }}
                        </td>
                        <td style="padding:9px 14px;text-align:right;font-variant-numeric:tabular-nums;border:1px solid #f3f4f6;color:{{ $row['saldo_debit'] > 0 ? '#111827' : '#9ca3af' }};">
                            {{ $row['saldo_debit'] > 0 ? number_format($row['saldo_debit'], 0, ',', '.') : '-' }}
                        </td>
                        <td style="padding:9px 14px;text-align:right;font-variant-numeric:tabular-nums;border:1px solid #f3f4f6;color:{{ $row['saldo_kredit'] > 0 ? '#dc2626' : '#9ca3af' }};">
                            {{ $row['saldo_kredit'] > 0 ? number_format($row['saldo_kredit'], 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:16px;text-align:center;color:#9ca3af;font-style:italic;border:1px solid #f3f4f6;">
                            Tidak ada transaksi
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background:#f9fafb;border-top:2px solid #e5e7eb;">
                    <td colspan="3" style="padding:10px 14px;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;border:1px solid #e5e7eb;">
                        Saldo Akhir
                    </td>
                    <td colspan="2" style="border:1px solid #e5e7eb;"></td>
                    <td style="padding:10px 14px;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;border:1px solid #e5e7eb;color:{{ $item['saldo_akhir'] > 0 ? '#16a34a' : '#9ca3af' }};">
                        {{ $item['saldo_akhir'] > 0 ? number_format($item['saldo_akhir'], 0, ',', '.') : '-' }}
                    </td>
                    <td style="padding:10px 14px;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;border:1px solid #e5e7eb;color:{{ $item['saldo_akhir'] < 0 ? '#dc2626' : '#9ca3af' }};">
                        {{ $item['saldo_akhir'] < 0 ? number_format(abs($item['saldo_akhir']), 0, ',', '.') : '-' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
