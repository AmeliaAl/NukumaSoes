<x-filament-widgets::widget>
    <x-filament::section>

<style>
    .jurnal-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .jurnal-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        color: white;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        margin-bottom: 24px;
    }
    
    .filter-box {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .filter-input {
        border: 2px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }
    
    .filter-input:focus {
        outline: none;
        border-color: #4338ca;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .filter-btn {
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    
    .filter-btn:hover {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.4);
        transform: translateY(-1px);
    }
    
    .jurnal-table-container {
        border-radius: 12px;
       overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: 2px solid #d1d5db;
    }
    
.jurnal-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse;
    font-size: 14px;
}
    
    .jurnal-table thead {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
    }
    
    .jurnal-table th {
        padding: 14px 12px;
        text-align: center;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 13px;
        border-right: 1px solid rgba(255,255,255,0.2);
    }
    
    .jurnal-table th:last-child {
        border-right: none;
    }
    
    .jurnal-table tbody tr {
        background-color: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }
    
    .jurnal-table tbody tr:hover {
        background-color: #eff6ff;
    }
    
    .jurnal-table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }
    
    .jurnal-table tbody tr:nth-child(even):hover {
        background-color: #eff6ff;
    }
    
    .jurnal-table td {
        padding: 10px 12px;
        border-right: 1px solid #e5e7eb;
        vertical-align: top;
    }
    
    .jurnal-table td:last-child {
        border-right: none;
    }
    
    .jurnal-table tfoot {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        font-weight: bold;
        border-top: 3px solid #4f46e5;
    }
    
    .jurnal-table tfoot td {
        padding: 14px 12px;
        font-size: 15px;
        border-right: 1px solid rgba(255,255,255,0.2);
    }
    
    .jurnal-table tfoot td:last-child {
        border-right: none;
    }
    
    .debit-account {
        font-weight: 600;
        color: #111827;
    }
    
    .credit-account {
        margin-left: 24px;
        color: #374151;
    }
    
    .balance-alert {
        border-radius: 12px;
        padding: 16px 20px;
        margin-top: 20px;
        border: 2px solid;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-color: #86efac;
        color: #059669;
    }
    
    .alert-error {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-color: #fca5a5;
        color: #dc2626;
    }
</style>

{{-- FILTER PERIODE --}}
<form wire:submit.prevent="filterJurnal" class="filter-box">
    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
        <label style="font-size: 14px; font-weight: 600; color: #374151;">Pilih Periode:</label>
        <input type="month"
               wire:model="periode"
               class="filter-input"
               style="flex: 0 0 200px;">
        <button type="submit" class="filter-btn">
            🔍 Filter
        </button>
    </div>
</form>

{{-- HEADER --}}
<div class="jurnal-header">
    <div style="font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
        Jurnal Umum
    </div>
    <div style="font-size: 22px; font-weight: 600; margin-bottom: 4px;">
        NUKUMA SOES
    </div>
    <div style="font-size: 14px; opacity: 0.95;">
        Periode {{ $periode ? \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') : now()->translatedFormat('F Y') }}
    </div>
</div>

{{-- TABEL JURNAL --}}
<div class="jurnal-table-container">
    <table class="jurnal-table" >
        <thead>
            <tr>
                <th style="width: 12% !important;">Tanggal</th>
                <th style="width: 38% !important;">Akun</th>
                <th style="width: 10% !important;">Ref</th>
                <th style="width: 20% !important;">Debit</th>
                <th style="width: 20% !important;">Kredit</th>
            </tr>
        </thead>

        <tbody>
            @forelse($jurnals as $jurnal)
                @php $firstRow = true; @endphp

                @foreach($jurnal->jurnaldetail as $detail)
                    <tr>
                        
                        {{-- Tanggal --}}
                        <td style="text-align: center; color: #374151;">
                            @if($firstRow)
                                {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}
                            @endif
                        </td>

                        {{-- Akun --}}
                        <td>
                            @if($detail->debit != 0)
                                <span class="debit-account">{{ $detail->akun->nama_akun ?? '-' }}</span>
                            @else
                                <span class="credit-account">{{ $detail->akun->nama_akun ?? '-' }}</span>
                            @endif
                        </td>

                        {{-- Ref --}}
                        <td style="text-align: center; color: #6b7280; font-family: monospace;">
                            {{ $detail->akun->no_akun ?? '-' }}
                        </td>

                        {{-- Debit --}}
                        <td style="text-align: right; font-family: monospace; color: #059669; font-weight: 600; text-align:right;">
                            @if($detail->debit != 0)
                                Rp {{ number_format($detail->debit, 0, ',', '.') }}
                            @endif
                        </td>

                        {{-- Kredit --}}
                        <td style="text-align: right; font-family: monospace; color: #dc2626; font-weight: 600; text-align:right;">
                            @if($detail->credit != 0)
                                Rp {{ number_format($detail->credit, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                    @php $firstRow = false; @endphp
                @endforeach
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af; font-style: italic;">
                        Tidak ada data jurnal untuk periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- TOTAL --}}
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; text-transform: uppercase; letter-spacing: 0.5px;">
                    TOTAL
                </td>
                <td style="text-align: right; font-family: monospace; font-size: 16px;">
                    Rp {{ number_format($jurnals->flatMap->jurnaldetail->sum('debit'), 0, ',', '.') }}
                </td>
                <td style="text-align: right; font-family: monospace; font-size: 16px;">
                    Rp {{ number_format($jurnals->flatMap->jurnaldetail->sum('credit'), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- CEK SEIMBANG --}}
@php
    $totalDebit = $jurnals->flatMap->jurnaldetail->sum('debit');
    $totalCredit = $jurnals->flatMap->jurnaldetail->sum('credit');
    $isBalanced = $totalDebit === $totalCredit;
@endphp

<div class="balance-alert {{ $isBalanced ? 'alert-success' : 'alert-error' }}">
    @if($isBalanced)
        <svg style="width: 28px; height: 28px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <div>
            <div style="font-size: 16px;">✓ Jurnal Seimbang</div>
            <div style="font-size: 13px; font-weight: 400; margin-top: 2px;">Debit = Kredit (Rp {{ number_format($totalDebit, 0, ',', '.') }})</div>
        </div>
    @else
        <svg style="width: 28px; height: 28px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>
            <div style="font-size: 16px;">⚠ Jurnal Tidak Seimbang</div>
            <div style="font-size: 13px; font-weight: 400; margin-top: 2px;">
                Selisih: Rp {{ number_format(abs($totalDebit - $totalCredit), 0, ',', '.') }}
            </div>
        </div>
    @endif
</div>

{{-- FOOTER INFO --}}
<div style="margin-top: 20px; padding: 16px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; border: 2px solid #bae6fd;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 13px; color: #0369a1;">
            <strong>Total Transaksi:</strong> {{ $jurnals->count() }} entri
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