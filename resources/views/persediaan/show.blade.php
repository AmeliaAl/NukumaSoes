<x-app-layout>
    <x-slot name="header">
        Detail Entry Produk Masuk
    </x-slot>

    <div class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <h3 class="text-lg font-semibold">ID Transaksi: {{ $entry->id_transaksi }}</h3>
        </div>

        <div class="mb-4">
            <strong>Tanggal:</strong> {{ $entry->tanggal }}
        </div>

        <div class="mb-4">
            <strong>No Batch:</strong> {{ $entry->kode_produk }}
        </div>

        <div class="mb-4">
            <strong>Nama Produk:</strong> {{ $entry->nama_produk }}
        </div>


        <div class="mb-4">
            <strong>Jumlah Pack Masuk:</strong> {{ number_format($entry->jumlah_masuk) }}
        </div>

        <div class="mb-4">
            <strong>Harga Per Pcs (Sesuai Kategori):</strong> Rp {{ number_format($entry->category_price, 0, ',', '.') }}
        </div>
        
        <div class="mb-4">
            <strong>Biaya Bahan Baku (BBB):</strong> Rp {{ number_format($entry->bbb, 0, ',', '.') }}
        </div>

        <div class="mb-4">
            <strong>Biaya Tenaga Kerja (BTKL):</strong> Rp {{ number_format($entry->btkl, 0, ',', '.') }}
        </div>

        <div class="mb-4">
            <strong>Biaya Overhead (BOP):</strong> Rp {{ number_format($entry->bop, 0, ',', '.') }}
        </div>

        <div class="mb-4 text-[#7a0e14] text-xl font-black">
            <strong>Total:</strong> Rp {{ number_format($entry->total_harga, 0, ',', '.') }}
        </div>

        <div class="flex items-center justify-end mt-6">
            <a href="{{ route('persediaan.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Kembali</a>
            <a href="{{ route('persediaan.edit', $entry->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
        </div>
    </div>
</x-app-layout>
