<x-app-layout>
    <x-slot name="header">
        <div class="bg-sky-100 p-4 rounded">
            Tambah Entry Produk Keluar
        </div>
    </x-slot>

    <div class="bg-sky-100 p-6 rounded shadow">
        <form method="POST" action="{{ route('produk-keluar.store') }}">
            @csrf

            <div class="mb-4">
                <x-input-label for="id_transaksi" :value="__('ID Transaksi')" />
                <x-text-input id="id_transaksi" class="block mt-1 w-full" type="text" name="id_transaksi" :value="old('id_transaksi')" required autofocus autocomplete="id_transaksi" />
                <x-input-error :messages="$errors->get('id_transaksi')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="tanggal" :value="__('Tanggal')" />
                <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal')" required autocomplete="tanggal" />
                <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="kode_produk" :value="__('Produk')" />
                <select id="kode_produk" name="kode_produk" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Pilih Produk</option>
                    @foreach($inventoriesForSelect as $inventory)
                        <option value="{{ $inventory->kode_produk }}" data-id="{{ $inventory->id }}" data-nama="{{ $inventory->nama_produk }}" data-stok="{{ $inventory->jumlah }}" data-harga="{{ $inventory->harga }}" {{ old('kode_produk') == $inventory->kode_produk ? 'selected' : '' }}>
                            {{ $inventory->nama_produk }} ({{ $inventory->kode_produk }}) - Exp: {{ $inventory->tgl_expired ? \Carbon\Carbon::parse($inventory->tgl_expired)->format('d/m/Y') : '-' }} (Stok: {{ $inventory->jumlah }})
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="inventory_id" id="inventory_id_hidden" value="">
                <x-input-error :messages="$errors->get('kode_produk')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="nama_produk" :value="__('Nama Produk')" />
                <x-text-input id="nama_produk" class="block mt-1 w-full" type="text" name="nama_produk" :value="old('nama_produk')" readonly autocomplete="nama_produk" />
                <x-input-error :messages="$errors->get('nama_produk')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="jumlah_keluar" :value="__('Jumlah Produk')" />
                <x-text-input id="jumlah_keluar" class="block mt-1 w-full" type="number" name="jumlah_keluar" :value="old('jumlah_keluar')" required autocomplete="jumlah_keluar" min="1" />
                <x-input-error :messages="$errors->get('jumlah_keluar')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="harga" :value="__('Harga')" />
                <x-text-input id="harga" class="block mt-1 w-full" type="text" name="harga" :value="old('harga')" required autocomplete="harga" />
                <x-input-error :messages="$errors->get('harga')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="diskon_persen" :value="__('Diskon (%)')" />
                <x-text-input id="diskon_persen" class="block mt-1 w-full" type="text" name="diskon_persen" :value="old('diskon_persen', 0)" autocomplete="diskon_persen" placeholder="Contoh: 10" />
                <input type="hidden" name="diskon" id="diskon_hidden" value="{{ old('diskon', 0) }}">
                <x-input-error :messages="$errors->get('diskon_persen')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="total_harga" :value="__('Total Harga')" />
                <x-text-input id="total_harga" class="block mt-1 w-full" type="number" step="0.01" name="total_harga" :value="old('total_harga')" readonly autocomplete="total_harga" />
                <x-input-error :messages="$errors->get('total_harga')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Pilih Status</option>
                    <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_lunas" {{ old('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="jenis" :value="__('Jenis')" />
                <select id="jenis" name="jenis" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Pilih Jenis</option>
                    <option value="keluar" {{ old('jenis') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="masuk" {{ old('jenis') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                </select>
                <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <a href="{{ route('produk-keluar.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Batal</a>
                <x-primary-button>
                    {{ __('Simpan') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const idProdukSelect = document.getElementById('kode_produk');
            const namaProdukInput = document.getElementById('nama_produk');
            const jumlahKeluarInput = document.getElementById('jumlah_keluar');
            const hargaInput = document.getElementById('harga');
            const diskonPersenInput = document.getElementById('diskon_persen');
            const diskonHiddenInput = document.getElementById('diskon_hidden');
            const totalHargaInput = document.getElementById('total_harga');

            function updateProductInfo() {
                const selectedOption = idProdukSelect.options[idProdukSelect.selectedIndex];
                if (selectedOption.value) {
                    document.getElementById('inventory_id_hidden').value = selectedOption.getAttribute('data-id');
                    namaProdukInput.value = selectedOption.getAttribute('data-nama');
                    const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
                    const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                    jumlahKeluarInput.max = stok;
                    hargaInput.value = 'Rp ' + harga.toLocaleString('id-ID');
                    calculateTotal();
                } else {
                    document.getElementById('inventory_id_hidden').value = '';
                    namaProdukInput.value = '';
                    jumlahKeluarInput.max = '';
                    hargaInput.value = '';
                    totalHargaInput.value = '';
                }
            }

            function calculateTotal() {
                const jumlah = parseFloat(jumlahKeluarInput.value) || 0;
                const hargaStr = hargaInput.value.replace('Rp ', '').replace(/\./g, '').replace(',', '.');
                const harga = parseFloat(hargaStr) || 0;
                
                const subtotal = jumlah * harga;
                
                const diskonPersenStr = diskonPersenInput.value.replace('%', '').replace(',', '.');
                const diskonPersen = parseFloat(diskonPersenStr) || 0;
                
                const diskonAmount = subtotal * (diskonPersen / 100);
                const total = subtotal - diskonAmount;
                
                diskonHiddenInput.value = diskonAmount;
                totalHargaInput.value = 'Rp ' + Math.round(total).toLocaleString('id-ID');
            }

            idProdukSelect.addEventListener('change', updateProductInfo);
            jumlahKeluarInput.addEventListener('input', calculateTotal);
            hargaInput.addEventListener('input', calculateTotal);
            diskonPersenInput.addEventListener('input', calculateTotal);

            // Initialize on page load
            updateProductInfo();
        });
    </script>
</x-app-layout>
