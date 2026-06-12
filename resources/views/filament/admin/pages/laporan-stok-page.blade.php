<x-filament-panels::page>

<style>
    .laporan-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .laporan-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 16px;
        margin-bottom: 24px;
    }
    
    .laporan-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border-radius: 12px;
        padding: 32px 24px;
        text-align: center;
        color: white;
        box-shadow: 0 4px 6px rgba(139, 92, 246, 0.3);
        margin-bottom: 24px;
    }
    
    .laporan-table {
        border: 2px solid #d1d5db;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        background: white;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    thead {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
    }
    
    th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        border: 1px solid #4f46e5;
        white-space: nowrap;
    }
    
    th.text-right {
        text-align: right;
    }
    
    th.text-center {
        text-align: center;
    }
    
    tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }
    
    tbody tr:hover {
        background-color: #f3f4f6;
    }
    
    tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }
    
    tbody tr:nth-child(even):hover {
        background-color: #f3f4f6;
    }
    
    td {
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        color: #374151;
    }
    
    td.text-right {
        text-align: right;
        font-weight: 500;
    }
    
    td.text-center {
        text-align: center;
    }
    
    .stok-warning {
        color: #dc2626;
        font-weight: 600;
    }
    
    .stok-normal {
        color: #059669;
        font-weight: 600;
    }
    
    .period-input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .period-input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }
    
    .summary-card {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        border: 2px solid #818cf8;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    
    .summary-item {
        text-align: center;
    }
    
    .summary-label {
        font-size: 12px;
        color: #4338ca;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    
    .summary-value {
        font-size: 24px;
        font-weight: bold;
        color: #4f46e5;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .laporan-header {
            padding: 24px 16px;
        }
        
        .laporan-card {
            padding: 12px;
        }
        
        table {
            font-size: 12px;
        }
        
        th, td {
            padding: 8px 10px;
        }
        
        .summary-value {
            font-size: 20px;
        }
    }
    
    /* Print Styles */
    @media print {
        /* Hide Filament UI elements */
        aside,
        nav,
        header,
        .fi-sidebar,
        .fi-topbar,
        .fi-header,
        .fi-breadcrumbs,
        [data-sidebar],
        [role="navigation"],
        .laporan-card,
        button {
            display: none !important;
        }
        
        /* Show only main content */
        body {
            margin: 0;
            padding: 0;
        }
        
        main,
        .fi-main,
        [role="main"] {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .laporan-container {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
        }
        
        .laporan-header {
            background: #6366f1 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            box-shadow: none;
            page-break-after: avoid;
        }
        
        .summary-card {
            background: #e0e7ff !important;
            border-color: #818cf8 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            page-break-inside: avoid;
        }
        
        .laporan-table {
            box-shadow: none;
            border: 1px solid #000;
            page-break-inside: avoid;
        }
        
        thead {
            background: #6366f1 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        tbody tr {
            page-break-inside: avoid;
        }
        
        /* Footer info */
        .laporan-container > div:last-child {
            page-break-inside: avoid;
        }
    }
</style>

<div class="laporan-container">

<div class="laporan-card">
    <div style="display: flex; align-items: center; gap: 12px;">
        <label style="font-size: 14px; font-weight: 600; color: #374151;">Periode:</label>
        <input
            type="month"
            wire:model.live="periode"
            class="period-input"
        />
    </div>
</div>

<div class="laporan-header">
    <div style="font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
        Laporan Stok Bahan Habis Pakai
    </div>
    <div style="font-size: 22px; font-weight: 600; margin-bottom: 4px;">
        NUKUMA SOES
    </div>
    <div style="font-size: 14px; opacity: 0.95;">
        Periode {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}
    </div>
</div>

<!-- Summary Cards -->
<div class="summary-card">
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-label">Total Item</div>
            <div class="summary-value">{{ count($laporanStok) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Masuk</div>
            <div class="summary-value">{{ number_format(array_sum(array_column($laporanStok, 'total_masuk')), 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Keluar</div>
            <div class="summary-value">{{ number_format(array_sum(array_column($laporanStok, 'total_keluar')), 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Stok Sisa</div>
            <div class="summary-value">{{ number_format(array_sum(array_column($laporanStok, 'stok_sisa')), 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="laporan-table">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-center" style="width: 100px;">Satuan</th>
                    <th class="text-right" style="width: 120px;">Total Masuk</th>
                    <th class="text-right" style="width: 120px;">Total Keluar</th>
                    <th class="text-right" style="width: 120px;">Stok Sisa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporanStok as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td>{{ $item['kategori'] }}</td>
                        <td class="text-center">{{ $item['satuan'] }}</td>
                        <td class="text-right">{{ number_format($item['total_masuk'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item['total_keluar'], 0, ',', '.') }}</td>
                        <td class="text-right {{ $item['stok_sisa'] <= 0 ? 'stok-warning' : 'stok-normal' }}">
                            {{ number_format($item['stok_sisa'], 0, ',', '.') }}
                            @if($item['stok_sisa'] <= 0)
                                <span style="margin-left: 4px;" title="Stok habis">⚠️</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #9ca3af; font-style: italic;">
                            Tidak ada data stok bahan habis pakai
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Footer Info -->
<div style="margin-top: 20px; padding: 16px; background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div style="font-size: 12px; color: #6b7280;">
        <strong>Keterangan:</strong> 
        <span style="color: #059669;">●</span> Stok Normal
        <span style="margin-left: 12px; color: #dc2626;">●</span> Stok Habis/Minus
    </div>
</div>

</div><!-- End laporan-container -->

</x-filament-panels::page>
