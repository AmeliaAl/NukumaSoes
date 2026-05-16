@if($aset)
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;500;600;700&display=swap');

    .kartu-wrap {
        font-family: 'Sora', sans-serif;
        margin-bottom: 24px;
    }

    .kartu-header {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        background: #0f172a;
        padding: 0;
    }

    .kartu-header-inner {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .kartu-header::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(234,179,8,.12) 0%, transparent 65%);
        z-index: 1;
        pointer-events: none;
    }

    .kartu-top-bar {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 20px 28px 14px;
        border-bottom: 1px solid rgba(255,255,255,.06);
    }

    .kartu-icon {
        width: 32px; height: 32px;
        background: rgba(234,179,8,.15);
        border: 1px solid rgba(234,179,8,.25);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .kartu-icon svg { width: 16px; height: 16px; color: #fbbf24; }

    .kartu-title {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #fbbf24;
    }

    .kartu-kode {
        margin-left: auto;
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        color: rgba(255,255,255,.35);
        letter-spacing: .05em;
    }

    .kartu-col {
        padding: 20px 28px 24px;
    }
    .kartu-col:first-of-type {
        border-right: 1px solid rgba(255,255,255,.05);
    }

    .kartu-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 10px;
    }
    .kartu-row:last-child { margin-bottom: 0; }

    .kartu-key {
        font-size: 11px;
        font-weight: 500;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: rgba(255,255,255,.3);
        min-width: 130px;
        flex-shrink: 0;
    }

    .kartu-val {
        font-size: 13px;
        font-weight: 500;
        color: rgba(255,255,255,.85);
        line-height: 1.4;
    }

    .kartu-val-money {
        font-family: 'DM Mono', monospace;
        font-size: 14px;
        font-weight: 500;
        color: #fbbf24;
    }

    .kartu-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 100px;
        background: rgba(99,102,241,.2);
        border: 1px solid rgba(99,102,241,.3);
        color: #a5b4fc;
    }

    @media (max-width: 640px) {
        .kartu-header-inner { grid-template-columns: 1fr; }
        .kartu-col:first-of-type { border-right: none; border-bottom: 1px solid rgba(255,255,255,.05); }
    }
</style>

<div class="kartu-wrap">
    <div class="kartu-header">
        <div class="kartu-header-inner">

            {{-- Top bar --}}
            <div class="kartu-top-bar">
                <div class="kartu-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="kartu-title">Kartu Penyusutan Aset Tetap</span>
                <span class="kartu-kode">{{ $aset->kode_aset }}</span>
            </div>

            {{-- Kolom kiri --}}
            <div class="kartu-col">
                <div class="kartu-row">
                    <span class="kartu-key">Nama Aset</span>
                    <span class="kartu-val">{{ $aset->nama_aset }}</span>
                </div>
                <div class="kartu-row">
                    <span class="kartu-key">Kategori</span>
                    <span class="kartu-val">
                        <span class="kartu-badge">{{ $aset->kategori_aset->nama_kategori ?? '-' }}</span>
                    </span>
                </div>
                <div class="kartu-row">
                    <span class="kartu-key">Tgl Perolehan</span>
                    <span class="kartu-val">
                        {{ $aset->tanggal_perolehan
                            ? \Carbon\Carbon::parse($aset->tanggal_perolehan)->translatedFormat('d F Y')
                            : '-' }}
                    </span>
                </div>
                <div class="kartu-row">
                    <span class="kartu-key">Masa Manfaat</span>
                    <span class="kartu-val">{{ $aset->masa_manfaat }} Tahun</span>
                </div>
            </div>

            {{-- Kolom kanan --}}
            <div class="kartu-col">
                <div class="kartu-row">
                    <span class="kartu-key">Nilai Perolehan</span>
                    <span class="kartu-val kartu-val-money">
                        Rp {{ number_format($aset->nilai_perolehan, 0, ',', '.') }}
                    </span>
                </div>
                <div class="kartu-row">
                    <span class="kartu-key">Nilai Residu</span>
                    <span class="kartu-val kartu-val-money">
                        Rp {{ number_format($aset->nilai_residu ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                <div class="kartu-row">
                    <span class="kartu-key">Metode</span>
                    <span class="kartu-val">
                        {{ ucwords(str_replace('_', ' ', $aset->metode_penyusutan)) }}
                    </span>
                </div>
                <div class="kartu-row">
                    <span class="kartu-key">Beban/Tahun</span>
                    <span class="kartu-val kartu-val-money">
                        @php
                            $bebanTahunan = ($aset->nilai_perolehan - ($aset->nilai_residu ?? 0)) / $aset->masa_manfaat;
                        @endphp
                        Rp {{ number_format($bebanTahunan, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>
@endif