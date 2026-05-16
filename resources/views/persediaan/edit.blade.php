<x-app-layout>
    <x-slot name="header">
        Edit Produk Masuk
    </x-slot>

    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Edit Entry Produk Masuk</h2>

        <form action="{{ route('persediaan.update', $entry->id) }}" method="POST" class="mb-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Hidden Fields to maintain data integrity -->
                <input type="hidden" name="kode_produk" value="{{ $entry->kode_produk }}">
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
                <div>
                    <label for="bbb" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Biaya Bahan Baku (BBB)</label>
                    <input type="number" name="bbb" id="bbb" value="{{ $entry->bbb }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg" step="0.01">
                </div>
                <div>
                    <label for="btkl" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Biaya Tenaga Kerja (BTKL)</label>
                    <input type="number" name="btkl" id="btkl" value="{{ $entry->btkl }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg" step="0.01">
                </div>
                <div>
                    <label for="bop" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Biaya Overhead (BOP)</label>
                    <input type="number" name="bop" id="bop" value="{{ $entry->bop }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37] font-black text-lg" step="0.01">
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
</x-app-layout>
