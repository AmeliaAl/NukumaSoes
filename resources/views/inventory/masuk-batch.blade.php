<x-app-layout>
    <x-slot name="header">
        Tambah Stok Batch: {{ $inventory->kode_produk }}
    </x-slot>

    <div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Tambah Stok untuk Batch: {{ $inventory->nama_produk }}</h2>
        <p class="text-sm text-gray-600 mb-6 italic">Kode: {{ $inventory->kode_produk }} | Sisa Stok Saat Ini: {{ $inventory->jumlah }}</p>

        <form method="POST" action="{{ route('inventory-entry.store-masuk', $inventory) }}">
            @csrf

            <!-- ID TRANSAKSI -->
            <div class="mb-4">
                <x-input-label for="id_transaksi" :value="__('ID TRANSAKSI')" />
                <x-text-input id="id_transaksi" class="block mt-1 w-full" type="text" name="id_transaksi" :value="old('id_transaksi', 'TM-' . date('YmdHis'))" required autofocus />
                <x-input-error :messages="$errors->get('id_transaksi')" class="mt-2" />
            </div>

            <!-- TANGGAL ENTRY -->
            <div class="mb-4">
                <x-input-label for="tanggal" :value="__('TANGGAL ENTRY')" />
                <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal', date('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
            </div>

            <!-- JUMLAH MASUK -->
            <div class="mb-4">
                <x-input-label for="jumlah_masuk" :value="__('JUMLAH TAMBAH STOK')" />
                <x-text-input id="jumlah_masuk" class="block mt-1 w-full" type="number" name="jumlah_masuk" :value="old('jumlah_masuk')" required min="1" />
                <x-input-error :messages="$errors->get('jumlah_masuk')" class="mt-2" />
            </div>

            <!-- HARGA (Beli/HPP) -->
            <div class="mb-4">
                <x-input-label for="harga" :value="__('HARGA SATUAN')" />
                <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga', $inventory->harga)" required min="0" />
                <x-input-error :messages="$errors->get('harga')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6 space-x-2">
                <a href="{{ route('persediaan-produk.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Batal</a>
                <x-primary-button class="bg-green-600 hover:bg-green-700">
                    {{ __('Simpan Produk Masuk') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
