<x-filament-panels::page>

    {{-- Filter --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;margin-bottom:24px;display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:16px;align-items:flex-end;">
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Dari Tanggal </label>
            <input type="date" wire:model="tanggalDari"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box;" />
        </div>
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Sampai Tanggal </label>
            <input type="date" wire:model="tanggalSampai"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box;" />
        </div>
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Status Pembayaran</label>
            <select wire:model="status"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box;">
                <option value="semua">Semua</option>
                <option value="lunas">Lunas</option>
                <option value="belum_lunas">Belum Lunas</option>
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

    {{-- Summary Cards --}}
    @if($this->laporanData->count() > 0)
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;">
                <div style="font-size:12px;color:#6b7280;margin-bottom:8px;font-weight:500;">Total Subtotal</div>
                <div style="font-size:24px;font-weight:700;color:#111827;">
                    Rp {{ number_format($this->getTotalSubtotal(), 0, ',', '.') }}
                </div>
            </div>
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;">
                <div style="font-size:12px;color:#6b7280;margin-bottom:8px;font-weight:500;">Total Diskon</div>
                <div style="font-size:24px;font-weight:700;color:#dc2626;">
                    Rp {{ number_format($this->getTotalDiskon(), 0, ',', '.') }}
                </div>
            </div>
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;">
                <div style="font-size:12px;color:#6b7280;margin-bottom:8px;font-weight:500;">Total Penjualan</div>
                <div style="font-size:24px;font-weight:700;color:#16a34a;">
                    Rp {{ number_format($this->getTotalAkhir(), 0, ',', '.') }}
                </div>
            </div>
        </div>
    @endif

    {{-- Tabel Laporan --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f3f4f6;">
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Tanggal</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Ref</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Pelanggan/Mitra</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Kode</th>
                        <th style="padding:10px 16px;text-align:left;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">Nama Barang</th>
                        <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Harga</th>
                        <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Qty</th>
                        <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Subtotal</th>
                        <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Diskon</th>
                        <th style="padding:10px 16px;text-align:right;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Total</th>
                        <th style="padding:10px 16px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Jenis</th>
                        <th style="padding:10px 16px;text-align:center;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->laporanData as $i => $item)
                        <tr style="border-top:1px solid #f3f4f6;background:{{ $i % 2 === 0 ? '#fff' : '#fafafa' }};">
                            <td style="padding:10px 16px;color:#6b7280;font-family:monospace;font-size:12px;white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td style="padding:10px 16px;color:#9ca3af;font-family:monospace;font-size:12px;white-space:nowrap;">
                                {{ $item->ref }}
                            </td>
                            <td style="padding:10px 16px;color:#374151;">
                                {{ $item->pelanggan_mitra }}
                            </td>
                            <td style="padding:10px 16px;color:#374151;font-family:monospace;font-size:12px;white-space:nowrap;">
                                {{ $item->kode_barang }}
                            </td>
                            <td style="padding:10px 16px;color:#374151;">
                                {{ $item->nama_barang }}
                            </td>
                            <td style="padding:10px 16px;text-align:right;color:#374151;font-variant-numeric:tabular-nums;">
                                {{ number_format($item->harga, 0, ',', '.') }}
                            </td>
                            <td style="padding:10px 16px;text-align:right;color:#374151;font-variant-numeric:tabular-nums;">
                                {{ $item->kuantitas }}
                            </td>
                            <td style="padding:10px 16px;text-align:right;color:#374151;font-variant-numeric:tabular-nums;">
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td style="padding:10px 16px;text-align:right;color:#dc2626;font-variant-numeric:tabular-nums;">
                                {{ $item->diskon > 0 ? number_format($item->diskon, 0, ',', '.') : '—' }}
                            </td>
                            <td style="padding:10px 16px;text-align:right;font-weight:600;color:#111827;font-variant-numeric:tabular-nums;">
                                {{ number_format($item->total, 0, ',', '.') }}
                            </td>
                            <td style="padding:10px 16px;text-align:center;">
                                <span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;
                                    {{ $item->jenis === 'Konsinyasi' ? 'background:#dbeafe;color:#1e40af;' : 'background:#f3e8ff;color:#7c3aed;' }}">
                                    {{ $item->jenis }}
                                </span>
                            </td>
                            <td style="padding:10px 16px;text-align:center;">
                                <span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;
                                    {{ $item->status_pembayaran === 'LUNAS' ? 'background:#dcfce7;color:#166534;' : 'background:#fef3c7;color:#92400e;' }}">
                                    {{ $item->status_pembayaran }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="padding:48px;text-align:center;color:#9ca3af;">
                                Tidak ada data untuk periode yang dipilih
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($this->laporanData->count() > 0)
                    <tfoot>
                        <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                            <td colspan="7" style="padding:10px 16px;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;text-align:right;">TOTAL:</td>
                            <td style="padding:10px 16px;text-align:right;font-weight:600;color:#111827;font-variant-numeric:tabular-nums;">
                                {{ number_format($this->getTotalSubtotal(), 0, ',', '.') }}
                            </td>
                            <td style="padding:10px 16px;text-align:right;font-weight:600;color:#dc2626;font-variant-numeric:tabular-nums;">
                                {{ number_format($this->getTotalDiskon(), 0, ',', '.') }}
                            </td>
                            <td style="padding:10px 16px;text-align:right;font-weight:700;color:#16a34a;font-variant-numeric:tabular-nums;">
                                {{ number_format($this->getTotalAkhir(), 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

</x-filament-panels::page>
