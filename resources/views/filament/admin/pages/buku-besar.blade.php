<x-filament-panels::page>

    {{-- Filter --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;margin-bottom:24px;display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:16px;align-items:flex-end;">
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
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Nama Akun</label>
            <select wire:model.live="filterAkunId"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box;cursor:pointer;">
                <option value="">— Semua Akun —</option>
                @foreach ($this->getAkunOptions() as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>
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

    @php $data = $this->getBukuBesarData(); @endphp

    @if ($data->isEmpty())
        <div style="text-align:center;padding:48px;color:#9ca3af;background:#fff;border:1px solid #e5e7eb;border-radius:12px;">
            Tidak ada data jurnal ditemukan.
        </div>
    @else
        @foreach ($data as $akun)
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:24px;overflow:hidden;">

                {{-- Header Akun --}}
                <div style="padding:14px 20px;background:#f9fafb;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:600;font-family:monospace;padding:2px 8px;border-radius:6px;letter-spacing:0.05em;">
                            {{ $akun['kode_akun'] }}
                        </span>
                        <span style="font-size:14px;font-weight:600;color:#111827;">
                            {{ $akun['nama_akun'] }}
                        </span>
                    </div>
                    {{-- Tampilkan Saldo Awal di sini --}}
                    @if ($akun['saldo_awal'] != 0)
                        <div style="display:flex;align-items:center;gap:8px;background:#fefce8;padding:6px 12px;border-radius:6px;border:1px solid #fde047;">
                            <span style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Saldo Awal:</span>
                            <span style="font-size:13px;font-weight:700;color:{{ $akun['saldo_awal'] < 0 ? '#dc2626' : '#111827' }};">
                                {{ number_format(abs($akun['saldo_awal']), 0, ',', '.') }}
                                @if($akun['saldo_awal'] < 0)
                                    <span style="font-size:11px;font-weight:400;"> ({{ $akun['is_debit_normal'] ? 'K' : 'D' }})</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Tabel --}}
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f3f4f6;">
                                <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;width:100px;">Tgl.</th>
                                <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Keterangan</th>
                                <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;width:140px;">Ref</th>
                                <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;width:120px;">Debet</th>
                                <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;width:120px;">Kredit</th>
                                <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;width:130px;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- TIDAK ADA LAGI SALDO AWAL ROW DI SINI --}}

                            @foreach ($akun['rows'] as $i => $row)
                                @php $isSaldoAwal = $row['is_saldo_awal'] ?? false; @endphp
                                <tr style="border-top:1px solid #f3f4f6;background:{{ $isSaldoAwal ? '#fefce8' : ($i % 2 === 0 ? '#fff' : '#fafafa') }};">
                                    <td style="padding:10px 16px;color:#6b7280;font-family:monospace;font-size:12px;white-space:nowrap;">
                                        {{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}
                                    </td>
                                    <td style="padding:10px 16px;color:{{ $isSaldoAwal ? '#92400e' : '#374151' }};font-weight:{{ $isSaldoAwal ? '600' : 'normal' }};">
                                        {{ $row['keterangan'] ?? '-' }}
                                    </td>
                                    <td style="padding:10px 16px;color:#9ca3af;font-family:monospace;font-size:12px;white-space:nowrap;">
                                        {{ $row['ref'] ?? '-' }}
                                    </td>
                                    <td style="padding:10px 16px;text-align:right;color:#374151;font-variant-numeric:tabular-nums;">
                                        {{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td style="padding:10px 16px;text-align:right;color:#374151;font-variant-numeric:tabular-nums;">
                                        {{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '—' }}
                                    </td>
                                    <td style="padding:10px 16px;text-align:right;font-weight:500;font-variant-numeric:tabular-nums;color:{{ $row['saldo'] < 0 ? '#dc2626' : '#111827' }};">
                                        {{ number_format(abs($row['saldo']), 0, ',', '.') }}
                                        @if($row['saldo'] < 0)
                                            <span style="font-size:11px;font-weight:400;"> ({{ $akun['is_debit_normal'] ? 'K' : 'D' }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                                <td colspan="3" style="padding:10px 16px;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Saldo Akhir</td>
                                <td style="padding:10px 16px;text-align:right;font-weight:600;color:#111827;font-variant-numeric:tabular-nums;">
                                    {{ number_format($akun['total_debit'], 0, ',', '.') }}
                                </td>
                                <td style="padding:10px 16px;text-align:right;font-weight:600;color:#111827;font-variant-numeric:tabular-nums;">
                                    {{ number_format($akun['total_kredit'], 0, ',', '.') }}
                                </td>
                                <td style="padding:10px 16px;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;color:{{ $akun['saldo_akhir'] < 0 ? '#dc2626' : '#16a34a' }};">
                                    {{ number_format(abs($akun['saldo_akhir']), 0, ',', '.') }}
                                    @if($akun['saldo_akhir'] < 0)
                                        <span style="font-size:11px;font-weight:400;"> ({{ $akun['is_debit_normal'] ? 'K' : 'D' }})</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        @endforeach
    @endif

</x-filament-panels::page>
