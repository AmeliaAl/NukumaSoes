<x-filament-panels::page>

<style>
    .neraca-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 16px;
        margin-bottom: 24px;
    }
    
    .neraca-header {
<<<<<<< HEAD
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border-radius: 12px 12px 0 0;
=======
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border-radius: 12px;
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
        padding: 24px;
        text-align: center;
        color: white;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
<<<<<<< HEAD
        margin-bottom: 0;
=======
        margin-bottom: 24px;
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
    }
    
    .neraca-table {
        border: 2px solid #d1d5db;
<<<<<<< HEAD
        border-radius: 0 0 12px 12px;
=======
        border-radius: 12px;
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .table-header {
<<<<<<< HEAD
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
=======
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
        color: white;
        font-weight: bold;
        padding: 16px;
        text-align: center;
        font-size: 18px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .section-header {
<<<<<<< HEAD
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        color: white;
        font-weight: 600;
        padding: 12px 20px;
        border-bottom: 2px solid #4f46e5;
=======
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 600;
        padding: 12px 20px;
        border-bottom: 2px solid #1d4ed8;
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
    }
    
    .account-row {
        padding: 12px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        transition: background-color 0.2s;
    }
    
    .account-row:hover {
        background-color: #eff6ff;
    }
    
    .subtotal-row {
<<<<<<< HEAD
        background-color: #c7d2fe;
        padding: 12px 20px;
        font-weight: bold;
        border-bottom: 2px solid #818cf8;
=======
        background-color: #dbeafe;
        padding: 12px 20px;
        font-weight: bold;
        border-bottom: 2px solid #93c5fd;
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
        display: flex;
        justify-content: space-between;
    }
    
    .total-row {
<<<<<<< HEAD
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
=======
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
        color: white;
        padding: 16px 20px;
        font-weight: bold;
        font-size: 18px;
<<<<<<< HEAD
        border-top: 4px solid #4f46e5;
=======
        border-top: 4px solid #1e40af;
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
    }
    
    .balance-indicator {
        border-radius: 12px;
        padding: 20px;
        margin-top: 24px;
        border: 2px solid;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .balanced {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-color: #86efac;
    }
    
    .unbalanced {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-color: #fca5a5;
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
<<<<<<< HEAD
        border-color: #4338ca;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
        .neraca-card,
        .balance-indicator,
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
        
        .neraca-container {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
        }
        
        .neraca-header {
            background: #6366f1 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            box-shadow: none;
            page-break-after: avoid;
        }
        
        .neraca-table {
            box-shadow: none;
            border: 1px solid #000;
            page-break-inside: avoid;
        }
        
        .table-header,
        .section-header {
            background: #6366f1 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .subtotal-row {
            background: #c7d2fe !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .total-row {
            background: #6366f1 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .two-column-layout > div {
            page-break-inside: avoid;
        }
    }
=======
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
</style>

<div class="neraca-card">
    <div style="display: flex; align-items: center; gap: 12px; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <label style="font-size: 14px; font-weight: 600; color: #374151;">Periode:</label>
            <input
                type="month"
                wire:model.live="periode"
                class="period-input"
            />
        </div>
        <button 
            wire:click="toggleDebug"
            style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;"
            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 6px rgba(79, 70, 229, 0.4)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
        >
<<<<<<< HEAD
            {{ $showDebug ? '🔍 Sembunyikan Detail' : '🔍 Tampilkan Detail' }}
=======
            {{ $showDebug ? '🔍 Sembunyikan Debug' : '🔍 Tampilkan Debug' }}
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
        </button>
    </div>
</div>

@if($showDebug)
<div style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border: 2px solid #9ca3af; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
    <div style="font-size: 18px; font-weight: bold; color: #111827; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <svg style="width: 24px; height: 24px; color: #6366f1;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        Debug Information
    </div>

    <!-- Validasi Jurnal -->
    <div style="background: white; border-radius: 8px; padding: 16px; margin-bottom: 16px; border: 1px solid #d1d5db;">
        <div style="font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 12px;">📊 Validasi Jurnal</div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Debit</div>
                <div style="font-size: 16px; font-weight: bold; color: #059669;">
                    Rp{{ number_format($debugInfo['total_debit_jurnal'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Kredit</div>
                <div style="font-size: 16px; font-weight: bold; color: #dc2626;">
                    Rp{{ number_format($debugInfo['total_kredit_jurnal'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Selisih</div>
                <div style="font-size: 16px; font-weight: bold; color: {{ abs($debugInfo['selisih_jurnal'] ?? 0) < 1 ? '#059669' : '#dc2626' }};">
                    Rp{{ number_format($debugInfo['selisih_jurnal'] ?? 0, 0, ',', '.') }}
                    @if(abs($debugInfo['selisih_jurnal'] ?? 0) < 1)
                        ✓
                    @else
                        ✗
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Neraca -->
    <div style="background: white; border-radius: 8px; padding: 16px; margin-bottom: 16px; border: 1px solid #d1d5db;">
        <div style="font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 12px;">📈 Summary Neraca</div>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Aset Lancar</div>
                <div style="font-size: 14px; font-weight: 600; color: #111827;">
                    Rp{{ number_format($debugInfo['summary']['total_aset_lancar'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Aset Tetap</div>
                <div style="font-size: 14px; font-weight: 600; color: #111827;">
                    Rp{{ number_format($debugInfo['summary']['total_aset_tetap'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Liabilitas</div>
                <div style="font-size: 14px; font-weight: 600; color: #111827;">
                    Rp{{ number_format($debugInfo['summary']['total_liabilitas'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Ekuitas</div>
                <div style="font-size: 14px; font-weight: 600; color: #111827;">
                    Rp{{ number_format($debugInfo['summary']['total_ekuitas'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div style="grid-column: span 2; border-top: 2px solid #e5e7eb; padding-top: 12px; margin-top: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Selisih Neraca</div>
                        <div style="font-size: 16px; font-weight: bold; color: {{ abs($debugInfo['summary']['selisih_neraca'] ?? 0) < 1 ? '#059669' : '#dc2626' }};">
                            Rp{{ number_format($debugInfo['summary']['selisih_neraca'] ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="padding: 8px 16px; border-radius: 8px; font-weight: 600; {{ ($debugInfo['summary']['is_balance'] ?? false) ? 'background: #d1fae5; color: #059669;' : 'background: #fee2e2; color: #dc2626;' }}">
                        {{ ($debugInfo['summary']['is_balance'] ?? false) ? '✓ Balance' : '✗ Tidak Balance' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Aset -->
    @if(isset($debugInfo['detail_aset']) && count($debugInfo['detail_aset']) > 0)
    <details style="background: white; border-radius: 8px; padding: 16px; margin-bottom: 16px; border: 1px solid #d1d5db;">
        <summary style="font-size: 15px; font-weight: 600; color: #374151; cursor: pointer;">📦 Detail Aset ({{ count($debugInfo['detail_aset']) }} akun)</summary>
        <div style="margin-top: 12px; max-height: 300px; overflow-y: auto;">
            <table style="width: 100%; font-size: 12px;">
                <thead style="background: #f3f4f6; position: sticky; top: 0;">
                    <tr>
                        <th style="padding: 8px; text-align: left;">No Akun</th>
                        <th style="padding: 8px; text-align: left;">Nama Akun</th>
                        <th style="padding: 8px; text-align: right;">Debit</th>
                        <th style="padding: 8px; text-align: right;">Kredit</th>
                        <th style="padding: 8px; text-align: right;">Saldo</th>
                        <th style="padding: 8px; text-align: center;">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($debugInfo['detail_aset'] as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px;">{{ $item['no_akun'] }}</td>
                        <td style="padding: 8px;">{{ $item['nama_akun'] }}</td>
                        <td style="padding: 8px; text-align: right; color: #059669;">{{ number_format($item['debit'], 0, ',', '.') }}</td>
                        <td style="padding: 8px; text-align: right; color: #dc2626;">{{ number_format($item['kredit'], 0, ',', '.') }}</td>
                        <td style="padding: 8px; text-align: right; font-weight: 600;">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                        <td style="padding: 8px; text-align: center;">
<<<<<<< HEAD
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; {{ $item['kategori'] == 'Aset Lancar' ? 'background: #c7d2fe; color: #4f46e5;' : ($item['kategori'] == 'Aset Tetap' ? 'background: #fef3c7; color: #92400e;' : 'background: #fee2e2; color: #dc2626;') }}">
=======
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; {{ $item['kategori'] == 'Aset Lancar' ? 'background: #dbeafe; color: #1e40af;' : ($item['kategori'] == 'Aset Tetap' ? 'background: #fef3c7; color: #92400e;' : 'background: #fee2e2; color: #dc2626;') }}">
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
                                {{ $item['kategori'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
    @endif

    <!-- Laba Rugi -->
    <div style="background: white; border-radius: 8px; padding: 16px; border: 1px solid #d1d5db;">
        <div style="font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 12px;">💰 Laba Rugi</div>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Pendapatan</div>
                <div style="font-size: 14px; font-weight: 600; color: #059669;">
                    Rp{{ number_format($debugInfo['pendapatan']['saldo'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Beban</div>
                <div style="font-size: 14px; font-weight: 600; color: #dc2626;">
                    Rp{{ number_format($debugInfo['beban']['saldo'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div style="grid-column: span 2; border-top: 2px solid #e5e7eb; padding-top: 12px; margin-top: 8px;">
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Laba/Rugi</div>
                <div style="font-size: 16px; font-weight: bold; color: {{ ($debugInfo['laba_rugi'] ?? 0) >= 0 ? '#059669' : '#dc2626' }};">
                    Rp{{ number_format($debugInfo['laba_rugi'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="neraca-header">
    <div style="font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
        Laporan Posisi Keuangan
    </div>
    <div style="font-size: 22px; font-weight: 600; margin-bottom: 4px;">
        NUKUMA SOES
    </div>
    <div style="font-size: 14px; opacity: 0.95;">
        Periode {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}
    </div>
</div>

<div class="neraca-table">
<<<<<<< HEAD
    <table style="width: 100%; border-collapse: collapse; border: 2px solid #9ca3af;">
        <thead>
            <tr>
                <th colspan="2" class="table-header" style="border-right: 1px solid #9ca3af; border-bottom: 1px solid #9ca3af;">AKTIVA</th>
                <th colspan="2" class="table-header" style="border-bottom: 1px solid #9ca3af;">PASIVA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" class="section-header" style="border-right: 1px solid #9ca3af; border-bottom: 1px solid #d1d5db;">ASET LANCAR</td>
                <td colspan="2" class="section-header" style="border-bottom: 1px solid #d1d5db;">KEWAJIBAN </td>
            </tr>

            @php
                $maxRows = max(count($aktivaLancar), count($liabilitas));
            @endphp

            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    @if(isset($aktivaLancar[$i]))
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; color: #374151; width: 35%;">
                            {{ $aktivaLancar[$i]->nama_akun }}
                            @if(isset($aktivaLancar[$i]->is_abnormal) && $aktivaLancar[$i]->is_abnormal)
                                <span style="color: #ef4444; font-size: 11px;" title="Saldo tidak normal">⚠️</span>
                            @endif
                        </td>
                        <td style="padding: 8px 12px; border-right: 1px solid #9ca3af; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 500; color: #111827; width: 15%;">
                            Rp {{ number_format($aktivaLancar[$i]->saldo, 0, ',', '.') }}
                        </td>
                    @else
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; width: 35%;">&nbsp;</td>
                        <td style="padding: 8px 12px; border-right: 1px solid #9ca3af; border-bottom: 1px solid #e5e7eb; width: 15%;">&nbsp;</td>
                    @endif

                    @if(isset($liabilitas[$i]))
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; color: #374151; width: 35%;">
                            {{ $liabilitas[$i]->nama_akun }}
                            @if(isset($liabilitas[$i]->is_abnormal) && $liabilitas[$i]->is_abnormal)
                                <span style="color: #ef4444; font-size: 11px;" title="Saldo tidak normal">⚠️</span>
                            @endif
                        </td>
                        <td style="padding: 8px 12px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 500; color: #111827; width: 15%;">
                            Rp {{ number_format($liabilitas[$i]->saldo, 0, ',', '.') }}
                        </td>
                    @else
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; width: 35%;">&nbsp;</td>
                        <td style="padding: 8px 12px; border-bottom: 1px solid #e5e7eb; width: 15%;">&nbsp;</td>
                    @endif
                </tr>
            @endfor

            <tr>
                <td style="padding: 10px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #9ca3af; font-weight: 600; background: #e0e7ff; color: #374151;">TOTAL ASET LANCAR</td>
                <td style="padding: 10px 12px; border-right: 1px solid #9ca3af; border-bottom: 1px solid #9ca3af; text-align: right; font-weight: 700; background: #e0e7ff; color: #111827;">Rp {{ number_format($totalAktivaLancar, 0, ',', '.') }}</td>
                <td style="padding: 10px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #9ca3af; font-weight: 600; background: #e0e7ff; color: #374151;">TOTAL KEWAJIBAN</td>
                <td style="padding: 10px 12px; border-bottom: 1px solid #9ca3af; text-align: right; font-weight: 700; background: #e0e7ff; color: #111827;">Rp {{ number_format($totalLiabilitas, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="section-header" style="border-right: 1px solid #9ca3af; border-bottom: 1px solid #d1d5db;">ASET TETAP</td>
                <td colspan="2" class="section-header" style="border-bottom: 1px solid #d1d5db;">EKUITAS</td>
            </tr>

            @php
                $maxRows2 = max(count($aktivaTetap), count($ekuitas));
            @endphp

            @for($i = 0; $i < $maxRows2; $i++)
                <tr>
                    @if(isset($aktivaTetap[$i]))
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; color: #374151;">
                            {{ $aktivaTetap[$i]->nama_akun }}
                            @if(isset($aktivaTetap[$i]->is_abnormal) && $aktivaTetap[$i]->is_abnormal)
                                <span style="color: #ef4444; font-size: 11px;" title="Saldo tidak normal">⚠️</span>
                            @endif
                        </td>
                        <td style="padding: 8px 12px; border-right: 1px solid #9ca3af; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 500; color: #111827;">
                            Rp {{ number_format($aktivaTetap[$i]->saldo, 0, ',', '.') }}
                        </td>
                    @else
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">&nbsp;</td>
                        <td style="padding: 8px 12px; border-right: 1px solid #9ca3af; border-bottom: 1px solid #e5e7eb;">&nbsp;</td>
                    @endif

                    @if(isset($ekuitas[$i]))
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; color: #374151;">
                            {{ $ekuitas[$i]->nama_akun }}
                            @if(isset($ekuitas[$i]->is_abnormal) && $ekuitas[$i]->is_abnormal)
                                <span style="color: #ef4444; font-size: 11px;" title="Saldo tidak normal">⚠️</span>
                            @endif
                        </td>
                        <td style="padding: 8px 12px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 500; color: #111827;">
                            Rp {{ number_format($ekuitas[$i]->saldo, 0, ',', '.') }}
                        </td>
                    @else
                        <td style="padding: 8px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">&nbsp;</td>
                        <td style="padding: 8px 12px; border-bottom: 1px solid #e5e7eb;">&nbsp;</td>
                    @endif
                </tr>
            @endfor

            <tr>
                <td style="padding: 10px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #9ca3af; font-weight: 600; background: #e0e7ff; color: #374151;">TOTAL ASET TETAP</td>
                <td style="padding: 10px 12px; border-right: 1px solid #9ca3af; border-bottom: 1px solid #9ca3af; text-align: right; font-weight: 700; background: #e0e7ff; color: #111827;">Rp {{ number_format($totalAktivaTetap, 0, ',', '.') }}</td>
                <td style="padding: 10px 12px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #9ca3af; font-weight: 600; background: #e0e7ff; color: #374151;">TOTAL EKUITAS</td>
                <td style="padding: 10px 12px; border-bottom: 1px solid #9ca3af; text-align: right; font-weight: 700; background: #e0e7ff; color: #111827;">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td style="padding: 14px 12px; border-right: 1px solid #e5e7eb; font-weight: 700; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; font-size: 16px;">TOTAL AKTIVA</td>
                <td style="padding: 14px 12px; border-right: 1px solid #9ca3af; text-align: right; font-weight: 700; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; font-size: 16px;">Rp {{ number_format($totalAktiva, 0, ',', '.') }}</td>
                <td style="padding: 14px 12px; border-right: 1px solid #e5e7eb; font-weight: 700; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; font-size: 16px;">TOTAL PASIVA</td>
                <td style="padding: 14px 12px; text-align: right; font-weight: 700; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; font-size: 16px;">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
=======
    <div style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 2px solid #d1d5db;">
        <div class="table-header" style="border-right: 2px solid white;">AKTIVA</div>
        <div class="table-header">PASIVA</div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; min-height: 500px;">
        
        <!-- KOLOM KIRI: AKTIVA -->
        <div style="border-right: 2px solid #d1d5db; background-color: #f9fafb;">
            <!-- Aktiva Lancar -->
            <div class="section-header">Aktiva Lancar</div>

            @forelse($aktivaLancar as $akun)
                <div class="account-row">
                    <span style="color: #374151;">
                        {{ $akun->nama_akun }}
                        @if(isset($akun->is_abnormal) && $akun->is_abnormal)
                            <span style="color: #ef4444; font-size: 11px; margin-left: 4px;" title="Saldo tidak normal - cek jurnal">⚠️</span>
                        @endif
                    </span>
                    <span style="font-weight: 600; color: {{ isset($akun->is_abnormal) && $akun->is_abnormal ? '#ef4444' : '#111827' }};">
                        Rp{{ number_format($akun->saldo, 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <div style="padding: 12px 20px; color: #9ca3af; font-style: italic; border-bottom: 1px solid #e5e7eb;">
                    Tidak ada data
                </div>
            @endforelse

            <div class="subtotal-row">
                <span style="text-transform: uppercase;">Total</span>
                <span>Rp{{ number_format($totalAktivaLancar, 0, ',', '.') }}</span>
            </div>

            <!-- Aktiva Tetap -->
            <div class="section-header" style="margin-top: 8px;">Aktiva Tetap</div>

            @forelse($aktivaTetap as $akun)
                <div class="account-row">
                    <span style="color: #374151;">
                        {{ $akun->nama_akun }}
                        @if(isset($akun->is_abnormal) && $akun->is_abnormal)
                            <span style="color: #ef4444; font-size: 11px; margin-left: 4px;" title="Saldo tidak normal - cek jurnal">⚠️</span>
                        @endif
                    </span>
                    <span style="font-weight: 600; color: {{ isset($akun->is_abnormal) && $akun->is_abnormal ? '#ef4444' : '#111827' }};">
                        Rp{{ number_format($akun->saldo, 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <div style="padding: 12px 20px; color: #9ca3af; font-style: italic; border-bottom: 1px solid #e5e7eb;">
                    Tidak ada data
                </div>
            @endforelse

            <div class="subtotal-row">
                <span style="text-transform: uppercase;">Total</span>
                <span>Rp{{ number_format($totalAktivaTetap, 0, ',', '.') }}</span>
            </div>

            <!-- TOTAL AKTIVA -->
            <div class="total-row">
                <span style="text-transform: uppercase; letter-spacing: 0.5px;">Total Aktiva</span>
                <span>Rp{{ number_format($totalAktiva, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- KOLOM KANAN: PASIVA -->
        <div style="background-color: #f9fafb;">
            <!-- Kewajiban -->
            <div class="section-header">Kewajiban</div>

            @forelse($liabilitas as $akun)
                <div class="account-row">
                    <span style="color: #374151;">
                        {{ $akun->nama_akun }}
                        @if(isset($akun->is_abnormal) && $akun->is_abnormal)
                            <span style="color: #ef4444; font-size: 11px; margin-left: 4px;" title="Saldo tidak normal - cek jurnal">⚠️</span>
                        @endif
                    </span>
                    <span style="font-weight: 600; color: {{ isset($akun->is_abnormal) && $akun->is_abnormal ? '#ef4444' : '#111827' }};">
                        Rp{{ number_format($akun->saldo, 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <div style="padding: 12px 20px; color: #9ca3af; font-style: italic; border-bottom: 1px solid #e5e7eb;">
                    Tidak ada data
                </div>
            @endforelse

            <div class="subtotal-row">
                <span style="text-transform: uppercase;">Total</span>
                <span>Rp{{ number_format($totalLiabilitas, 0, ',', '.') }}</span>
            </div>

            <!-- Ekuitas -->
            <div class="section-header" style="margin-top: 8px;">Ekuitas</div>

            @forelse($ekuitas as $akun)
                <div class="account-row">
                    <span style="color: #374151;">
                        {{ $akun->nama_akun }}
                        @if(isset($akun->is_abnormal) && $akun->is_abnormal)
                            <span style="color: #ef4444; font-size: 11px; margin-left: 4px;" title="Saldo tidak normal - cek jurnal">⚠️</span>
                        @endif
                    </span>
                    <span style="font-weight: 600; color: {{ isset($akun->is_abnormal) && $akun->is_abnormal ? '#ef4444' : '#111827' }};">
                        Rp{{ number_format($akun->saldo, 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <div style="padding: 12px 20px; color: #9ca3af; font-style: italic; border-bottom: 1px solid #e5e7eb;">
                    Tidak ada data
                </div>
            @endforelse

            <div class="subtotal-row">
                <span style="text-transform: uppercase;">Total</span>
                <span>Rp{{ number_format($totalEkuitas, 0, ',', '.') }}</span>
            </div>

            <!-- TOTAL PASIVA -->
            <div class="total-row">
                <span style="text-transform: uppercase; letter-spacing: 0.5px;">Total Pasiva</span>
                <span>Rp{{ number_format($totalPasiva, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
</div>

<div class="balance-indicator {{ $totalAktiva == $totalPasiva ? 'balanced' : 'unbalanced' }}">
    <div style="display: flex; align-items: center; gap: 12px;">
        @if($totalAktiva == $totalPasiva)
            <svg style="width: 32px; height: 32px; color: #10b981;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <div style="font-size: 18px; font-weight: bold; color: #059669;">✓ Neraca Seimbang</div>
                <div style="font-size: 14px; color: #10b981;">Total Aktiva = Total Pasiva</div>
            </div>
        @else
            <svg style="width: 32px; height: 32px; color: #ef4444;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <div style="font-size: 18px; font-weight: bold; color: #dc2626;">✗ Neraca Tidak Seimbang</div>
                <div style="font-size: 14px; color: #ef4444;">Selisih: Rp{{ number_format(abs($totalAktiva - $totalPasiva), 0, ',', '.') }}</div>
            </div>
        @endif
    </div>
    <div style="text-align: right;">
        <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
            Dilihat Pada
        </div>
        <div style="font-size: 14px; font-weight: bold; color: #111827;">
            {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>
</div>

@if(count($saldoTidakNormal) > 0)
<div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #fbbf24; border-radius: 12px; padding: 16px; margin-top: 16px;">
    <div style="display: flex; align-items: start; gap: 12px;">
        <svg style="width: 24px; height: 24px; color: #f59e0b; flex-shrink: 0; margin-top: 2px;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div style="flex: 1;">
            <div style="font-size: 16px; font-weight: bold; color: #92400e; margin-bottom: 8px;">
                ⚠️ Peringatan: Ditemukan {{ count($saldoTidakNormal) }} Akun dengan Saldo Tidak Normal
            </div>
            <div style="font-size: 13px; color: #78350f; margin-bottom: 12px;">
                Akun-akun berikut memiliki saldo yang tidak sesuai dengan saldo normalnya. Silakan cek jurnal terkait:
            </div>
            <div style="background: white; border-radius: 8px; padding: 12px; border: 1px solid #fbbf24;">
                @foreach($saldoTidakNormal as $item)
                    <div style="padding: 8px 0; border-bottom: 1px solid #fef3c7; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 600; color: #92400e;">{{ $item['akun'] }}</div>
                            <div style="font-size: 12px; color: #a16207; margin-top: 2px;">{{ $item['keterangan'] }}</div>
                        </div>
                        <div style="font-weight: bold; color: #dc2626;">
                            Rp{{ number_format($item['saldo'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="font-size: 12px; color: #78350f; margin-top: 8px; font-style: italic;">
                💡 Tip: Periksa jurnal di menu Laporan → Jurnal atau Buku Besar untuk menemukan transaksi yang salah.
            </div>
        </div>
    </div>
</div>
@endif

</x-filament-panels::page>