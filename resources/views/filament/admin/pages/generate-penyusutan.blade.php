<x-filament-panels::page>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    .gp-wrap {
        font-family: 'Plus Jakarta Sans', sans-serif;
        max-width: 680px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }

    /* ── Hero card ── */
    .gp-hero {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
        padding: 40px 40px 36px;
        margin-bottom: 24px;
        box-shadow: 0 20px 60px rgba(15,52,96,.35);
    }

    .gp-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 260px; height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(234,179,8,.18) 0%, transparent 70%);
        pointer-events: none;
    }
    .gp-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; left: -40px;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99,102,241,.12) 0%, transparent 70%);
        pointer-events: none;
    }

    .gp-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(234,179,8,.15);
        border: 1px solid rgba(234,179,8,.3);
        color: #fbbf24;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 100px;
        margin-bottom: 16px;
    }
    .gp-badge svg { width:12px; height:12px; }

    .gp-hero h1 {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 8px;
        line-height: 1.25;
    }
    .gp-hero p {
        font-size: 14px;
        color: rgba(255,255,255,.55);
        margin: 0;
        line-height: 1.6;
    }

    /* ── Main card ── */
    .gp-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
    }

    @media (prefers-color-scheme: dark) {
        .gp-card {
            background: #1e2130;
            border-color: rgba(255,255,255,.08);
        }
        .gp-label { color: #c9cad4 !important; }
        .gp-info { background: rgba(99,102,241,.1) !important; border-color: rgba(99,102,241,.25) !important; }
        .gp-info p { color: #a5b4fc !important; }
        .gp-info .gp-info-title { color: #c7d2fe !important; }
        .gp-divider { border-color: rgba(255,255,255,.07) !important; }
        .gp-stat { background: #262a3d !important; border-color: rgba(255,255,255,.07) !important; }
        .gp-stat-label { color: #6b7280 !important; }
        .gp-stat-val { color: #e5e7eb !important; }
    }

    /* ── Label ── */
    .gp-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 10px;
    }

    /* ── Input ── */
    .gp-input {
        width: 100%;
        background: #f9fafb;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 18px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        font-weight: 500;
        color: #111827;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
        box-sizing: border-box;
        margin-bottom: 24px;
    }
    .gp-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.15);
    }
    @media (prefers-color-scheme: dark) {
        .gp-input { background: #262a3d !important; border-color: rgba(255,255,255,.1) !important; color: #fff !important; }
        .gp-input::-webkit-calendar-picker-indicator { filter: invert(1); }
    }

    /* ── Button ── */
    .gp-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 15px 24px;
        border-radius: 12px;
        border: none;
        background: linear-gradient(135deg, #eab308 0%, #f59e0b 100%);
        color: #1a1000;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s, filter .15s;
        box-shadow: 0 4px 16px rgba(234,179,8,.35);
        margin-bottom: 24px;
        letter-spacing: .01em;
    }
    .gp-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(234,179,8,.45);
        filter: brightness(1.05);
    }
    .gp-btn:active { transform: translateY(0); }
    .gp-btn svg { width:18px; height:18px; }

    /* ── Divider ── */
    .gp-divider {
        border: none;
        border-top: 1px solid #f3f4f6;
        margin: 0 0 24px;
    }

    /* ── Info box ── */
    .gp-info {
        display: flex;
        gap: 14px;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 28px;
    }
    .gp-info-icon {
        flex-shrink: 0;
        width: 20px; height: 20px;
        color: #6366f1;
        margin-top: 1px;
    }
    .gp-info-title {
        font-size: 13px;
        font-weight: 600;
        color: #4f46e5;
        margin-bottom: 3px;
    }
    .gp-info p {
        font-size: 13px;
        color: #6366f1;
        margin: 0;
        line-height: 1.55;
    }

    /* ── Stats row ── */
    .gp-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .gp-stat {
        background: #f9fafb;
        border: 1px solid #f3f4f6;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    .gp-stat-val {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
        margin-bottom: 4px;
    }
    .gp-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9ca3af;
    }

    /* ── Loading state ── */
    .gp-btn.loading { pointer-events: none; filter: brightness(.9); }
    .gp-btn.loading .btn-text { opacity: .7; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .gp-spinner { animation: spin .8s linear infinite; display:none; }
    .gp-btn.loading .gp-spinner { display:block; }
    .gp-btn.loading .btn-icon { display:none; }

   .periode-box {
    margin-top: 12px;
    padding: 16px;
    border-radius: 12px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    
    color: white; /* 🔥 bikin semua tulisan putih */
    
    height: 100%; /* biar ikut tinggi card */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.periode-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.periode-label {
    color: rgba(255,255,255,0.7);
    font-size: 13px;
}

.periode-value {
    color: white;
    font-weight: 600;
}
</style>

<div class="gp-wrap">

    {{-- ── Hero ── --}}
    <div class="gp-hero">
        <div class="gp-badge">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Akuntansi Aset Tetap
        </div>
        <h1>Generate Penyusutan</h1>
        <p>Hitung beban penyusutan seluruh aset tetap aktif secara otomatis dan catat ke jurnal akuntansi.</p>
    </div>

    {{-- ── Main card ── --}}
    <div class="gp-card">

        {{-- Periode input --}}
        <label class="gp-label">Periode Penyusutan</label>
        <input
            type="month"
            wire:model="periode"
            class="gp-input"
        >

        {{-- Info box --}}
        <div class="gp-info" style="margin-bottom:24px;">
            <svg class="gp-info-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <div class="gp-info-title">Cara kerja sistem</div>
                <p>Sistem akan menghitung beban penyusutan semua aset tetap aktif pada periode yang dipilih menggunakan metode garis lurus, lalu mencatat jurnal akuntansi secara otomatis.</p>
            </div>
        </div>

        {{-- Button --}}
        <button class="gp-btn" wire:click="generate" wire:loading.class="loading">
            <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <svg class="gp-spinner" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9"/>
            </svg>
            <span class="btn-text">Proses Penyusutan</span>
        </button>

        <hr class="gp-divider">

        {{-- Stats --}}
        <div class="gp-stats">
            <div class="gp-stat">
                <div class="gp-stat-val" style="color:#6366f1;">
                    {{ \App\Models\Aset::whereNotNull('tanggal_perolehan')->count() }}
                </div>
                <div class="gp-stat-label">Total Aset</div>
            </div>
            <div class="gp-stat">
                <div class="gp-stat-val" style="color:#10b981;">
                    {{ \App\Models\Penyusutan::where('periode', 'like', \Carbon\Carbon::parse($this->periode)->format('Y') . '-%')
                    ->distinct()
                    ->count('periode') }}
                </div>
                <div class="gp-stat-label">Proses Tahun Ini</div>
            </div>
            <div class="card-item">
                <div class="periode-box">
                    <div class="periode-item">
                        <span class="periode-label">📅 Penyusutan terakhir</span>
                        <span class="periode-value">{{ $periodeTerakhir }}</span>
                    </div>

                    <div class="periode-item">
                        <span class="periode-label">➡️ Periode berikutnya</span>
                        <span class="periode-value">{{ $periodeBerikutnya }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</x-filament-panels::page>