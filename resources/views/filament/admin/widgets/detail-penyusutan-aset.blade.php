<x-filament-widgets::widget>
    <x-filament::section>

        <div class="mb-5">
            {{ $this->form }}
        </div>

        @if ($aset)
            <div style="
            background: linear-gradient(180deg, #7B6FE8 0%, #563fd4 100%);
            border-radius:16px;
            padding:20px;
            color:white;
            border:1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 30px rgba(86, 63, 212, 0.4);
        ">

                <div style="
                    border-bottom:1px solid rgba(255, 255, 255, 0.85);
                    padding-bottom:12px;
                    margin-bottom:15px;
                ">
                    <div style="font-size:18px; font-weight:700;">
                        {{ $aset->kode_aset ?? '-' }} - {{ $aset->nama_aset ?? '-' }}
                    </div>

                    <div style="font-size:13px; color:#fff; margin-top:6px;">
                        Perolehan {{ \Carbon\Carbon::parse($aset->tanggal_perolehan)->format('d M Y') }}
                    </div>
                </div>

                <div style="margin-bottom:15px;">
                    <div style="
                        display:flex;
                        justify-content:space-between;
                        margin-bottom:6px;
                        font-size:13px;
                    ">
                        <span style="color:#fff;">Tingkat Penyusutan</span>
                        <span style="font-size:20px; font-weight:700;">
                            {{ $persen }}%
                        </span>
                    </div>

                    <div style="
                        width:100%;
                        height:8px;
                        background:#444;
                        border-radius:999px;
                        overflow:hidden;
                    ">
                        <div style="
                            width: {{ $persen }}%;
                            height:8px;
                            background: {{ $persen < 50 ? '#34d399' : ($persen < 80 ? '#fbbf24' : '#f87171') }};
                            border-radius:999px;
                        "></div>
                    </div>
                </div>

                <div style="margin-bottom:10px;">
                    <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                        <span style="color:#fff;">Nilai Perolehan</span>
                        <span style="font-weight:600;">Rp {{ number_format($aset->nilai_perolehan, 0, ',', '.') }}</span>
                    </div>

                    <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                        <span style="color:#fff;">Akum. Penyusutan</span>
                        <span style="font-weight:600;">Rp {{ number_format($akumulasi, 0, ',', '.') }}</span>
                    </div>

                    <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                        <span style="color:#fff;">Nilai Buku</span>
                        <span style="font-weight:600;">Rp {{ number_format($nilaiBuku, 0, ',', '.') }}</span>
                    </div>

                    <div style="display:flex; justify-content:space-between; padding:10px 0;">
                        <span style="color:#fff;">Periode Terakhir</span>
                        <span style="font-weight:600;">{{ $penyusutanTerakhir->periode ?? '-' }}</span>
                    </div>
                </div>

                <div style="
                    margin-top:10px;
                    padding:12px;
                    border-radius:10px;
                    font-size:13px;
                    background:  #fae385;
                    color: #3b2c86;
                    border:1px solid rgba(255, 255, 255, 0.08);
                ">
                    {{ $status }}
                </div>
            </div>
          
        @endif

    </x-filament::section>
</x-filament-widgets::widget>