<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Entry Kartu Stok</h1>

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('kartu-stok.update', $kartuStok) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $kartuStok->tanggal) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <select name="keterangan" id="keterangan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Pilih Keterangan</option>
                                <option value="MASUK" {{ old('keterangan', $kartuStok->keterangan) == 'MASUK' ? 'selected' : '' }}>MASUK</option>
                                <option value="KELUAR" {{ old('keterangan', $kartuStok->keterangan) == 'KELUAR' ? 'selected' : '' }}>KELUAR</option>
                                <option value="Retur" {{ old('keterangan', $kartuStok->keterangan) == 'Retur' ? 'selected' : '' }}>Retur</option>
                                <option value="Penyesuaian" {{ old('keterangan', $kartuStok->keterangan) == 'Penyesuaian' ? 'selected' : '' }}>Penyesuaian</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="id_transaksi" class="block text-sm font-medium text-gray-700">ID Transaksi</label>
                            <input type="text" name="id_transaksi" id="id_transaksi" value="{{ old('id_transaksi', $kartuStok->id_transaksi) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="masuk" class="block text-sm font-medium text-gray-700">Masuk</label>
                            <input type="number" name="masuk" id="masuk" value="{{ old('masuk', $kartuStok->masuk) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="0">
                        </div>

                        <div class="mb-4">
                            <label for="keluar" class="block text-sm font-medium text-gray-700">Keluar</label>
                            <input type="number" name="keluar" id="keluar" value="{{ old('keluar', $kartuStok->keluar) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="0">
                        </div>

                        <div class="mb-4">
                            <label for="harga" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                            <input type="number" name="harga" id="harga" value="{{ old('harga', $kartuStok->harga) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01" min="0" required>
                        </div>

                        <div class="mb-4">
                            <label for="no_batch" class="block text-sm font-medium text-gray-700">No Batch</label>
                            <input type="text" name="no_batch" id="no_batch" value="{{ old('no_batch', $kartuStok->no_batch) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('kartu-stok.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill masuk or keluar based on keterangan
        document.getElementById('keterangan').addEventListener('change', function() {
            const keterangan = this.value;
            const masukField = document.getElementById('masuk');
            const keluarField = document.getElementById('keluar');

            if (keterangan === 'MASUK') {
                keluarField.value = '';
                keluarField.disabled = true;
                masukField.disabled = false;
            } else if (keterangan === 'KELUAR' || keterangan === 'Retur') {
                masukField.value = '';
                masukField.disabled = true;
                keluarField.disabled = false;
            } else {
                masukField.disabled = false;
                keluarField.disabled = false;
            }
        });

        // Trigger on page load if keterangan is already selected
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('keterangan').dispatchEvent(new Event('change'));
        });
    </script>
</x-app-layout>
