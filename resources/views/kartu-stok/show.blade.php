<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Detail Entry Kartu Stok</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <strong>Tanggal:</strong>
                            <p>{{ $kartuStok->tanggal }}</p>
                        </div>

                        <div>
                            <strong>Keterangan:</strong>
                            <p>{{ $kartuStok->keterangan }}</p>
                        </div>

                        <div>
                            <strong>ID Transaksi:</strong>
                            <p>{{ $kartuStok->id_transaksi }}</p>
                        </div>

                        <div>
                            <strong>Masuk:</strong>
                            <p>{{ $kartuStok->masuk ?: '-' }}</p>
                        </div>

                        <div>
                            <strong>Keluar:</strong>
                            <p>{{ $kartuStok->keluar ?: '-' }}</p>
                        </div>

                        <div>
                            <strong>Harga:</strong>
                            <p>Rp {{ number_format($kartuStok->harga, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <strong>Total Harga:</strong>
                            <p>Rp {{ number_format($kartuStok->total_harga, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <strong>No Batch:</strong>
                            <p>{{ $kartuStok->no_batch ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('kartu-stok.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                        <div class="space-x-2">
                            <a href="{{ route('kartu-stok.edit', $kartuStok) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('kartu-stok.destroy', $kartuStok) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus entry ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
