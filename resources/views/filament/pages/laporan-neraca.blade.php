<x-filament-panels::page>
    <div class="space-y-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex gap-4 items-end">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Tanggal</label>
                    <input type="date" wire:model.live="tanggal" class="mt-1 block rounded-lg border-gray-300 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h2 class="text-xl font-bold text-center mb-2 dark:text-white">NERACA</h2>
            <p class="text-center text-sm text-gray-500 mb-6">Per {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>

            @php $aset = $this->getAsetData(); $kewajiban = $this->getKewajibanData(); $ekuitas = $this->getEkuitasData(); @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- ASET --}}
                <div>
                    <h3 class="font-bold text-lg mb-3 text-blue-700 dark:text-blue-400 border-b pb-2">ASET</h3>
                    <table class="w-full text-sm">
                        @foreach ($aset['items'] as $item)
                        <tr>
                            <td class="py-1">{{ $item['akun']->kode_akun }} - {{ $item['akun']->nama_akun }}</td>
                            <td class="py-1 text-right w-40">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-t-2 font-bold text-blue-600">
                            <td class="py-2">Total Aset</td>
                            <td class="py-2 text-right">Rp {{ number_format($aset['total'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>

                {{-- KEWAJIBAN & EKUITAS --}}
                <div>
                    <h3 class="font-bold text-lg mb-3 text-orange-700 dark:text-orange-400 border-b pb-2">KEWAJIBAN</h3>
                    <table class="w-full text-sm">
                        @foreach ($kewajiban['items'] as $item)
                        <tr>
                            <td class="py-1">{{ $item['akun']->kode_akun }} - {{ $item['akun']->nama_akun }}</td>
                            <td class="py-1 text-right w-40">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-t font-bold text-orange-600">
                            <td class="py-2">Total Kewajiban</td>
                            <td class="py-2 text-right">Rp {{ number_format($kewajiban['total'], 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <h3 class="font-bold text-lg mb-3 mt-4 text-purple-700 dark:text-purple-400 border-b pb-2">EKUITAS</h3>
                    <table class="w-full text-sm">
                        @foreach ($ekuitas['items'] as $item)
                        <tr>
                            <td class="py-1">{{ $item['akun']->kode_akun }} - {{ $item['akun']->nama_akun }}</td>
                            <td class="py-1 text-right w-40">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-t font-bold text-purple-600">
                            <td class="py-2">Total Ekuitas</td>
                            <td class="py-2 text-right">Rp {{ number_format($ekuitas['total'], 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <div class="border-t-2 border-double mt-4 pt-2">
                        <table class="w-full font-bold">
                            <tr>
                                <td>Total Kewajiban + Ekuitas</td>
                                <td class="text-right text-lg">Rp {{ number_format($kewajiban['total'] + $ekuitas['total'], 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Balance Check --}}
            @php $balanced = abs($aset['total'] - ($kewajiban['total'] + $ekuitas['total'])) < 0.01; @endphp
            <div class="mt-6 p-4 rounded-lg text-center {{ $balanced ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300' }}">
                <p class="font-bold text-lg">{{ $balanced ? '✅ BALANCE — Aset = Kewajiban + Ekuitas' : '❌ TIDAK BALANCE — Periksa kembali jurnal Anda' }}</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
