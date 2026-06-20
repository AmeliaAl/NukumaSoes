<x-filament-panels::page>

<style>
    [x-cloak] { display: none !important; }
    
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

{{-- Modal Penyusutan Per Tahun --}}
@if($showYearlyModal)
<div 
    x-data="{ show: @entangle('showYearlyModal') }"
    x-show="show"
    x-cloak
    style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);"
    @click.self="$wire.closeYearlyModal()"
>
    <div style="background: white; border-radius: 16px; max-width: 900px; width: 90%; max-height: 90vh; overflow: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        
        {{-- Modal Header --}}
        <div style="background: linear-gradient(135deg, #1b1c4dff 0%, #1b1c4dff 100%); padding: 24px; border-radius: 16px 16px 0 0; position: sticky; top: 0; z-index: 10;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h2 style="color: white; font-size: 20px; font-weight: 700; margin: 0 0 4px;">
                        Penyusutan Per Tahun
                    </h2>
                    @if($selectedAsetForYearly)
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin: 0;">
                        {{ $selectedAsetForYearly->nama_aset }} ({{ $selectedAsetForYearly->kode_aset }})
                    </p>
                    @endif
                </div>
                <button 
                    wire:click="closeYearlyModal"
                    style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 8px; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.1)'"
                >
                    <svg style="width: 20px; height: 20px; color: white;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div style="padding: 24px;">
            @if(count($yearlyData) > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f3f4f6; border-bottom: 2px solid #d1d5db;">
                            <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Tahun</th>
                            <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #374151;">Jumlah Bulan</th>
                            <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #374151;">Penyusutan</th>
                            <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #374151;">Akumulasi Penyusutan</th>
                            <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #374151;">Nilai Buku</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Baris HP (Harga Perolehan) --}}
                        @if($selectedAsetForYearly)
                        <tr style="background: #fef3c7; border-bottom: 2px solid #fbbf24;">
                            <td style="padding: 12px 16px;">
                                <span style="background: #fbbf24; color: white; padding: 6px 14px; border-radius: 8px; font-size: 14px; font-weight: 700; display: inline-block;">
                                    HP
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center; color: #92400e; font-weight: 600;">
                                -
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: #92400e; font-weight: 600;">
                                -
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: #92400e; font-weight: 600;">
                                -
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: #16a34a; font-weight: 700;">
                                Rp {{ number_format($selectedAsetForYearly->nilai_perolehan, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        
                        @foreach($yearlyData as $data)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            <td style="padding: 12px 16px;">
                                <span style="background: #e0e7ff; color: #4f46e5; padding: 6px 14px; border-radius: 8px; font-size: 14px; font-weight: 700; display: inline-block;">
                                    {{ $data['year'] }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center; color: #6b7280; font-weight: 500;">
                                {{ $data['bulan_count'] }} bulan
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: #dc2626; font-weight: 600;">
                                Rp {{ number_format($data['penyusutan_tahun'], 0, ',', '.') }}
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: #ea580c; font-weight: 600;">
                                Rp {{ number_format($data['akumulasi_akhir'], 0, ',', '.') }}
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: #16a34a; font-weight: 700;">
                                Rp {{ number_format($data['nilai_buku_akhir'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="text-align: center; padding: 40px; color: #6b7280;">
                <p>Tidak ada data penyusutan per tahun</p>
            </div>
            @endif
        </div>

        {{-- Modal Footer --}}
        <div style="background: #f9fafb; padding: 16px 24px; border-radius: 0 0 16px 16px; display: flex; justify-content: flex-end;">
            <button 
                wire:click="closeYearlyModal"
                style="background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.background='#4338ca'"
                onmouseout="this.style.background='#4f46e5'"
            >
                Tutup
            </button>
        </div>

    </div>
</div>
@endif

</x-filament-panels::page>
