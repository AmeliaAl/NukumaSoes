<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Edit Pengeluaran</h1>
                        <a href="{{ route('pengeluaran.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
                    </div>

                    <form method="POST" action="{{ route('pengeluaran.update', $pengeluaran->id) }}">
                        @csrf
                        @method('PUT')



                        <div class="mb-4">
                            <label for="produk_expired" class="block text-sm font-medium text-gray-700">Produk Expired (Opsional)</label>
                            <input type="text" name="produk_expired" id="produk_expired" value="{{ old('produk_expired', $pengeluaran->produk_expired) }}" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   placeholder="Nama produk dan batch...">
                            @error('produk_expired')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="jumlah_expired" class="block text-sm font-medium text-gray-700">Jumlah Expired</label>
                                <input type="number" name="jumlah_expired" id="jumlah_expired" value="{{ old('jumlah_expired', $pengeluaran->jumlah_expired) }}" 
                                       class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       placeholder="0">
                                @error('jumlah_expired')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="harga_pokok_per_pack" class="block text-sm font-medium text-gray-700">HP Produksi</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="harga_pokok_per_pack" id="harga_pokok_per_pack" value="{{ old('harga_pokok_per_pack', $pengeluaran->harga_pokok_per_pack) }}" step="0.01" 
                                           list="cost_history_list"
                                           class="pl-10 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                           placeholder="0.00">
                                    <datalist id="cost_history_list">
                                        @foreach($costHistory as $cost)
                                            <option value="{{ number_format($cost, 2, '.', '') }}">Rp {{ number_format($cost, 2, ',', '.') }}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                                @error('harga_pokok_per_pack')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', $pengeluaran->nominal) }}" step="0.01" min="0" 
                                       class="pl-12 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm font-bold text-gray-700" required>
                            </div>
                            @error('nominal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_pengeluaran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengeluaran</label>
                            <input type="date" name="tanggal_pengeluaran" id="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', $pengeluaran->tanggal_pengeluaran->format('Y-m-d')) }}" 
                                   class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" required>
                            @error('tanggal_pengeluaran')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const jumlahInput = document.getElementById('jumlah_expired');
                                const hargaInput = document.getElementById('harga_pokok_per_pack');
                                const nominalInput = document.getElementById('nominal');

                                function calculateNominal() {
                                    const jumlah = parseFloat(jumlahInput.value) || 0;
                                    const harga = parseFloat(hargaInput.value) || 0;
                                    if (jumlah > 0 && harga > 0) {
                                        nominalInput.value = (jumlah * harga).toFixed(2);
                                        nominalInput.readOnly = true;
                                        nominalInput.classList.add('bg-gray-100');
                                    } else {
                                        nominalInput.readOnly = false;
                                        nominalInput.classList.remove('bg-gray-100');
                                    }
                                }

                                // Initial check
                                calculateNominal();

                                jumlahInput.addEventListener('input', calculateNominal);
                                hargaInput.addEventListener('input', calculateNominal);
                            });
                        </script>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
