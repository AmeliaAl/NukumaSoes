<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Akun</label>
                    <select wire:model.live="id_akun" class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="">-- Pilih Akun --</option>
                        @foreach ($this->getAkunList() as $akun)
                            <option value="{{ $akun->id_akun }}">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
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

        @if ($this->getSelectedAkun())
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <h3 class="text-lg font-bold">{{ $this->getSelectedAkun()->kode_akun }} - {{ $this->getSelectedAkun()->nama_akun }}</h3>
            <p class="text-sm text-gray-500">Tipe: {{ ucfirst($this->getSelectedAkun()->tipe_akun) }} | Saldo Normal: {{ ucfirst($this->getSelectedAkun()->saldo_normal) }}</p>
        </div>
        @endif

        {{-- Table --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Tanggal</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">No. Bukti</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Keterangan</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Debit</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Kredit</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($this->getBukuBesarData() as $entry)
                        <tr>
                            <td class="px-4 py-2">{{ $entry->jurnalUmum->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $entry->jurnalUmum->nomor_bukti }}</td>
                            <td class="px-4 py-2">{{ $entry->jurnalUmum->keterangan }}</td>
                            <td class="px-4 py-2 text-right">{{ $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-2 text-right">{{ $entry->kredit > 0 ? 'Rp ' . number_format($entry->kredit, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-2 text-right font-semibold {{ $entry->saldo_berjalan < 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format(abs($entry->saldo_berjalan), 0, ',', '.') }}{{ $entry->saldo_berjalan < 0 ? ' (K)' : ' (D)' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">{{ $this->id_akun ? 'Tidak ada transaksi untuk akun ini.' : 'Silakan pilih akun terlebih dahulu.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
