<x-app-layout>
    <div class="py-12">
    <div class="py-8 w-full">
        <div class="px-4 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="mb-6 bg-white p-4 rounded-2xl shadow-md border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-4 w-full">
                    <div class="flex-1 min-w-[200px]">
                        <label for="periode" class="block text-sm font-medium text-gray-700 mb-1">Pilih Periode</label>
                        <input type="month" name="periode" id="periode" value="{{ request('periode', $startDate->format('Y-m')) }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-none bg-red-800 text-white px-6 py-2.5 rounded-lg hover:bg-red-900 transition-colors shadow-sm font-medium text-sm flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filter
                        </button>
                        <a href="{{ route('dashboard') }}" class="flex-1 sm:flex-none bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition-colors shadow-sm font-medium text-sm flex justify-center items-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <!-- Card 1: Total Produk -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-red-800 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Produk</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $totalProduk }}</h3>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg">
                            <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Produk Aman -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-green-600 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Produk Aman</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $produkAman }}</h3>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Akan Expired -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-yellow-500 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Hampir Expired</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $produkAkanExpired }}</h3>
                            <p class="text-xs text-yellow-600 font-medium mt-1">Dalam 30 hari</p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Expired -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-red-600 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Produk Expired</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $produkExpired }}</h3>
                            <p class="text-xs text-red-600 font-medium mt-1">Perlu tindakan</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Total Transaksi -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-blue-600 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Transaksi</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $totalTransaksi }}</h3>
                            <p class="text-xs text-blue-600 font-medium mt-1">Masuk & Keluar</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <!-- Pie Chart -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-t-8 border-red-900">
                    <h3 class="text-2xl font-serif font-bold text-gray-800 mb-6 text-center border-b pb-4">Status Produk Expired</h3>
                    <div class="relative h-72 w-full flex justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-8 grid grid-cols-3 gap-4 text-center">
                        <div class="p-3 bg-green-50 rounded-xl shadow-inner">
                            <span class="block text-2xl font-bold text-green-700">{{ $produkAman }}</span>
                            <span class="text-xs text-green-600 font-medium uppercase">Aman</span>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-xl shadow-inner">
                            <span class="block text-2xl font-bold text-yellow-700">{{ $produkAkanExpired }}</span>
                            <span class="text-xs text-yellow-600 font-medium uppercase">Warning</span>
                        </div>
                        <div class="p-3 bg-red-50 rounded-xl shadow-inner">
                            <span class="block text-2xl font-bold text-red-700">{{ $produkExpired }}</span>
                            <span class="text-xs text-red-600 font-medium uppercase">Expired</span>
                        </div>
                    </div>
                </div>

                <!-- Transaction Chart -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-t-8 border-red-900">
                    <h3 class="text-2xl font-serif font-bold text-gray-800 mb-6 text-center border-b pb-4">Grafik Transaksi Produk<br><span class="text-sm font-normal text-gray-500">Periode {{ $startDate->format('F Y') }}</span></h3>
                    <div class="relative h-72 w-full">
                        <canvas id="transactionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('turbo:load', function () {
        console.log('Dashboard Blade Script: Turbo load detected');
        
        // Data from PHP
        const statusData = {!! json_encode($statusChartData) !!};
        const transactionData = {!! json_encode($transactionChartData ?? []) !!};

        // --- 1. Status Produk Chart ---
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: statusData.labels,
                    datasets: [{
                        data: statusData.data,
                        backgroundColor: ['#3498db', '#f39c12', '#e74c3c'],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                plugins: [typeof ChartDataLabels !== 'undefined' ? ChartDataLabels : {}],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 14 },
                            formatter: (value, context) => {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return percentage > 0 ? `${context.chart.data.labels[context.dataIndex]}\n${percentage}%` : '';
                            },
                            textAlign: 'center'
                        }
                    }
                }
            });
        }

        // --- 2. Transaction Chart ---
        const transCtx = document.getElementById('transactionChart');
        if (transCtx) {
            new Chart(transCtx, {
                type: 'line',
                data: {
                    labels: transactionData.labels,
                    datasets: [
                        {
                            label: 'Produk Masuk',
                            data: transactionData.masuk || [],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4
                        },
                        {
                            label: 'Produk Keluar',
                            data: transactionData.keluar || [],
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }
    });
</script>
</x-app-layout>
