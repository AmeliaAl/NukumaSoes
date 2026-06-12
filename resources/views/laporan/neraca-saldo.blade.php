<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="flex justify-between items-center mb-6">
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-center border-b-2 border-gray-800 pb-4 mb-8">
                        <div class="mb-4 md:mb-0 w-full md:w-1/4">
                            <form method="GET" action="{{ route('laporan.neraca-saldo') }}" class="flex items-end space-x-2" id="filterForm">
                                <div class="w-full max-w-xs">
                                    <label for="periode" class="block text-sm font-medium text-gray-700">Pilih Periode</label>
                                    <input type="month" name="periode" id="periode" value="{{ $periode }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                                </div>
                            </form>
                        </div>

                        <!-- Konten Laporan (Kanan) -->
                        <div class="text-center w-full md:w-1/2 flex-grow">
                            <h2 class="text-3xl font-bold text-gray-900 uppercase tracking-widest">NUKUMA SOES</h2>
                            <h2 class="text-xl font-bold text-gray-800 uppercase mt-2">NERACA SALDO</h2>
                            @php
                                $parts = explode('-', $periode);
                                $bulanNum = (int) ($parts[1] ?? date('m'));
                                $tahunNum = $parts[0] ?? date('Y');
                                $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                                $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
                            @endphp
                            <h3 class="text-md text-gray-600 mt-1 uppercase font-semibold tracking-wide">PERIODE: {{ $namaPeriode }}</h3>
                        </div>

                        <div class="w-full md:w-1/4 hidden md:block"></div>
                    </div>

                    <div class="w-full">
                        <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300 text-sm">
                                    <thead>
                                        <tr class="bg-orange-100 border-b border-gray-300">
                                            <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-r border-gray-300">No</th>
                                            <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase tracking-wider border-r border-gray-300">Akun</th>
                                            <th class="px-4 py-3 text-right font-bold text-gray-700 uppercase tracking-wider border-r border-gray-300">Debit</th>
                                            <th class="px-4 py-3 text-right font-bold text-gray-700 uppercase tracking-wider">Kredit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($entries as $entry)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 border-r border-gray-200">{{ $entry['ref'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 border-r border-gray-200">{{ $entry['keterangan'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900 border-r border-gray-200">
                                                {{ $entry['debit'] > 0 ? number_format($entry['debit'], 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900">
                                                {{ $entry['kredit'] > 0 ? number_format($entry['kredit'], 0, ',', '.') : '-' }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data untuk periode ini.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="bg-gray-200 font-bold border-t-2 border-gray-300">
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-center uppercase border-r border-gray-300">Total</td>
                                            <td class="px-4 py-3 text-right border-r border-gray-300">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-right">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mt-6 flex justify-end space-x-4">
                                <a href="{{ route('laporan.neraca-saldo.export.pdf', ['periode' => $periode]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                    Export PDF
                                </a>
                                <a href="{{ route('laporan.neraca-saldo.export.excel', ['periode' => $periode]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                    Export Excel
                                </a>
                            </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
