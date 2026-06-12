<x-filament-panels::page>
    @php
        $data = $this->getViewData();
        extract($data);
    @endphp

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Aset -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-600">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Total Aset</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalAset) }}</h3>
                        <p class="text-xs text-blue-600 mt-1">Unit</p>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Nilai Perolehan -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-600">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Nilai Perolehan</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalNilaiPerolehan, 0, ',', '.') }}</h3>
                        <p class="text-xs text-green-600 mt-1">Total Investasi</p>
                    </div>
                    <div class="p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Akumulasi Penyusutan -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-orange-600">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Akum. Penyusutan</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalNilaiPenyusutan, 0, ',', '.') }}</h3>
                        <p class="text-xs text-orange-600 mt-1">Depresiasi</p>
                    </div>
                    <div class="p-3 bg-orange-50 dark:bg-orange-900 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Nilai Buku -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-purple-600">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Nilai Buku</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalNilaiBuku, 0, ',', '.') }}</h3>
                        <p class="text-xs text-purple-600 mt-1">Nilai Neto</p>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Aset per Kategori -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 pb-3 border-b">Aset per Kategori</h3>
                <div class="h-80">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>

            <!-- Aset per Lokasi -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 pb-3 border-b">Aset per Lokasi</h3>
                <div class="h-80">
                    <canvas id="lokasiChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Table: Aset per Kategori -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 pb-3 border-b">Detail Aset per Kategori</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3 text-center">Jumlah Unit</th>
                            <th class="px-6 py-3 text-right">Nilai Perolehan</th>
                            <th class="px-6 py-3 text-right">Akum. Penyusutan</th>
                            <th class="px-6 py-3 text-right">Nilai Buku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asetPerKategori as $item)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium">{{ $item->kategoriAset->nama_kategori ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ number_format($item->jumlah) }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->total_nilai, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-orange-600">Rp {{ number_format($item->total_penyusutan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($item->total_nilai - $item->total_penyusutan, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data aset</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table: Aset Terbaru -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 pb-3 border-b">10 Aset Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">Kode Aset</th>
                            <th class="px-6 py-3">Nama Aset</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Lokasi</th>
                            <th class="px-6 py-3">Tgl Perolehan</th>
                            <th class="px-6 py-3 text-right">Harga Perolehan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asetTerbaru as $aset)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-mono">{{ $aset->kode_aset }}</td>
                            <td class="px-6 py-4 font-medium">{{ $aset->nama_aset }}</td>
                            <td class="px-6 py-4">{{ $aset->kategoriAset->nama_kategori ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $aset->lokasiAset->nama_lokasi ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $aset->tanggal_perolehan ? $aset->tanggal_perolehan->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($aset->nilai_perolehan, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data aset</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kategoriData = @json($kategoriChartData);
            const lokasiData = @json($lokasiChartData);

            // Chart: Kategori (Bar Chart)
            const kategoriCtx = document.getElementById('kategoriChart');
            if (kategoriCtx) {
                new Chart(kategoriCtx, {
                    type: 'bar',
                    data: {
                        labels: kategoriData.labels,
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: kategoriData.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Chart: Lokasi (Pie Chart)
            const lokasiCtx = document.getElementById('lokasiChart');
            if (lokasiCtx) {
                new Chart(lokasiCtx, {
                    type: 'pie',
                    data: {
                        labels: lokasiData.labels,
                        datasets: [{
                            data: lokasiData.data,
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(251, 191, 36, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(236, 72, 153, 0.8)',
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
