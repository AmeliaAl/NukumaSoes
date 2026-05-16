<x-filament-widgets::widget>
    <x-filament::section>
        <div style="
            height: 420px;
            display:flex;
            flex-direction:column;
            background: linear-gradient(180deg, #7B6FE8 0%, #563fd4 100%);
            border-radius:16px;
            padding:20px;
            color:white;
            border:1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 30px rgba(86, 63, 212, 0.4);
        ">

            {{-- HEADER --}}
            <div style="
                border-bottom: 1px solid rgba(255,255,255,0.1);
                padding-bottom: 10px;
                margin-bottom: 12px;
                flex-shrink:0;
            ">
                <div style="font-size:16px; font-weight:600;">
                    Penyusutan bulan ini
                </div>
                <div style="font-size:12px; color:rgba(255,255,255,.7); margin-top:4px;">
                    {{ $periode }}
                </div>
            </div>

            {{-- LIST SCROLL --}}
            <div style="
                flex:1;
                overflow-y:auto;
                padding-right:4px;
            ">
                @forelse ($items as $item)
                    <div style="
                        margin-bottom: 12px;
                        padding-bottom: 10px;
                        border-bottom: 1px solid rgba(255,255,255,0.08);
                    ">
                        <div style="
                            display:flex;
                            justify-content:space-between;
                            gap:12px;
                            font-size:13px;
                            margin-bottom:6px;
                        ">
                            <span style="color:rgba(255,255,255,.85);">
                                {{ $item['nama'] }}
                            </span>
                            <span style="font-weight:600; color:#fff;">
                                {{ $item['nominal'] }}
                            </span>
                        </div>

                        <div style="
                            width:100%;
                            height:6px;
                            background:rgba(0,0,0,.35);
                            border-radius:999px;
                            overflow:hidden;
                        ">
                            <div style="
                                height:6px;
                                width: {{ $item['persen'] }}%;
                                background: {{ $item['warna'] }};
                                border-radius:999px;
                            "></div>
                        </div>
                    </div>
                @empty
                    <div style="font-size:13px; color:rgba(255,255,255,.7);">
                        Belum ada data penyusutan untuk bulan ini.
                    </div>
                @endforelse
            </div>

            {{-- TOTAL --}}
            <div style="
                flex-shrink:0;
                border-top: 1px solid rgba(255,255,255,0.1);
                padding-top: 12px;
                margin-top: 12px;
                display:flex;
                justify-content:space-between;
                font-weight:700;
                font-size:14px;
            ">
                <span>Total bulan ini</span>
                <span>{{ $total }}</span>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>