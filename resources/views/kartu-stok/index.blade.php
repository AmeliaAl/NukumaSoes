<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8 border-b-2 border-gray-800 pb-4">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <h1 class="text-3xl font-extrabold text-gray-900 tracking-widest uppercase">KARTU STOK PRODUK</h1>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('kartu-stok.pdf', request()->all()) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-900 transition shadow-lg shadow-red-900/20">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    PDF
                                </a>
                                <div class="text-right">
                                    @php
                                        $parts     = explode('-', $periode);
                                        $bulanNum  = (int)($parts[1] ?? date('m'));
                                        $tahunNum  = $parts[0] ?? date('Y');
                                        $bulanList = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                        $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
                                    @endphp
                                    <h3 class="text-lg font-bold text-gray-600 uppercase tracking-wide">PERIODE: {{ $namaPeriode }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter --}}
                    <div class="bg-gray-50/50 p-6 rounded-xl border border-gray-100 mb-8">
                        <form method="GET" action="{{ route('kartu-stok.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                <div>
                                    <label for="periode" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Periode</label>
                                    <input type="month" name="periode" id="periode" value="{{ $periode }}"
                                        onchange="this.form.submit()"
                                        class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">Filter Kategori</label>
                                    <select id="kategori" name="kategori" onchange="this.form.submit()"
                                        class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->nama_kategori }}"
                                                {{ request('kategori') == $category->nama_kategori ? 'selected' : '' }}>
                                                {{ $category->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    @php $hasRows = count($rows) > 0; @endphp

                    @if($hasRows)
                        <div class="overflow-x-auto shadow-xl rounded-xl border border-green-200">
                            <table class="min-w-full border-collapse">
                                <thead>
                                    <tr class="bg-orange-100 text-gray-700 border-b border-gray-300">
                                        <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Tanggal Transaksi</th>
                                        <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">No Batch</th>
                                        <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Deskripsi</th>
                                        <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Tanggal Kemas</th>
                                        <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Expired</th>
                                        <th colspan="2" class="px-4 py-2 text-center text-sm font-bold uppercase tracking-wider border border-gray-300">Produk</th>
                                        <th rowspan="2" class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300">Sisa</th>
                                        <th rowspan="2" class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300 bg-orange-100">HPP</th>
                                        <th rowspan="2" class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300 bg-orange-100">Nilai Persediaan</th>
                                    </tr>
                                    <tr class="bg-orange-100 text-gray-700">
                                        <th class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300">Masuk</th>
                                        <th class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider border border-gray-300">Keluar</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($rows as $row)
                                        @php
                                            if ($row['is_header']) {
                                                $bgStyle     = $row['type'] === 'masuk'
                                                    ? 'background-color: #dcfce7;'
                                                    : 'background-color: #fee2e2;';
                                                $borderStyle = 'border-top: 2px solid #9ca3af;';
                                            } else {
                                                $bgStyle     = 'background-color: #f9fafb;';
                                                $borderStyle = '';
                                            }

                                            // Styling expired
                                            $expiredRaw    = $row['tgl_expired_raw'] ?? null;
                                            $isExpired     = $expiredRaw && \Carbon\Carbon::parse($expiredRaw)->isPast();
                                            $isNearExpired = $expiredRaw && !$isExpired && \Carbon\Carbon::parse($expiredRaw)->diffInDays(now()) <= 30;
                                            $expiredClass  = $isExpired
                                                ? 'bg-red-100 text-red-700'
                                                : ($isNearExpired ? 'bg-orange-100 text-orange-700' : 'bg-green-50 text-green-700');
                                        @endphp
                                        <tr style="{{ $bgStyle }} {{ $borderStyle }}" class="hover:brightness-95 transition-all border-b border-gray-200">

                                            {{-- Tanggal --}}
                                            <td class="px-4 py-3 text-sm text-center text-gray-900 border-x border-gray-200">
                                                @if($row['tanggal'])
                                                    {{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}
                                                @endif
                                            </td>

                                            {{-- No Batch --}}
                                            <td class="px-4 py-3 text-sm text-center font-mono font-bold text-gray-700 border-r border-gray-200 uppercase">
                                                @if($row['is_header'])
                                                    {{ $row['no_batch'] ?? '-' }}
                                                @else
                                                    <span class="text-gray-400 text-xs">{{ $row['no_batch'] ?? '-' }}</span>
                                                @endif
                                            </td>

                                            {{-- Deskripsi --}}
                                            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                                                @if($row['keterangan'])
                                                    {{ $row['keterangan'] }}
                                                @endif
                                            </td>

                                            {{-- Tanggal Kemas --}}
                                            <td class="px-4 py-3 text-sm text-center border-r border-gray-200">
                                                @if(!empty($row['tgl_masuk']))
                                                    {{ \Carbon\Carbon::parse($row['tgl_masuk'])->format('d/m/Y') }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>

                                            {{-- Expired --}}
                                            <td class="px-4 py-3 text-sm text-center border-r border-gray-200">
                                                @if(!empty($row['tgl_expired']))
                                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $expiredClass }}">
                                                        {{ $row['tgl_expired'] }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>

                                            {{-- Masuk --}}
                                            <td class="px-4 py-3 text-sm text-center font-semibold text-green-700 border-r border-gray-200">
                                                @if(!is_null($row['masuk']))
                                                    <span>{{ $row['masuk'] }}</span>
                                                @endif
                                            </td>

                                            {{-- Keluar --}}
                                            <td class="px-4 py-3 text-sm text-center font-semibold text-red-700 border-r border-gray-200">
                                                @if(!is_null($row['keluar']))
                                                    <span>{{ $row['keluar'] }}</span>
                                                @endif
                                            </td>

                                            {{-- Sisa --}}
                                            <td class="px-4 py-3 text-sm text-center font-bold text-gray-900 border-r border-gray-200 bg-gray-50/50">
                                                {{ $row['sisa'] }}
                                            </td>

                                            {{-- HPP --}}
                                            <td class="px-4 py-3 text-sm text-right font-semibold text-gray-700 border-r border-gray-200 bg-orange-50">
                                                @if(!empty($row['hpp']))
                                                    Rp{{ number_format($row['hpp'], 0, ',', '.') }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>

                                            {{-- Nilai Persediaan --}}
                                            <td class="px-4 py-3 text-sm text-right font-bold text-[#7a0e14] border-r border-gray-200 bg-orange-50">
                                                @if(($row['nilai_persediaan'] ?? 0) > 0)
                                                    Rp{{ number_format($row['nilai_persediaan'], 0, ',', '.') }}
                                                @else
                                                    <span class="text-gray-500 font-bold">Rp0</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    {{-- Total produk masuk --}}
                                    <tr class="bg-green-100 border-t-2 border-green-300">
                                        <td colspan="5" class="px-4 py-3 text-sm font-bold text-green-800 text-right border border-gray-200 uppercase tracking-wide">
                                            Total Nilai Produk Masuk
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-bold text-green-700 border border-gray-200 bg-green-100/50">
                                            {{ number_format($totalMasuk, 0, ',', '.') }}
                                        </td>
                                        <td class="border border-gray-200 bg-green-100"></td>
                                        <td class="border border-gray-200 bg-green-100"></td>
                                        <td class="border border-gray-200 bg-green-100"></td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-green-700 border border-gray-200 bg-green-100">
                                            Rp{{ number_format($totalNilaiMasuk, 0, ',', '.') }}
                                        </td>
                                    </tr>

                                    {{-- Total produk keluar --}}
                                    <tr class="bg-red-50 border-t-2 border-red-200">
                                        <td colspan="6" class="px-4 py-3 text-sm font-bold text-red-800 text-right border border-gray-200 uppercase tracking-wide">
                                            Total Nilai Produk Keluar
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-bold text-red-700 border border-gray-200 bg-red-50/50">
                                            {{ number_format($totalKeluar, 0, ',', '.') }}
                                        </td>
                                        <td class="border border-gray-200 bg-red-50"></td>
                                        <td class="border border-gray-200 bg-red-50"></td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-red-700 border border-gray-200 bg-red-50">
                                            Rp{{ number_format($totalNilaiKeluar, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Data</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi untuk periode ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
