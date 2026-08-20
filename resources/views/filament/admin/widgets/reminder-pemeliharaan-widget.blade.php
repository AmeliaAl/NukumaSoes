<x-filament-widgets::widget>
    <x-filament::section>
        @if($jumlah > 0)
            <div style="display:flex; flex-direction:column; gap:8px; max-height:320px; overflow-y:auto; padding-right:4px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <x-filament::icon
                        icon="heroicon-o-exclamation-triangle"
                        style="width:20px; height:20px; color:#f59e0b;"
                    />
                    <h3 style="font-size:15px; font-weight:600; margin:0;">
                        Aset Perlu Pemeliharaan ({{ $jumlah }})
                    </h3>
                </div>

                @foreach ($aset as $item)
                    <div style="
                        background: #F7F4FF;
                        border: 1px solid #DDD6FF;
                        border-radius: 12px;
                        padding: 12px 16px;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 16px;
                    ">
                        {{-- Kiri --}}
                        <div style="flex:1; min-width:0;">
                            <p style="font-weight:600; font-size:14px; margin:0 0 2px 0;">
                                {{ $item['nama_aset'] }}
                            </p>
                            <p style="font-size:12px; color:#6b7280; margin:0 0 2px 0;">
                                {{ $item['kode_aset'] }}
                            </p>
                            <p style="font-size:12px; color:#9ca3af; margin:0;">
                                Perolehan: {{ \Carbon\Carbon::parse($item['tanggal_perolehan'])->format('d/m/Y') }}
                            </p>
                        </div>

                        {{-- Kanan --}}
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0;">
                            @if ($item['status'] === 'terlambat')
                                <span style="background:#fef2f2; color:#b91c1c; font-size:11px; font-weight:500; padding:3px 10px; border-radius:999px;">
                                    Terlambat {{ $item['hari_terlambat'] }} hari
                                </span>
                            @elseif ($item['status'] === 'hari_ini')
                                <span style="background:#fffbeb; color:#b45309; font-size:11px; font-weight:500; padding:3px 10px; border-radius:999px;">
                                    Jatuh tempo hari ini
                                </span>
                            @elseif ($item['status'] === 'segera')
                                <span style="background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:500; padding:3px 10px; border-radius:999px;">
                                    {{ $item['akan_jatuh_tempo'] }} hari lagi
                                </span>
                            @endif

                            <p style="font-size:11px; color:#9ca3af; margin:0;">
                                {{ \Carbon\Carbon::parse($item['next_pemeliharaan'])->format('d/m/Y') }}
                            </p>

                            <x-filament::button
                                type="button"
                                wire:click.stop="dismiss({{ $item['id'] }})"
                                color="success"
                                size="xs"
                            >
                                Done
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="display:flex; align-items:center; gap:8px; color:#16a34a;">
                <x-filament::icon
                    icon="heroicon-o-check-circle"
                    style="width:20px; height:20px;"
                />
                <p style="font-size:14px; font-weight:500; margin:0;">Aset Perlu Pemeliharaan</p>
            </div>
            <p style="font-size:13px; color:#6b7280; margin-top:4px;">
                Semua aset dalam kondisi aman.
            </p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>