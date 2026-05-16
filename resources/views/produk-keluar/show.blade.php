<x-app-layout>
    <x-slot name="header">
        Detail Entry Produk Keluar
    </x-slot>

    <div class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <h3 class="text-lg font-semibold">ID Transaksi: {{ $entry->id_transaksi }}</h3>
        </div>

        <div class="mb-4">
            <strong>Tanggal:</strong> {{ $entry->tanggal }}
        </div>

        <div class="mb-4">
            <strong>Kode Produk:</strong> {{ $entry->kode_produk }}
        </div>

        <div class="mb-4">
            <strong>Nama Produk:</strong> {{ $entry->nama_produk }}
        </div>

        <div class="mb-4">
            <strong>Jumlah:</strong> {{ $entry->jumlah_keluar }}
        </div>

        <div class="mb-4">
            <strong>Harga:</strong> Rp {{ number_format($entry->harga, 0, ',', '.') }}
        </div>

        <div class="mb-4">
            <strong>Diskon:</strong> {{ $entry->diskon_persen }}% (Rp {{ number_format($entry->diskon, 0, ',', '.') }})
        </div>

        <div class="mb-4">
            <strong>Persediaan Produk Jadi:</strong> Rp {{ number_format($entry->harga_persediaan_produk_jadi, 0, ',', '.') }}
        </div>

        <div class="mb-4">
            <strong>Harga Pokok/Pack:</strong> Rp {{ number_format($entry->harga_pokok_per_pack, 2, ',', '.') }}
        </div>

        <div class="mb-4">
            <strong>Persediaan Produk Keluar:</strong> Rp {{ number_format(($entry->jumlah_pack_keluar ?? $entry->jumlah_keluar) * $entry->harga_pokok_per_pack, 2, ',', '.') }}
        </div>

        <div class="mb-4">
            <strong>Total Harga Jual:</strong> Rp {{ number_format($entry->total_harga, 0, ',', '.') }}
        </div>

        <div class="mb-4">
            <strong>Jenis:</strong> {{ ucfirst($entry->jenis) }}
        </div>

        <div class="mb-4">
            <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $entry->status)) }}
        </div>

        <div class="flex items-center justify-end mt-6">
            <a href="{{ route('produk-keluar.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Kembali</a>
            <a href="{{ route('produk-keluar.edit', $entry->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
        </div>
    </div>
</x-app-layout>
