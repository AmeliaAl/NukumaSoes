<x-filament-panels::page>

    {{-- Filter --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;margin-bottom:24px;display:grid;grid-template-columns:1fr 1fr auto;gap:16px;align-items:flex-end;">
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Tanggal Dari</label>
            <input type="date" wire:model="inputDari"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box;" />
        </div>
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Tanggal Sampai</label>
            <input type="date" wire:model="inputSampai"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box;" />
        </div>
        <div style="display:flex;gap:8px;">
            <button wire:click="applyFilter"
                style="padding:8px 18px;background:#f59e0b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">
                Filter
            </button>
            <button wire:click="resetFilter"
                style="padding:8px 14px;background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;white-space:nowrap;">
                Reset
            </button>
        </div>
    </div>

    @php
        $pelangganData = $this->getDataPelanggan();
        $mitraData     = $this->getDataMitra();
        $allEmpty      = $pelangganData->isEmpty() && $mitraData->isEmpty();
    @endphp

    @if ($allEmpty)
        <div style="text-align:center;padding:48px;color:#9ca3af;background:#fff;border:1px solid #e5e7eb;border-radius:12px;">
            Tidak ada data piutang ditemukan.
        </div>
    @else

        {{-- ── PELANGGAN ── --}}
        @if ($pelangganData->isNotEmpty())
            <div style="margin-bottom:16px;">
                <h2 style="font-size:15px;font-weight:700;color:#374151;padding-bottom:6px;border-bottom:2px solid #f59e0b;display:inline-block;">
                    Piutang Pelanggan (Non-Konsinyasi)
                </h2>
            </div>

            @foreach ($pelangganData as $item)
                <div style="background:#fff;border:1px solid #d1d5db;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                    <div style="padding:12px 20px;background:#f9fafb;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <span style="font-size:12px;color:#6b7280;font-weight:500;">Nama : </span>
                            <span style="font-size:14px;font-weight:700;color:#111827;">{{ $item['nama'] }}</span>
                        </div>
                        <div>
                            <span style="font-size:12px;color:#6b7280;font-weight:500;">Kode Pelanggan : </span>
                            <span style="font-size:13px;font-weight:600;font-family:monospace;color:#92400e;background:#fef3c7;padding:2px 8px;border-radius:6px;">{{ $item['kode'] }}</span>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;">
                            <thead>
                                <tr style="background:#f3f4f6;">
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:100px;">Tanggal</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;">Deskripsi</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:130px;">Ref</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:130px;">Debit</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:130px;">Kredit</th>
                                    <th colspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;">Balance</th>
                                </tr>
                                <tr style="background:#f3f4f6;">
                                    <th style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:120px;">Debit</th>
                                    <th style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:120px;">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($item['rows'] as $i => $row)
                                    <tr style="background:{{ $i % 2 === 0 ? '#fff' : '#fafafa' }};">
                                        <td style="padding:9px 14px;color:#6b7280;font-family:monospace;font-size:12px;white-space:nowrap;border:1px solid #f3f4f6;text-align:center;">
                                            {{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}
                                        </td>
                                        <td style="padding:9px 14px;color:#374151;border:1px solid #f3f4f6;">{{ $row['keterangan'] }}</td>
                                        <td style="padding:9px 14px;color:#9ca3af;font-family:monospace;font-size:12px;white-space:nowrap;border:1px solid #f3f4f6;text-align:center;">{{ $row['ref'] }}</td>
                                        <td style="padding:9px 14px;text-align:right;color:#374151;border:1px solid #f3f4f6;">{{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '—' }}</td>
                                        <td style="padding:9px 14px;text-align:right;color:#374151;border:1px solid #f3f4f6;">{{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '—' }}</td>
                                        <td style="padding:9px 14px;text-align:right;border:1px solid #f3f4f6;color:{{ $row['saldo_debit'] > 0 ? '#111827' : '#9ca3af' }};">{{ $row['saldo_debit'] > 0 ? number_format($row['saldo_debit'], 0, ',', '.') : '-' }}</td>
                                        <td style="padding:9px 14px;text-align:right;border:1px solid #f3f4f6;color:{{ $row['saldo_kredit'] > 0 ? '#dc2626' : '#9ca3af' }};">{{ $row['saldo_kredit'] > 0 ? number_format($row['saldo_kredit'], 0, ',', '.') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" style="padding:16px;text-align:center;color:#9ca3af;font-style:italic;border:1px solid #f3f4f6;">Tidak ada transaksi</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="background:#f9fafb;border-top:2px solid #e5e7eb;">
                                    <td colspan="3" style="padding:10px 14px;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;border:1px solid #e5e7eb;">Saldo Akhir</td>
                                    <td colspan="2" style="border:1px solid #e5e7eb;"></td>
                                    <td style="padding:10px 14px;text-align:right;font-weight:700;border:1px solid #e5e7eb;color:{{ $item['saldo_akhir'] > 0 ? '#16a34a' : '#9ca3af' }};">{{ $item['saldo_akhir'] > 0 ? number_format($item['saldo_akhir'], 0, ',', '.') : '-' }}</td>
                                    <td style="padding:10px 14px;text-align:right;font-weight:700;border:1px solid #e5e7eb;color:{{ $item['saldo_akhir'] < 0 ? '#dc2626' : '#9ca3af' }};">{{ $item['saldo_akhir'] < 0 ? number_format(abs($item['saldo_akhir']), 0, ',', '.') : '-' }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- ── MITRA ── --}}
        @if ($mitraData->isNotEmpty())
            <div style="margin-bottom:16px;margin-top:32px;">
                <h2 style="font-size:15px;font-weight:700;color:#374151;padding-bottom:6px;border-bottom:2px solid #f59e0b;display:inline-block;">
                    Piutang Mitra (Konsinyasi)
                </h2>
            </div>

            @foreach ($mitraData as $item)
                <div style="background:#fff;border:1px solid #d1d5db;border-radius:12px;margin-bottom:24px;overflow:hidden;">
                    <div style="padding:12px 20px;background:#f9fafb;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <span style="font-size:12px;color:#6b7280;font-weight:500;">Nama : </span>
                            <span style="font-size:14px;font-weight:700;color:#111827;">{{ $item['nama'] }}</span>
                        </div>
                        <div>
                            <span style="font-size:12px;color:#6b7280;font-weight:500;">Kode Mitra : </span>
                            <span style="font-size:13px;font-weight:600;font-family:monospace;color:#92400e;background:#fef3c7;padding:2px 8px;border-radius:6px;">{{ $item['kode'] }}</span>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;">
                            <thead>
                                <tr style="background:#f3f4f6;">
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:100px;">Tanggal</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;">Deskripsi</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:130px;">Ref</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:130px;">Debit</th>
                                    <th rowspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:130px;">Kredit</th>
                                    <th colspan="2" style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;">Balance</th>
                                </tr>
                                <tr style="background:#f3f4f6;">
                                    <th style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:120px;">Debit</th>
                                    <th style="padding:8px 14px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;border:1px solid #e5e7eb;width:120px;">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($item['rows'] as $i => $row)
                                    <tr style="background:{{ $i % 2 === 0 ? '#fff' : '#fafafa' }};">
                                        <td style="padding:9px 14px;color:#6b7280;font-family:monospace;font-size:12px;white-space:nowrap;border:1px solid #f3f4f6;text-align:center;">
                                            {{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}
                                        </td>
                                        <td style="padding:9px 14px;color:#374151;border:1px solid #f3f4f6;">{{ $row['keterangan'] }}</td>
                                        <td style="padding:9px 14px;color:#9ca3af;font-family:monospace;font-size:12px;white-space:nowrap;border:1px solid #f3f4f6;text-align:center;">{{ $row['ref'] }}</td>
                                        <td style="padding:9px 14px;text-align:right;color:#374151;border:1px solid #f3f4f6;">{{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '—' }}</td>
                                        <td style="padding:9px 14px;text-align:right;color:#374151;border:1px solid #f3f4f6;">{{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '—' }}</td>
                                        <td style="padding:9px 14px;text-align:right;border:1px solid #f3f4f6;color:{{ $row['saldo_debit'] > 0 ? '#111827' : '#9ca3af' }};">{{ $row['saldo_debit'] > 0 ? number_format($row['saldo_debit'], 0, ',', '.') : '-' }}</td>
                                        <td style="padding:9px 14px;text-align:right;border:1px solid #f3f4f6;color:{{ $row['saldo_kredit'] > 0 ? '#dc2626' : '#9ca3af' }};">{{ $row['saldo_kredit'] > 0 ? number_format($row['saldo_kredit'], 0, ',', '.') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" style="padding:16px;text-align:center;color:#9ca3af;font-style:italic;border:1px solid #f3f4f6;">Tidak ada transaksi</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="background:#f9fafb;border-top:2px solid #e5e7eb;">
                                    <td colspan="3" style="padding:10px 14px;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;border:1px solid #e5e7eb;">Saldo Akhir</td>
                                    <td colspan="2" style="border:1px solid #e5e7eb;"></td>
                                    <td style="padding:10px 14px;text-align:right;font-weight:700;border:1px solid #e5e7eb;color:{{ $item['saldo_akhir'] > 0 ? '#16a34a' : '#9ca3af' }};">{{ $item['saldo_akhir'] > 0 ? number_format($item['saldo_akhir'], 0, ',', '.') : '-' }}</td>
                                    <td style="padding:10px 14px;text-align:right;font-weight:700;border:1px solid #e5e7eb;color:{{ $item['saldo_akhir'] < 0 ? '#dc2626' : '#9ca3af' }};">{{ $item['saldo_akhir'] < 0 ? number_format(abs($item['saldo_akhir']), 0, ',', '.') : '-' }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif

    @endif

</x-filament-panels::page>
