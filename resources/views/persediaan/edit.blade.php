<x-app-layout>
    <x-slot name="header">
        Edit Produk Masuk
    </x-slot>

    <div class="bg-white p-6 rounded shadow max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Edit Entry Produk Masuk</h2>

        <form action="{{ route('persediaan.update', $entry->id) }}" method="POST" class="mb-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- PRODUK ID (dropdown) -->
                <div class="lg:col-span-3">
                    <label for="produk_select" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Produk ID <span class="text-red-500">*</span></label>
                    <select id="produk_select" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-bold">
                        <option value="">-- Pilih Produk ID untuk auto-fill HPP --</option>
                        @foreach($inventories as $inv)
                            <option value="{{ $inv->id }}"
                                data-kode="{{ $inv->kode_produk }}"
                                data-nama="{{ $inv->nama_produk }}"
                                data-bbb="{{ $inv->product->bbb ?? 0 }}"
                                data-btkl="{{ $inv->product->btkl ?? 0 }}"
                                data-bop="{{ $inv->product->bop ?? 0 }}"
                                data-hpp="{{ $inv->product->hpp ?? 0 }}"
                                {{ $entry->kode_produk == $inv->kode_produk ? 'selected' : '' }}>
                                {{ $inv->kode_produk }} - {{ $inv->nama_produk }} - {{ $inv->no_batch ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden Fields to maintain data integrity -->
                <input type="hidden" name="kode_produk" id="kode_produk_hidden" value="{{ $entry->kode_produk }}">
                <input type="hidden" name="harga" value="{{ $entry->harga }}">
                <input type="hidden" name="total_harga" value="{{ $entry->total_harga }}">

                <div>
                    <label for="id_transaksi" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">ID Transaksi</label>
                    <input type="text" name="id_transaksi" id="id_transaksi" value="{{ $entry->id_transaksi }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-bold" required>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ $entry->tanggal }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-bold" required>
                </div>
                <div>
                    <label for="no_batch" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">No Batch</label>
                    <input type="text" name="no_batch" id="no_batch" value="{{ $entry->no_batch ?? $entry->kode_produk }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-bold" required>
                </div>
                <div class="md:col-span-2">
                    <label for="nama_produk" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" value="{{ $entry->nama_produk }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-bold uppercase" required>
                </div>
                <div>
                    <label for="jumlah_masuk" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Jumlah Pack Masuk</label>
                    <input type="number" name="jumlah_masuk" id="jumlah_masuk" value="{{ $entry->jumlah_masuk }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg" required>
                </div>

                <!-- HPP - AUTO FILL -->
                <div>
                    <label for="harga_pokok_produksi" class="block text-sm font-bold text-[#d4af37] mb-1 uppercase tracking-wider">
                        HP Produksi
                        <span class="text-xs text-gray-400 normal-case font-normal ml-1">(otomatis dari Produk ID)</span>
                    </label>
                    <input type="number" name="harga_pokok_produksi" id="harga_pokok_produksi"
                           value="{{ $entry->harga_pokok_produksi }}"
                           class="w-full border-2 border-[#d4af37]/50 bg-[#fdf9eb] rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg text-[#7a0e14]"
                           step="0.01">
                </div>

                <!-- BBB - AUTO FILL -->
                <div>
                    <label for="bbb" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">
                        Biaya Bahan Baku (BBB)
                    </label>
                    <input type="number" name="bbb" id="bbb" value="{{ $entry->bbb }}"
                           class="w-full border-2 border-[#d4af37]/30 bg-[#fdf9eb] rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg"
                           step="0.01">
                </div>

                <!-- BTKL - AUTO FILL -->
                <div>
                    <label for="btkl" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">
                        Biaya Tenaga Kerja (BTKL)
                    </label>
                    <input type="number" name="btkl" id="btkl" value="{{ $entry->btkl }}"
                           class="w-full border-2 border-[#d4af37]/30 bg-[#fdf9eb] rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg"
                           step="0.01">
                </div>

                <!-- BOP - AUTO FILL -->
                <div>
                    <label for="bop" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">
                        Biaya Overhead (BOP)
                    </label>
                    <input type="number" name="bop" id="bop" value="{{ $entry->bop }}"
                           class="w-full border-2 border-[#d4af37]/30 bg-[#fdf9eb] rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg"
                           step="0.01">
                </div>

                <div class="md:col-span-3 flex justify-end gap-4 pt-4 border-t mt-4">
                    <a href="{{ route('persediaan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-xl transition-all uppercase tracking-widest text-xs">
                        Kembali
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-black py-3 px-12 rounded-xl shadow-lg transition-all transform hover:scale-105 uppercase tracking-widest text-xs">
                        Update Entry
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const produkSelect = document.getElementById('produk_select');

            function autoFillFromProduct(option) {
                if (!option || !option.value) return;

                const bbb  = parseFloat(option.getAttribute('data-bbb'))  || 0;
                const btkl = parseFloat(option.getAttribute('data-btkl')) || 0;
                const bop  = parseFloat(option.getAttribute('data-bop'))  || 0;
                const hpp  = parseFloat(option.getAttribute('data-hpp'))  || 0;
                const kode = option.getAttribute('data-kode') || '';
                const nama = option.getAttribute('data-nama') || '';

                document.getElementById('harga_pokok_produksi').value = hpp  > 0 ? hpp  : '';
                document.getElementById('bbb').value  = bbb  > 0 ? bbb  : '';
                document.getElementById('btkl').value = btkl > 0 ? btkl : '';
                document.getElementById('bop').value  = bop  > 0 ? bop  : '';
                document.getElementById('kode_produk_hidden').value = kode;
                document.getElementById('nama_produk').value = nama;
            }

            produkSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                autoFillFromProduct(selectedOption);
            });

            // Auto-fill on load if a product is already selected
            if (produkSelect.value) {
                autoFillFromProduct(produkSelect.options[produkSelect.selectedIndex]);
            }
        });
    </script>
</x-app-layout>
