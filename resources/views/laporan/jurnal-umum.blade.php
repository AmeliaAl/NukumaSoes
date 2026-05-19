<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        <div class="flex flex-col md:flex-row justify-between items-center border-b-2 border-gray-800 pb-4 mb-8">
                            <div class="mb-4 md:mb-0 w-full md:w-1/4">
                                <form action="{{ route('laporan.jurnal-umum') }}" method="GET" class="flex items-end space-x-2">
                                    <div class="w-full max-w-xs">
                                        <label for="periode" class="block text-sm font-medium text-gray-700">Pilih Periode</label>
                                        <input type="month" name="periode" id="periode" value="{{ $periode }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                                    </div>
                                </form>
                            </div>
                            
                            <div class="text-center w-full md:w-1/2 flex-grow">
                                <h1 class="text-3xl font-bold text-gray-900 uppercase tracking-widest">NUKUMA SOES</h1>
                                <h2 class="text-xl font-bold text-gray-800 uppercase mt-2">JURNAL UMUM</h2>
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
                    </div>

                    <!-- Journal Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-2 border-gray-800 text-sm">
                            <thead>
                                <tr class="bg-orange-100 border-b-2 border-gray-800">
                                    <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">TANGGAL</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">KETERANGAN</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">REF</th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-700 uppercase tracking-wider">DEBIT</th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-700 uppercase tracking-wider">KREDIT</th>
                                    <th class="px-4 py-3 text-center font-bold text-gray-700 uppercase tracking-wider">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-400">
                                @php 
                                    $lastGroup = null; 
                                    $rowColor = 'bg-white';
                                    $groupCounter = 0;
                                @endphp
                                @forelse($entries as $entry)
                                @php
                                    $currentGroup = $entry->id_transaksi ?? $entry->created_at->format('Y-m-d H:i:s');
                                    if ($lastGroup !== $currentGroup) {
                                        $groupCounter++;
                                        $rowColor = ($groupCounter % 2 == 0) ? 'bg-[#fdf9eb]' : 'bg-white';
                                        $lastGroup = $currentGroup;
                                        $isNewGroup = true;
                                    } else {
                                        $isNewGroup = false;
                                    }
                                @endphp
                                <tr class="{{ $rowColor }} hover:bg-[#ffeec2] transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-900 border-r border-gray-400">
                                        @if($isNewGroup)
                                            {{ \Carbon\Carbon::parse($entry->tanggal)->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-900 border-r border-gray-200 {{ $entry->kredit > 0 ? 'pl-20' : 'pl-4' }}">
                                        {{ $entry->keterangan }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-900 border-r border-gray-200">{{ $entry->ref }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900 border-r border-gray-200">
                                        @if($entry->debit > 0)
                                            {{ number_format($entry->debit, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900 border-r border-gray-200">
                                        @if($entry->kredit > 0)
                                            {{ number_format($entry->kredit, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <form action="{{ route('laporan.jurnal-umum.destroy', $entry->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus entri ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data jurnal.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-200 font-bold border-t-2 border-gray-800">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right uppercase">Total</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($entries->sum('debit'), 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($entries->sum('kredit'), 2, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Export Buttons -->
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('laporan.jurnal-umum.export.pdf', ['periode' => $periode]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export PDF
                        </a>
                        <a href="{{ route('laporan.jurnal-umum.export.excel', ['periode' => $periode]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition flex items-center">
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
