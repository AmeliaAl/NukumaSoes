<x-filament-panels::page>
    <div class="space-y-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                    <input type="date" wire:model.live="tanggal_mulai" class="mt-1 block rounded-lg border-gray-300 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                    <input type="date" wire:model.live="tanggal_akhir" class="mt-1 block rounded-lg border-gray-300 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h2 class="text-xl font-bold text-center mb-6 dark:text-white">LAPORAN LABA RUGI</h2>
            <p class="text-center text-sm text-gray-500 mb-6">Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d/m/Y') }}</p>

            @php $pendapatan = $this->getPendapatanData(); $beban = $this->getBebanData(); @endphp

            {{-- Pendapatan --}}
            <div class="mb-6">
                <h3 class="font-bold text-lg mb-2 text-green-700 dark:text-green-400">PENDAPATAN</h3>
                <table class="w-full text-sm">
                    @foreach ($pendapatan['items'] as $item)
                    <tr>
                        <td class="py-1 pl-6">{{ $item['akun']->kode_akun }} - {{ $item['akun']->nama_akun }}</td>
                        <td class="py-1 text-right w-48">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="border-t font-bold">
                        <td class="py-2">Total Pendapatan</td>
                        <td class="py-2 text-right text-green-600">Rp {{ number_format($pendapatan['total'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            {{-- Beban --}}
            <div class="mb-6">
                <h3 class="font-bold text-lg mb-2 text-red-700 dark:text-red-400">BEBAN</h3>
                <table class="w-full text-sm">
                    @foreach ($beban['items'] as $item)
                    <tr>
                        <td class="py-1 pl-6">{{ $item['akun']->kode_akun }} - {{ $item['akun']->nama_akun }}</td>
                        <td class="py-1 text-right w-48">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="border-t font-bold">
                        <td class="py-2">Total Beban</td>
                        <td class="py-2 text-right text-red-600">Rp {{ number_format($beban['total'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            {{-- Laba/Rugi --}}
            <div class="border-t-2 border-double border-gray-400 pt-4">
                @php $labaRugi = $this->getLabaRugi(); @endphp
                <table class="w-full">
                    <tr class="text-xl font-bold">
                        <td>{{ $labaRugi >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</td>
                        <td class="text-right {{ $labaRugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
