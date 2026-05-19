<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8 border-b-2 border-gray-800 pb-4">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <h1 class="text-3xl font-extrabold text-gray-900 tracking-widest uppercase">KARTU STOK PRODUK</h1>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('kartu-stok.pdf', request()->all()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-900 active:bg-red-950 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg shadow-red-900/20">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    PDF
                                </a>
                                <div class="text-right">
                                    @php
                                        $parts = explode('-', $periode);
                                        $bulanNum = (int) ($parts[1] ?? date('m'));
                                        $tahunNum = $parts[0] ?? date('Y');
                                        $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                                        $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
                                    @endphp
                                    <h3 class="text-lg font-bold text-gray-600 uppercase tracking-wide">PERIODE: {{ $namaPeriode }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50/50 p-6 rounded-xl border border-gray-100 mb-8">
                        <form method="GET" action="{{ route('kartu-stok.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                <div>
                                    <label for="periode" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Periode</label>
                                    <input type="month" name="periode" id="periode" value="{{ $periode }}" onchange="this.form.submit()" class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">Filter Kategori</label>
                                    <select id="kategori" name="kategori" onchange="this.form.submit()" class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->nama_kategori }}" {{ request('kategori') == $category->nama_kategori ? 'selected' : '' }}>
                                                {{ $category->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    @php $hasEntries = count($entries) > 0; @endphp
                    @if($hasEntries)

                        <div class="overflow-x-auto shadow-xl rounded-xl border border-green-200">
                            <table class="min-w-full border-collapse">
                                <thead>
                                    <!-- Main Header with Orange Theme (Matching Jurnal Umum) -->
                                    <tr class="bg-orange-100 text-gray-700 border-b border-gray-300">
                                        <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Tanggal</th>
                                        <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Deskripsi</th>
                                        <th colspan="3" class="px-4 py-2 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Produk</th>
                                    </tr>
                                    <!-- Sub Header -->
                                    <tr class="bg-orange-100 text-gray-700">
                                        <th class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300">Masuk</th>
                                        <th class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300">Keluar</th>
                                        <th class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300">Sisa</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @php $currentBalance = 0; @endphp
                                    @forelse($entries as $entry)
                                        @php
                                            $rowStyle = $entry['masuk'] > 0 ? 'background-color: #dcfce7;' : ($entry['keluar'] > 0 ? 'background-color: #fee2e2;' : '');
                                        @endphp
                                        <tr style="{{ $rowStyle }}" class="hover:brightness-95 transition-all border-b border-gray-200">
                                            <!-- Tanggal -->
                                            <td class="px-4 py-4 text-sm text-center text-gray-900 border-x border-gray-200">
                                                {{ \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}
                                            </td>
                                            
                                            <!-- Deskripsi -->
                                            <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-200">
                                                {{ $entry['keterangan'] }}
                                            </td>

                                            <!-- Produk Masuk -->
                                            <td class="px-4 py-4 text-sm text-center font-semibold text-green-700 border-r border-gray-200">
                                                {{ $entry['masuk'] > 0 ? $entry['masuk'] : '0' }}
                                            </td>
                                            
                                            <!-- Produk Keluar -->
                                            <td class="px-4 py-4 text-sm text-center font-semibold text-red-700 border-r border-gray-200">
                                                {{ $entry['keluar'] > 0 ? $entry['keluar'] : '0' }}
                                            </td>
                                            
                                            <!-- Sisa (Saldo) -->
                                            <td class="px-4 py-4 text-sm text-center font-bold text-gray-900 border-r border-gray-200 bg-gray-50/50">
                                                {{ $entry['saldo'] ?? '0' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                                Belum ada transaksi untuk periode ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Silakan Pilih Produk</h3>
                            <p class="mt-1 text-sm text-gray-500">Pilih salah satu produk dari dropdown di atas untuk melihat kartu stok.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
