<x-filament-panels::page>

<style>
    .filter-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .filter-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .filter-select {
        width: 100%;
        max-width: 400px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .filter-select:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .penyusutan-table {
        background: white;
        border: 2px solid #d1d5db;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .penyusutan-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .penyusutan-table thead {
        background: linear-gradient(135deg, #1b1c4dff 0%, #1b1c4dff 100%);
        color: white;
    }
    
    .penyusutan-table th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        border: 1px solid #120f43ff;
    }
    
    .penyusutan-table th.text-right {
        text-align: right;
    }
    
    .penyusutan-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }
    
    .penyusutan-table tbody tr:hover {
        background-color: #f3f4f6;
    }
    
    .penyusutan-table td {
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        color: #374151;
    }
    
    .penyusutan-table td.text-right {
        text-align: right;
        font-weight: 500;
    }
</style>

{{-- Filter Dropdown --}}
<div class="filter-card">
    <label class="filter-label">Pilih Aset</label>
    <select wire:model.live="selectedAset" class="filter-select">
        <option value="">-- Pilih Aset --</option>
        @foreach($daftarAset as $aset)
            <option value="{{ $aset->id }}">{{ $aset->nama_aset }}</option>
        @endforeach
    </select>
</div>

{{-- Kartu Header Aset --}}
@if($selectedAset)
    @php
        $aset = \App\Models\Aset::with('kategori_aset')->find($selectedAset);
    @endphp
    
    @if($aset)
        @include('filament.header.kartu-penyusutan', ['aset' => $aset])
    @endif
@endif

{{-- Tabel Penyusutan --}}
@if($selectedAset && count($penyusutans) > 0)
    <div class="penyusutan-table">
        <table>
            <thead>
                <tr>
                    <th>Periode</th>
                    <th class="text-right">Beban Penyusutan</th>
                    <th class="text-right">Akumulasi Penyusutan</th>
                    <th class="text-right">Nilai Buku</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penyusutans as $p)
                    <tr>
                        <td>
                            <span style="background: #e0e7ff; color: #4f46e5; padding: 4px 12px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                            </span>
                        </td>
                        <td class="text-right" style="color: #000000ff; font-weight: 600;">
                            Rp {{ number_format($p->beban_penyusutan, 0, ',', '.') }}
                        </td>
                        <td class="text-right" style="color: #000000ff; font-weight: 600;">
                            Rp {{ number_format($p->akumulasi_penyusutan, 0, ',', '.') }}
                        </td>
                        <td class="text-right" style="color: #000000ff; font-weight: 700;">
                            Rp {{ number_format($p->nilai_buku, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@elseif($selectedAset && count($penyusutans) == 0)
    <div style="background: #fef3c7; border: 2px solid #fbbf24; border-radius: 12px; padding: 24px; text-align: center;">
        <p style="font-size: 14px; color: #92400e; margin: 0;">Belum ada data penyusutan untuk aset ini</p>
    </div>
@else
    @include('filament.header.kartu-penyusutan-empty')
@endif

</x-filament-panels::page>
