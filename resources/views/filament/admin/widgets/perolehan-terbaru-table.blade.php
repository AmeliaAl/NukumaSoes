<x-filament-widgets::widget>
    <div style="
        height: 452px;
        background: linear-gradient(180deg, #7B6FE8 0%, #563fd4 100%);
        border:1px solid rgba(255,255,255,0.1);
box-shadow: 0 10px 30px rgba(86, 63, 212, 0.4);
        border-radius:8px;
        padding:16px;
        color:white;
        display:flex;
        flex-direction:column;
    ">

        <h3 style="
            font-size:14px;
            font-weight:700;
            margin:0 0 18px 0;
        ">
            Perolehan aset terbaru
        </h3>

        <div style="
            flex:1;
            overflow-y:auto;
            min-height:0;
            padding-right:4px;
        ">
            @forelse ($asets as $aset)
                <div style="
                    display:flex;
                    justify-content:space-between;
                    gap:12px;
                    padding:8px 0;
                    border-bottom:1px solid rgba(255,255,255,.15);
                ">
                    <div style="min-width:0;">
                        <div style="
                            font-size:13px;
                            font-weight:700;
                            color:#fff;
                            line-height:1.2;
                            white-space:nowrap;
                            overflow:hidden;
                            text-overflow:ellipsis;
                        ">
                            {{ $aset->nama_aset }}
                        </div>
                        <div style="
                            font-size:12px;
                            color:#c9c9c9;
                            line-height:1.2;
                        ">
                            {{ $aset->kode_aset }}
                        </div>
                    </div>

                    <div style="text-align:right; flex-shrink:0;">
                        <div style="
                            font-size:12px;
                            font-weight:700;
                            background:#e7f8dc;
                            color:#2f6b1f;
                            padding:2px 8px;
                            border-radius:999px;
                            display:inline-block;
                            margin-bottom:3px;
                        ">
                            Rp {{ number_format($aset->nilai_perolehan, 0, ',', '.') }}
                        </div>
                        <div style="font-size:12px; color:#d0d0d0;">
                            {{ \Carbon\Carbon::parse($aset->tanggal_perolehan)->format('j M Y') }}
                        </div>
                    </div>
                </div>
            @empty
                <p style="font-size:13px; color:#c9c9c9; margin:0;">
                    Belum ada data aset.
                </p>
            @endforelse
        </div>

        <a href="{{ $urlSemuaAset }}" style="
            margin-top:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            height:34px;
            border:1px solid rgba(255,255,255,.25);
            border-radius:7px;
            color:white;
            text-decoration:none;
            font-size:14px;
            font-weight:700;
        ">
            Lihat semua aset ↗
        </a>

    </div>
</x-filament-widgets::widget>