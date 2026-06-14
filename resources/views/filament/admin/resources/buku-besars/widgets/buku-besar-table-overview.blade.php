<x-filament-widgets::widget>
    <x-filament::section>

<style>
    .bukubesar-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .bukubesar-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        color: white;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        margin-bottom: 24px;
    }
    
    .filter-grid {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .filter-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .filter-input, .filter-select {
        width: 100%;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }
    
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: #4338ca;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .filter-button {
        width: 100%;
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        color: white;
        padding: 11px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 27px;
    }
    
    .filter-button:hover {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.4);
        transform: translateY(-1px);
    }
    
    .bukubesar-table-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: 2px solid #d1d5db;
        margin-top: 24px;
    }
    
    .bukubesar-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        table-layout: auto;
    }
    
    .saldo-awal-row {
        background-color: #ffffff;
        font-weight: 700;
        font-size: 14px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .saldo-awal-row td {
        padding: 12px 16px;
        border-right: 1px solid #e5e7eb;
        color: #374151;
    }
    
    .bukubesar-table thead {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
    }
    
    .bukubesar-table th {
        padding: 14px 12px;
        text-align: center;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 13px;
        border-right: 1px solid rgba(255,255,255,0.2);
    }
    
    .bukubesar-table th:last-child {
        border-right: none;
    }
    
    .bukubesar-table tbody tr {
        background-color: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }
    
    .bukubesar-table tbody tr:hover {
        background-color: #eff6ff;
    }
    
    .bukubesar-table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }
    
    .bukubesar-table tbody tr:nth-child(even):hover {
        background-color: #eff6ff;
    }
    
    .bukubesar-table tbody td {
        padding: 10px 12px;
        border-right: 1px solid #e5e7eb;
    }
    
    .bukubesar-table tbody td:last-child {
        border-right: none;
    }
    
    .bukubesar-table tfoot tr {
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        font-weight: 700;
        border-top: 3px solid #9ca3af;
    }
    
    .bukubesar-table tfoot td {
        padding: 14px 16px;
        font-size: 15px;
        border-right: 1px solid #9ca3af;
    }
    
    .bukubesar-table tfoot td:last-child {
        border-right: none;
    }
    
    .saldo-akhir-row {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        font-weight: 700;
        font-size: 16px;
    }
    
    .saldo-akhir-row td {
        padding: 16px !important;
        border-right: 1px solid rgba(255,255,255,0.3) !important;
    }
    
    .account-info-badge {
        background: linear-gradient(135deg, #c7d2fe 0%, #bfdbfe 100%);
        border: 2px solid #818cf8;
        border-radius: 12px;
        padding: 16px 20px;
        margin-top: 20px;
        margin-bottom: 20px;
        font-weight: 600;
        color: #4f46e5;
        text-align: center;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
        font-style: italic;
    }
    
    .empty-state svg {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        opacity: 0.3;
    }
</style>

<!-- Filter Form -->
<form wire:submit.prevent="filterJurnal" class="filter-grid">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: end;">
        <div>
            <label for="periode_awal" class="filter-label">Periode Awal:</label>
            <input 
                type="month" 
                wire:model="periode_awal" 
                id="periode_awal" 
                class="filter-input"
            >
        </div>
        <div>
            <label for="periode_akhir" class="filter-label">Periode Akhir:</label>
            <input 
                type="month" 
                wire:model="periode_akhir" 
                id="periode_akhir" 
                class="filter-input"
            >
        </div>
        <div>
            <label for="id_akun" class="filter-label">Pilih Akun:</label>
            <select 
                wire:model="id_akun" 
                id="id_akun" 
                class="filter-select"
            >
                <option value="">-- Pilih Akun --</option>
                @foreach (\App\Models\Akun::orderBy('no_akun')->get() as $akun)
                    <option value="{{ $akun->no_akun }}">{{ $akun->no_akun }} - {{ $akun->nama_akun }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="filter-button">
                🔍 Filter Data
            </button>
        </div>
    </div>
</form>

{{-- HEADER --}}
<div class="bukubesar-header">
    <div style="font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
        Buku Besar
    </div>
    <div style="font-size: 22px; font-weight: 600; margin-bottom: 4px;">
        NUKUMA SOES
    </div>
    <div style="font-size: 14px; opacity: 0.95;">
        Periode 
        @if($periode_awal && $periode_akhir)
            {{ \Carbon\Carbon::createFromFormat('Y-m', $periode_awal)->translatedFormat('F Y') }}
            -
            {{ \Carbon\Carbon::createFromFormat('Y-m', $periode_akhir)->translatedFormat('F Y') }}
        @else
            {{ now()->translatedFormat('F Y') }}
        @endif
    </div>
</div>

{{-- INFO AKUN YANG DIPILIH --}}
@if($id_akun)
    <div class="account-info-badge">
        <svg style="width: 24px; height: 24px; display: inline-block; vertical-align: middle; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
        </svg>
        <strong>Akun Terpilih:</strong> 
        {{ \App\Models\Akun::where('no_akun', $id_akun)->first()?->no_akun }} - 
        {{ \App\Models\Akun::where('no_akun', $id_akun)->first()?->nama_akun }}
    </div>
@endif

{{-- TABEL BUKU BESAR --}}
<div class="bukubesar-table-container">
    <table class="bukubesar-table">
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 30%;">Keterangan</th>
                <th style="width: 13%;">Debit</th>
                <th style="width: 13%;">Kredit</th>
                <th colspan="2" style="width: 32%; text-align: center; border-bottom: 2px solid rgba(255,255,255,0.3);">Saldo</th>
            </tr>
            <tr>
                <th colspan="4" style="border-right: 1px solid rgba(255,255,255,0.2);"></th>
                <th style="width: 16%;">Debit</th>
                <th style="width: 16%;">Kredit</th>
            </tr>
        </thead>
        
        {{-- SALDO AWAL --}}
        <tbody>
            <tr class="saldo-awal-row">
                <td colspan="2" style="text-align: center; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">
                    Saldo Awal
                </td>
                <td style="text-align: center; font-family: monospace; font-size: 14px; color: #374151;">-</td>
                <td style="text-align: center; font-family: monospace; font-size: 14px; color: #374151;">-</td>
                @if($posisiSaldo === 'debit')
                    <td style="text-align: right; font-family: monospace; font-size: 14px; font-weight: 600; color: #374151;">
                        Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}
                    </td>
                    <td style="text-align: center; font-family: monospace; font-size: 14px; color: #374151;">-</td>
                @else
                    <td style="text-align: center; font-family: monospace; font-size: 14px; color: #374151;">-</td>
                    <td style="text-align: right; font-family: monospace; font-size: 14px; font-weight: 600; color: #374151;">
                        Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}
                    </td>
                @endif
            </tr>
            @php
                $akunDipilih = $id_akun ? \App\Models\Akun::where('no_akun', $id_akun)->first() : null;
                $idAkunDipilih = $akunDipilih?->id;
                $saldoBerjalan = $saldoAwal ?? 0;
            @endphp

            @forelse($jurnals as $jurnal)
                @php
                    $detailAkunIni = $idAkunDipilih 
                        ? $jurnal->jurnaldetail->where('no_akun', $idAkunDipilih)
                        : $jurnal->jurnaldetail;
                @endphp

                @foreach($detailAkunIni as $detail)
                    @php
                        // Hitung saldo berjalan
                        if ($posisiSaldo === 'debit') {
                            $saldoBerjalan = $saldoBerjalan + $detail->debit - $detail->credit;
                        } else {
                            $saldoBerjalan = $saldoBerjalan + $detail->credit - $detail->debit;
                        }
                        
                        // Tentukan posisi saldo (debit atau kredit)
                        $saldoDebit = 0;
                        $saldoKredit = 0;
                        
                        if ($saldoBerjalan >= 0) {
                            if ($posisiSaldo === 'debit') {
                                $saldoDebit = $saldoBerjalan;
                            } else {
                                $saldoKredit = $saldoBerjalan;
                            }
                        } else {
                            // Saldo abnormal (negatif)
                            if ($posisiSaldo === 'debit') {
                                $saldoKredit = abs($saldoBerjalan);
                            } else {
                                $saldoDebit = abs($saldoBerjalan);
                            }
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center; color: #374151;">
                            {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}
                        </td>
                        <td style="color: #374151;">
                            @php
                                // Ambil keterangan dari detail, jurnal, atau no_referensi
                                $keterangan = $detail->deskripsi ?: $jurnal->deskripsi;
                                
                                // Jika masih kosong atau "-", coba ambil dari no_referensi atau analisa akun
                                if (!$keterangan || $keterangan === '-') {
                                    if ($jurnal->no_referensi) {
                                        // Parse no_referensi untuk membuat keterangan yang lebih baik
                                        if (str_starts_with($jurnal->no_referensi, 'UJP-')) {
                                            $keterangan = 'Utang Jangka Panjang';
                                        } elseif (str_starts_with($jurnal->no_referensi, 'BAYAR-UJP-')) {
                                            $keterangan = 'Pembayaran Utang Jangka Panjang';
                                        } elseif (str_starts_with($jurnal->no_referensi, 'SALDO-AWAL-') || str_starts_with($jurnal->no_referensi, 'SA-')) {
                                            $keterangan = 'Saldo Awal';
                                        } elseif (str_starts_with($jurnal->no_referensi, 'FP-')) {
                                            $keterangan = 'Faktur Pembelian';
                                        } elseif (str_starts_with($jurnal->no_referensi, 'PENY-')) {
                                            $keterangan = 'Penyusutan Aset';
                                        } else {
                                            $keterangan = 'Ref: ' . $jurnal->no_referensi;
                                        }
                                    } else {
                                        // Tidak ada no_referensi, coba deteksi dari akun yang terlibat
                                        $allDetails = $jurnal->jurnaldetail;
                                        $akunIds = $allDetails->pluck('no_akun')->toArray();
                                        $akunNames = \App\Models\Akun::whereIn('id', $akunIds)->pluck('nama_akun', 'id')->toArray();
                                        
                                        // Deteksi berdasarkan nama akun
                                        $akunNamesLower = array_map('strtolower', $akunNames);
                                        $allAkunNames = implode(' ', $akunNamesLower);
                                        
                                        if (str_contains($allAkunNames, 'utang jangka panjang')) {
                                            // Cek apakah ini penerimaan utang atau pembayaran
                                            $utangAkun = collect($akunNames)->filter(fn($name) => str_contains(strtolower($name), 'utang jangka panjang'))->keys()->first();
                                            $utangDetail = $allDetails->where('no_akun', $utangAkun)->first();
                                            
                                            if ($utangDetail && $utangDetail->credit > 0) {
                                                $keterangan = 'Penerimaan Utang Jangka Panjang';
                                            } else {
                                                $keterangan = 'Pembayaran Utang Jangka Panjang';
                                            }
                                        } elseif (str_contains($allAkunNames, 'utang')) {
                                            $keterangan = 'Transaksi Utang';
                                        } elseif (str_contains($allAkunNames, 'piutang')) {
                                            $keterangan = 'Transaksi Piutang';
                                        } elseif (str_contains($allAkunNames, 'modal')) {
                                            $keterangan = 'Transaksi Modal';
                                        } else {
                                            $keterangan = 'Transaksi';
                                        }
                                    }
                                }
                            @endphp
                            {{ $keterangan }}
                        </td>
                        <td style="text-align: right; font-family: monospace; color: #374151; font-weight: 600;">
                            {{ $detail->debit ? 'Rp '.number_format($detail->debit, 0, ',', '.') : '-' }}
                        </td>
                        <td style="text-align: right; font-family: monospace; color: #374151; font-weight: 600;">
                            {{ $detail->credit ? 'Rp '.number_format($detail->credit, 0, ',', '.') : '-' }}
                        </td>
                        <td style="text-align: right; font-family: monospace; color: #374151; font-weight: 600;">
                            {{ $saldoDebit > 0 ? 'Rp '.number_format($saldoDebit, 0, ',', '.') : '-' }}
                        </td>
                        <td style="text-align: right; font-family: monospace; color: #374151; font-weight: 600;">
                            {{ $saldoKredit > 0 ? 'Rp '.number_format($saldoKredit, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                        </svg>
                        <div style="font-size: 16px; margin-bottom: 8px;">Tidak ada data</div>
                        <div style="font-size: 13px;">Tidak ada transaksi untuk periode dan akun yang dipilih</div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- FOOTER --}}
        <tfoot>
            @php
                // Ambil saldo akhir dari kolom saldo transaksi terakhir
                // Jika tidak ada transaksi, gunakan saldo awal
                $saldoAkhirDebit = 0;
                $saldoAkhirKredit = 0;
                
                if ($jurnals->isEmpty()) {
                    // Tidak ada transaksi, gunakan saldo awal
                    if ($posisiSaldo === 'debit') {
                        $saldoAkhirDebit = $saldoAwal ?? 0;
                    } else {
                        $saldoAkhirKredit = $saldoAwal ?? 0;
                    }
                } else {
                    // Ambil dari saldo berjalan terakhir (sudah dihitung di loop tbody)
                    if ($saldoBerjalan >= 0) {
                        if ($posisiSaldo === 'debit') {
                            $saldoAkhirDebit = $saldoBerjalan;
                        } else {
                            $saldoAkhirKredit = $saldoBerjalan;
                        }
                    } else {
                        // Saldo abnormal (negatif)
                        if ($posisiSaldo === 'debit') {
                            $saldoAkhirKredit = abs($saldoBerjalan);
                        } else {
                            $saldoAkhirDebit = abs($saldoBerjalan);
                        }
                    }
                }
                
                $isAbnormal = $saldoBerjalan < 0;
            @endphp
            <tr class="saldo-akhir-row">
                <td colspan="2" style="text-align: center; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">
                    Saldo Akhir
                    @if($isAbnormal)
                        <span style="font-size: 11px; font-weight: 400; opacity: 0.9;">(Abnormal)</span>
                    @endif
                </td>
                <td colspan="2" style="text-align: center;">-</td>
                <td style="text-align: right; font-family: monospace; font-size: 17px; font-weight: 700;">
                    {{ $saldoAkhirDebit > 0 ? 'Rp '.number_format($saldoAkhirDebit, 0, ',', '.') : '-' }}
                </td>
                <td style="text-align: right; font-family: monospace; font-size: 17px; font-weight: 700;">
                    {{ $saldoAkhirKredit > 0 ? 'Rp '.number_format($saldoAkhirKredit, 0, ',', '.') : '-' }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- FOOTER INFO --}}
<div style="margin-top: 20px; padding: 16px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; border: 2px solid #bae6fd;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 13px; color: #0369a1;">
            <strong>Total Transaksi:</strong> {{ $jurnals->sum(fn($j) => $j->jurnaldetail->where('no_akun', $idAkunDipilih)->count()) }} entri
        </div>
        <div style="text-align: right;">
            <div style="font-size: 11px; font-weight: 600; color: #0c4a6e; text-transform: uppercase; letter-spacing: 0.5px;">
                Dilihat pada
            </div>
            <div style="font-size: 13px; font-weight: bold; color: #0369a1;">
                {{ now()->translatedFormat('d F Y, H:i') }} WIB
            </div>
        </div>
    </div>
</div>

    </x-filament::section>
</x-filament-widgets::widget>