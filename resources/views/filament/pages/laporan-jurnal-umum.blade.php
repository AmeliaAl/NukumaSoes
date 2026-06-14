<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <form wire:submit.prevent="$refresh" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                    <input type="date" wire:model.live="tanggal_mulai" class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                    <input type="date" wire:model.live="tanggal_akhir" class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
            </form>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 text-center">
                <p class="text-sm text-gray-500">Total Jurnal</p>
                <p class="text-2xl font-bold text-primary-600">{{ $this->getJurnalData()->count() }}</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 text-center">
                <p class="text-sm text-gray-500">Total Debit</p>
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($this->getTotalDebit(), 0, ',', '.') }}</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 text-center">
                <p class="text-sm text-gray-500">Total Kredit</p>
                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($this->getTotalKredit(), 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Tanggal</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">No. Bukti</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Keterangan / Akun</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Debit</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($this->getJurnalData() as $jurnal)
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                            <td class="px-4 py-2 font-medium">{{ $jurnal->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 font-medium">{{ $jurnal->nomor_bukti }}</td>
                            <td class="px-4 py-2 font-medium" colspan="3">{{ $jurnal->keterangan }}</td>
                        </tr>
                        @foreach ($jurnal->detail as $detail)
                            <tr>
                                <td class="px-4 py-1"></td>
                                <td class="px-4 py-1"></td>
                                <td class="px-4 py-1 {{ $detail->kredit > 0 ? 'pl-12' : 'pl-8' }}">
                                    {{ $detail->akun->kode_akun ?? '' }} - {{ $detail->akun->nama_akun ?? '' }}
                                </td>
                                <td class="px-4 py-1 text-right">{{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '' }}</td>
                                <td class="px-4 py-1 text-right">{{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit, 0, ',', '.') : '' }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data jurnal untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100 dark:bg-gray-800 font-bold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">TOTAL</td>
                        <td class="px-4 py-3 text-right text-green-600">Rp {{ number_format($this->getTotalDebit(), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-blue-600">Rp {{ number_format($this->getTotalKredit(), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-filament-panels::page>
