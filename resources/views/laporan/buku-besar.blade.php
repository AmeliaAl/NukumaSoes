<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6 text-center">
                        <h1 class="text-2xl font-bold text-gray-900">Buku Besar</h1>
                    </div>

                    <div class="mb-6 text-center">
                        <form method="GET" action="{{ route('laporan.buku-besar') }}" class="inline-block">
                            <div class="flex items-center space-x-4">
                                <label for="periode" class="text-sm font-medium">Periode:</label>
                                <input type="month" id="periode" name="periode" value="{{ $periode }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" onchange="this.form.submit()">
                                <label for="nama_akun" class="text-sm font-medium">Nama Akun:</label>
                                <select id="nama_akun" name="nama_akun" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" onchange="this.form.submit()">
                                    <option value="">-- Pilih Akun --</option>
                                    @foreach($akunJurnal as $akun)
                                        <option value="{{ $akun->keterangan }}" {{ request('nama_akun') == $akun->keterangan ? 'selected' : '' }}>
                                            {{ $akun->keterangan }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm hidden">Tampilkan</button>
                            </div>
                        </form>
                    </div>

                    <div class="mb-6 text-center border-b-2 border-gray-800 pb-4">
                        <h2 class="text-2xl font-bold uppercase tracking-widest">NUKUMA SOES</h2>
                        <h3 class="text-xl font-semibold uppercase mt-2">Buku Besar</h3>
                        @php
                            $parts = explode('-', $periode);
                            $bulanNum = (int) ($parts[1] ?? date('m'));
                            $tahunNum = $parts[0] ?? date('Y');
                            $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                            $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
                        @endphp
                        <p class="text-md font-medium uppercase mt-2">Periode: {{ $namaPeriode }}</p>
                        <p class="text-md font-medium uppercase">Nama Akun: {{ $namaAkun ?: 'PILIH AKUN' }}</p>
                    </div>

                    <table class="min-w-full bg-white table-auto border-2 border-gray-800">
                        <thead class="bg-orange-100 border-b-2 border-gray-800">
                            <tr>
                                <th rowspan="2" class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-400">Tanggal</th>
                                <th rowspan="2" class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-400">Keterangan</th>
                                <th rowspan="2" class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-400">Debit</th>
                                <th rowspan="2" class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-400">Kredit</th>
                                <th rowspan="2" class="px-6 py-3 text-center text-xs font-bold text-black uppercase tracking-wider border-r border-gray-400">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-400">
                            @forelse($entries ?? [] as $entry)
                                @if(isset($entry['is_saldo_akhir']) && $entry['is_saldo_akhir'])
                                    <tr class="bg-gray-200 text-black font-bold">
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm border-r border-gray-400 uppercase">SALDO AKHIR</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm border-r border-gray-400">
                                            {{ ($entry['saldo'] < 0 ? '-' : '') . 'Rp' . number_format(abs($entry['saldo']), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @else
                                    <tr class="{{ isset($entry['is_saldo_awal']) && $entry['is_saldo_awal'] ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-400">{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-400">{{ $entry['keterangan'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-black border-r border-gray-400">
                                            {{ $entry['debit'] > 0 ? 'Rp' . number_format($entry['debit'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-black border-r border-gray-400">
                                            {{ $entry['kredit'] > 0 ? 'Rp' . number_format($entry['kredit'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-black border-r border-gray-400">
                                            {{ ($entry['saldo'] < 0 ? '-' : '') . 'Rp' . number_format(abs($entry['saldo']), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada entry ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Export Buttons -->
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('laporan.buku-besar.export.pdf', ['periode' => $periode, 'nama_akun' => request('nama_akun')]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export PDF
                        </a>
                        <a href="{{ route('laporan.buku-besar.export.excel', ['periode' => $periode, 'nama_akun' => request('nama_akun')]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
