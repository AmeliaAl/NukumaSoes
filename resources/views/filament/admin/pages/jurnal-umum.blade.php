<x-filament-panels::page>

    {{-- Filter --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;margin-bottom:24px;display:grid;grid-template-columns:1fr 1fr auto;gap:16px;align-items:flex-end;">
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Dari Tanggal</label>
            <input type="date" wire:model="inputDari"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box;" />
        </div>
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Sampai Tanggal</label>
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
        $grouped     = $this->getData();
        $totalDebit  = $grouped->sum(fn ($g) => $g['baris']->sum('debit'));
        $totalKredit = $grouped->sum(fn ($g) => $g['baris']->sum('kredit'));
    @endphp

    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">

        {{-- Header SAK — lebih besar & berwarna --}}
        <div style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);padding:28px 32px;text-align:center;">
            <p style="font-size:20px;font-weight:800;color:#ffffff;letter-spacing:0.5px;margin:0;">Nukuma Soes</p>
            <p style="font-size:16px;font-weight:600;color:#e0e7ff;margin:4px 0 0;">Jurnal Umum</p>
            @if ($dari || $sampai)
                <p style="font-size:13px;color:#c7d2fe;margin:6px 0 0;">
                    Periode:
                    {{ $dari ? \Carbon\Carbon::parse($dari)->translatedformat('d/m/Y') : '...' }}
                    s/d
                    {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedformat('d/m/Y') : '...' }}
                </p>
            @else
                <p style="font-size:13px;color:#c7d2fe;margin:6px 0 0;">Semua Periode</p>
            @endif
        </div>

        {{-- Tabel --}}
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;background:#fff;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#1e293b;border-bottom:2px solid #6366f1;width:110px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;">Tanggal</th>
                        <th style="padding:12px 16px;text-align:left;font-weight:700;color:#1e293b;border-bottom:2px solid #6366f1;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;">Nama Akun</th>
                        <th style="padding:12px 16px;text-align:center;font-weight:700;color:#1e293b;border-bottom:2px solid #6366f1;width:100px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;">Ref</th>
                        <th style="padding:12px 16px;text-align:right;font-weight:700;color:#1e293b;border-bottom:2px solid #6366f1;width:150px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;">Debit</th>
                        <th style="padding:12px 16px;text-align:right;font-weight:700;color:#1e293b;border-bottom:2px solid #6366f1;width:150px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grouped as $jurnal)
                        @foreach ($jurnal['baris'] as $idx => $row)
                            @php
                                $isKredit = $row->kredit > 0;
                                $namaAkun = optional($row->akun)->nama_akun ?? '-';
                                $kodeAkun = optional($row->akun)->kode_akun ?? '-';
                                $isFirst  = $idx === 0;
                            @endphp
                            <tr style="border-bottom:1px solid #f1f5f9;{{ $isFirst ? 'border-top:2px solid #e2e8f0;' : '' }}">
                                {{-- Tanggal hanya di baris pertama setiap jurnal --}}
                                <td style="padding:9px 16px;color:#64748b;font-family:monospace;font-size:12px;white-space:nowrap;vertical-align:top;">
                                    @if ($isFirst)
                                        <span style="background:#ede9fe;color:#5b21b6;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px;">
                                            {{ $jurnal['tanggal'] ? \Carbon\Carbon::parse($jurnal['tanggal'])->format('d/m/Y') : '-' }}
                                        </span>
                                        @if ($jurnal['no_jurnal'])
                                            <div style="margin-top:4px;font-size:10px;color:#94a3b8;font-weight:400;font-family:monospace;">
                                                {{ $jurnal['no_jurnal'] }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td style="padding:9px 16px;color:#1e293b;">
                                    @if ($isKredit)
                                        <span style="padding-left:36px;color:#475569;">{{ $namaAkun }}</span>
                                    @else
                                        <span style="font-weight:500;">{{ $namaAkun }}</span>
                                    @endif
                                </td>
                                <td style="padding:9px 16px;text-align:center;color:#94a3b8;font-family:monospace;font-size:11px;">
                                    <span style="background:#f8fafc;border:1px solid #e2e8f0;padding:1px 6px;border-radius:4px;">{{ $kodeAkun }}</span>
                                </td>
                                <td style="padding:9px 16px;text-align:right;font-variant-numeric:tabular-nums;color:#1e293b;">
                                    @if ($row->debit > 0)
                                        <span style="font-weight:600;">{{ number_format($row->debit, 0, ',', '.') }}</span>
                                    @else
                                        <span style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td style="padding:9px 16px;text-align:right;font-variant-numeric:tabular-nums;color:#1e293b;">
                                    @if ($row->kredit > 0)
                                        <span style="font-weight:600;">{{ number_format($row->kredit, 0, ',', '.') }}</span>
                                    @else
                                        <span style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">
                                Tidak ada data jurnal ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);">
                        <td colspan="3" style="padding:12px 16px;font-weight:700;color:#ffffff;text-align:center;font-size:13px;letter-spacing:0.05em;">
                            JUMLAH
                        </td>
                        <td style="padding:12px 16px;text-align:right;font-weight:700;color:#ffffff;font-variant-numeric:tabular-nums;font-size:14px;">
                            {{ number_format($totalDebit, 0, ',', '.') }}
                        </td>
                        <td style="padding:12px 16px;text-align:right;font-weight:700;color:#ffffff;font-variant-numeric:tabular-nums;font-size:14px;">
                            {{ number_format($totalKredit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

</x-filament-panels::page>
