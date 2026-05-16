<x-app-layout>
    <x-slot name="header">
        Kurangi Stok Batch: {{ $inventory->kode_produk }}
    </x-slot>

    <div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Kurangi Stok dari Batch: {{ $inventory->nama_produk }}</h2>
        <p class="text-sm text-gray-600 mb-6 italic">Kode: {{ $inventory->kode_produk }} | Sisa Stok Saat Ini: {{ $inventory->jumlah }}</p>

        <form method="POST" action="{{ route('inventory-entry.store-keluar', $inventory) }}">
            @csrf

            <!-- ID TRANSAKSI -->
            <div class="mb-4">
                <x-input-label for="id_transaksi" :value="__('ID TRANSAKSI')" />
                <x-text-input id="id_transaksi" class="block mt-1 w-full" type="text" name="id_transaksi" :value="old('id_transaksi', 'TK-' . date('YmdHis'))" required autofocus />
                <x-input-error :messages="$errors->get('id_transaksi')" class="mt-2" />
            </div>

            <!-- TANGGAL ENTRY -->
            <div class="mb-4">
                <x-input-label for="tanggal" :value="__('TANGGAL ENTRY')" />
                <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal', date('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
            </div>

            <!-- JUMLAH KELUAR -->
            <div class="mb-4">
                <x-input-label for="jumlah_keluar" :value="__('JUMLAH KURANGI STOK')" />
                <x-text-input id="jumlah_keluar" class="block mt-1 w-full" type="number" name="jumlah_keluar" :value="old('jumlah_keluar')" required min="1" max="{{ $inventory->jumlah }}" />
                <p class="text-xs text-red-500 mt-1">Maksimum pengeluaran: {{ $inventory->jumlah }}</p>
                <x-input-error :messages="$errors->get('jumlah_keluar')" class="mt-2" />
            </div>

            <!-- HARGA (Jual) -->
            <div class="mb-4">
                <x-input-label for="harga" :value="__('HARGA JUAL SATUAN')" />
                <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga', $inventory->harga)" required min="0" />
                <x-input-error :messages="$errors->get('harga')" class="mt-2" />
            </div>

            <!-- STATUS PEMBAYARAN -->
            <div class="mb-4">
                <x-input-label for="status" :value="__('STATUS PEMBAYARAN')" />
                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_lunas" {{ old('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6 space-x-2">
                <a href="{{ route('persediaan-produk.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Batal</a>
                <x-primary-button class="bg-orange-600 hover:bg-orange-700">
                    {{ __('Simpan Produk Keluar') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
