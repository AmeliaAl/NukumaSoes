{{-- resources/views/filament/widgets/notifikasi-widget.blade.php --}}

<x-filament-widgets::widget>
    <x-filament::section>
        <div style="
            background: linear-gradient(180deg, #7B6FE8 0%, #563fd4 100%);
            border-radius:16px;
            padding:20px;
            color:white;
            border:1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 30px rgba(86, 63, 212, 0.4);
            height: 100%;
            min-height: 340px;
            max-height: 340px;
            display: flex;
            flex-direction: column;
                    ">
            {{-- heading --}}
            <div style="
                font-size:16px;
                font-weight:700;
                margin-bottom:14px;
                color:#fff;
            ">
                Notifikasi & laporan
            </div>

            {{-- list --}}
            <div>
                @foreach ($notifs as $notif)
                    <div style="
                        flex: 1;
                        overflow-y: auto;
                        min-height: 0;
                        border-bottom:1px solid rgba(255,255,255,0.10);
                    ">
                        <span style="
                            margin-top:6px;
                            width:8px;
                            height:8px;
                            border-radius:999px;
                            flex-shrink:0;
                            background:
                                {{ ($notif['warna'] ?? '') === 'warning' ? '#f6ad3c' : (($notif['warna'] ?? '') === 'info' ? '#4ea1ff' : '#5ec49b') }};
                        "></span>

                        <div style="min-width:0;">
                            <div style="
                                font-size:13px;
                                line-height:1.45;
                                color:#f1f1f1;
                                font-weight:500;
                            ">
                                {{ $notif['pesan'] ?? '-' }}
                            </div>

                            <div style="
                                font-size:12px;
                                color:#b5b5b5;
                                margin-top:2px;
                            ">
                               {{ \Carbon\Carbon::parse($notif['tanggal'])->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- buttons --}}
            <div style="margin-top:14px; display:flex; flex-direction:column; gap:8px;">
                <a
                    href="{{ \App\Filament\Admin\Resources\Jurnals\JurnalResource::getUrl('index') }}"
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        width:100%;
                        padding:10px 14px;
                        border-radius:10px;
                        border:1px solid rgba(255,255,255,0.18);
                        background:rgba(255,255,255,0.02);
                        color:#f3f3f3;
                        text-decoration:none;
                        font-size:14px;
                        font-weight:600;
                    "
                >
                    Jurnal penyusutan ↗
                </a>

                <a
                    href="{{ \App\Filament\Admin\Resources\BukuBesars\BukuBesarResource::getUrl('index') }}"
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        width:100%;
                        padding:10px 14px;
                        border-radius:10px;
                        border:1px solid rgba(255,255,255,0.18);
                        background:rgba(255,255,255,0.02);
                        color:#f3f3f3;
                        text-decoration:none;
                        font-size:14px;
                        font-weight:600;
                    "
                >
                    Buku besar ↗
                </a>

                <a
                    href="{{ \App\Filament\Admin\Pages\NeracaPage::getUrl() }}"
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        width:100%;
                        padding:10px 14px;
                        border-radius:10px;
                        border:1px solid rgba(255,255,255,0.18);
                        background:rgba(255,255,255,0.02);
                        color:#f3f3f3;
                        text-decoration:none;
                        font-size:14px;
                        font-weight:600;
                    "
                >
                    Lap. posisi keuangan ↗
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>