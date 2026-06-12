<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="flex justify-between items-center mb-6">
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-center border-b-2 border-gray-800 pb-4 mb-8">
                        <div class="mb-4 md:mb-0 w-full md:w-1/4">
                            <form method="GET" action="{{ route('laporan.laba-rugi') }}" class="flex items-end space-x-2" id="filterForm">
                                <div class="w-full max-w-xs">
                                    <label for="periode" class="block text-sm font-medium text-gray-700">Pilih Periode</label>
                                    <input type="month" name="periode" id="periode" value="{{ $periode }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="document.getElementById('filterForm').submit()">
                                </div>
                            </form>
                        </div>

                        <!-- Konten Laporan (Kanan) -->
                        <div class="w-full md:w-3/4 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                            <div>
                                <h2 class="text-3xl font-black text-[#7a0e14] tracking-tight uppercase">NUKUMA SOES</h2>
                                <h2 class="text-xl font-bold text-gray-800 uppercase">LAPORAN LABA RUGI</h2>
                                @php
                                    $parts = explode('-', $periode);
                                    $bulanNum = (int) ($parts[1] ?? date('m'));
                                    $tahunNum = $parts[0] ?? date('Y');
                                    $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                                    $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
                                @endphp
                                <p class="text-gray-500 font-medium">Periode: <span class="text-[#d4af37]">{{ strtoupper($namaPeriode) }}</span></p>
                            </div>
                            <div class="flex items-center space-x-6 bg-white/50 p-4 rounded-xl border border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-[#fef3c7] border border-[#fcd34d] rounded"></div>
                                    <span class="text-xs text-gray-600 font-bold uppercase">Input Manual</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-white border border-gray-300 rounded"></div>
                                    <span class="text-xs text-gray-600 font-bold uppercase">Hitung Otomatis</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .input-manual {
                            background-color: #fef3c7 !important; /* amber-100 */
                            border: 1px solid #fcd34d !important; /* amber-300 */
                            border-radius: 4px;
                            padding: 2px 8px !important;
                            transition: all 0.2s;
                        }
                        .input-manual:focus {
                            background-color: #fffbeb !important; /* amber-50 */
                            border-color: #d4af37 !important;
                            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
                            outline: none;
                        }
                        .auto-calc {
                            font-weight: 800;
                            color: #1f2937;
                        }
                    </style>

                    <div class="w-full">
                        <form method="POST" action="{{ route('laporan.laba-rugi.manual.store') }}" id="labaRugiForm">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $periode }}">
                            
                            <div class="overflow-x-auto flex justify-center">
                                <div class="w-full max-w-5xl px-4 py-8 bg-[#fdfaf2] rounded-[30px] border border-[#d4af37]/20 shadow-inner">
                                    <table class="w-full text-sm font-medium text-gray-800" id="calcTable">
                                        <tbody>
                                            <!-- PENJUALAN BERSIH -->
                                            <tr class="font-bold border-b border-gray-300">
                                                <td class="py-3 px-4 uppercase tracking-wider">Penjualan Bersih</td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-4 text-right border-l border-gray-200 bg-white/50">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-2">Rp</span>
                                                        <input type="number" step="0.01" name="penjualan_bersih" id="penjualan_bersih" value="{{ $penjualanBersih }}" class="w-40 text-right border-none p-0 font-bold input-manual" oninput="calculateLabaRugi()">
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr class="bg-gray-50/50"><td colspan="5" class="py-2"></td></tr>

                                            <!-- HARGA POKOK PENJUALAN -->
                                            <tr class="font-bold">
                                                <td class="py-2 px-4" colspan="5">Harga Pokok Penjualan :</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 px-8">Persediaan Produk Jadi Awal</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="persediaan_produk_jadi_awal" id="persediaan_produk_jadi_awal" value="{{ $persediaanProdukJadiAwal }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>

                                            <!-- HARGA POKOK PRODUKSI -->
                                            <tr>
                                                <td class="py-2 px-12 font-bold" colspan="5">Harga Pokok Produksi :</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 px-16 italic">Persediaan BDP Awal</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="persediaan_bdp_awal" id="persediaan_bdp_awal" value="{{ $persediaanBDPAwal }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>

                                            <!-- BIAYA PRODUKSI -->
                                            <tr>
                                                <td class="py-1 px-16 text-gray-500" colspan="5">Biaya Produksi :</td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 px-20">&bull; Biaya Bahan Baku</td>
                                                <td class="py-1 px-4 text-right">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="biaya_bahan_baku" id="biaya_bahan_baku" value="{{ $biayaBahanBaku }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                    </div>
                                                </td>
                                                <td class="py-1 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-1 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-1 px-4 text-right border-l border-gray-200"></td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 px-20">&bull; Biaya Tenaga Kerja Langsung</td>
                                                <td class="py-1 px-4 text-right">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="biaya_tenaga_kerja_langsung" id="biaya_tenaga_kerja_langsung" value="{{ $biayaTenagaKerjaLangsung }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                    </div>
                                                </td>
                                                <td class="py-1 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-1 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-1 px-4 text-right border-l border-gray-200"></td>
                                            </tr>
                                            <tr class="border-b border-gray-300">
                                                <td class="py-1 px-20">&bull; Overhead Pabrik</td>
                                                <td class="py-1 px-4 text-right relative">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="biaya_overhead_pabrik" id="biaya_overhead_pabrik" value="{{ $biayaOverheadPabrik }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                        <span class="ml-1">+</span>
                                                    </div>
                                                </td>
                                                <td class="py-1 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-1 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-1 px-4 text-right border-l border-gray-200"></td>
                                            </tr>
                                            
                                            <!-- TOTAL BIAYA PRODUKSI -->
                                            <tr>
                                                <td class="py-2 px-16 font-bold">Biaya Produksi</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100 relative">
                                                    <span id="display_total_biaya_produksi" class="auto-calc">Rp {{ number_format($totalBiayaProduksi, 2, ',', '.') }}</span>
                                                    <span class="absolute -right-2 top-0">+</span>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>

                                            <tr class="border-t border-gray-400">
                                                <td class="py-2 px-16 italic">Jumlah Biaya Produksi</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100">
                                                    <span id="display_jumlah_biaya_produksi" class="auto-calc">Rp {{ number_format($persediaanBDPAwal + $totalBiayaProduksi, 2, ',', '.') }}</span>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>
                                            <tr class="border-b-2 border-gray-400">
                                                <td class="py-2 px-16 italic">Persediaan BDP Akhir</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100 relative">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="persediaan_bdp_akhir" id="persediaan_bdp_akhir" value="{{ $persediaanBDPAkhir }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                        <span class="ml-1">-</span>
                                                    </div>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>

                                            <!-- HARGA POKOK PRODUKSI FINAL -->
                                            <tr>
                                                <td class="py-2 px-12 font-bold uppercase tracking-tighter">Harga Pokok Produksi</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100 relative bg-gray-50/30">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="harga_pokok_produksi" id="harga_pokok_produksi" value="{{ $hargaPokokProduksi }}" class="w-32 text-right border-none p-0 font-bold input-manual" oninput="calculateLabaRugi()">
                                                        <span class="ml-1">+</span>
                                                    </div>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>

                                            <tr class="border-t border-gray-400">
                                                <td class="py-2 px-8 font-bold italic">Barang Tersedia Dijual</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100">
                                                    <span id="display_barang_tersedia_dijual" class="auto-calc">Rp {{ number_format($persediaanProdukJadiAwal + $hargaPokokProduksi, 2, ',', '.') }}</span>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>
                                            <tr class="border-b-2 border-gray-500">
                                                <td class="py-2 px-8 font-bold italic">Persediaan Produk Jadi Akhir</td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-100 relative">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="persediaan_produk_jadi_akhir" id="persediaan_produk_jadi_akhir" value="{{ $persediaanProdukJadiAkhir }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                        <span class="ml-1">-</span>
                                                    </div>
                                                </td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>

                                            <!-- HARGA POKOK PENJUALAN FINAL -->
                                            <tr class="font-black bg-[#7a0e14]/5">
                                                <td class="py-3 px-4 uppercase tracking-widest text-[#7a0e14]">Harga Pokok Penjualan</td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-4 text-right border-l border-gray-200 relative text-[#7a0e14]">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="harga_pokok_penjualan" id="harga_pokok_penjualan" value="{{ $hargaPokokPenjualan }}" class="w-32 text-right border-none p-0 font-bold input-manual" oninput="calculateLabaRugi()">
                                                        <span class="ml-1 text-xl">-</span>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr class="bg-gray-200"><td colspan="5" class="py-0.5"></td></tr>

                                            <!-- LABA KOTOR -->
                                            <tr class="font-black text-xl bg-[#d4af37]/10">
                                                <td class="py-4 px-4 uppercase tracking-[0.2em]">Laba Kotor</td>
                                                <td class="py-4 px-2 text-right"></td>
                                                <td class="py-4 px-2 text-right"></td>
                                                <td class="py-4 px-2 text-right"></td>
                                                <td class="py-4 px-4 text-right border-l border-[#d4af37]">
                                                    <span id="display_laba_kotor" class="auto-calc text-2xl">Rp {{ number_format($labaKotor, 2, ',', '.') }}</span>
                                                </td>
                                            </tr>

                                            <tr class="bg-gray-50"><td colspan="5" class="py-4"></td></tr>

                                            <!-- BIAYA USAHA -->
                                            <tr class="font-bold bg-gray-100/50">
                                                <td class="py-2 px-4" colspan="5">Biaya Usaha :</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2 px-8">Biaya Pemasaran</td>
                                                <td class="py-2 px-4 text-right">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="biaya_pemasaran" id="biaya_pemasaran" value="{{ $biayaPemasaran }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>
                                            <tr class="border-b border-gray-300">
                                                <td class="py-2 px-8">Biaya Administrasi dan Umum</td>
                                                <td class="py-2 px-4 text-right relative">
                                                    <div class="flex items-center justify-end">
                                                        <span class="mr-1 text-xs text-gray-400">Rp</span>
                                                        <input type="number" step="0.01" name="biaya_adm_umum" id="biaya_adm_umum" value="{{ $biayaAdmUmum }}" class="w-32 text-right border-none p-0 input-manual" oninput="calculateLabaRugi()">
                                                        <span class="ml-1">+</span>
                                                    </div>
                                                </td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-2 text-right"></td>
                                                <td class="py-2 px-4 text-right border-l border-gray-200"></td>
                                            </tr>
                                            <tr class="font-bold border-b-2 border-gray-800">
                                                <td class="py-3 px-4">Total Biaya Usaha</td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-2 text-right"></td>
                                                <td class="py-3 px-4 text-right border-l border-gray-200 relative">
                                                    <span id="display_total_biaya_usaha" class="auto-calc">Rp {{ number_format($totalBiayaUsaha, 2, ',', '.') }}</span>
                                                    <span class="absolute -right-2 top-0 text-xl">-</span>
                                                </td>
                                            </tr>

                                            <!-- LABA BERSIH USAHA -->
                                            <tr class="font-black text-2xl bg-gradient-to-r from-[#7a0e14] to-[#4a080c] text-[#d4af37] shadow-[0_-10px_20px_rgba(0,0,0,0.2)]">
                                                <td class="py-6 px-6 uppercase tracking-[0.3em]">Laba Bersih Usaha</td>
                                                <td class="py-6 px-2 text-right"></td>
                                                <td class="py-6 px-2 text-right"></td>
                                                <td class="py-6 px-2 text-right"></td>
                                                <td class="py-6 px-6 text-right border-l border-[#d4af37]/50 drop-shadow-xl">
                                                    <span id="display_laba_bersih_usaha" class="font-black text-3xl text-[#d4af37] drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)]">
                                                        @if($labaBersihUsaha < 0)
                                                            - Rp {{ number_format(abs($labaBersihUsaha), 2, ',', '.') }}
                                                        @else
                                                            Rp {{ number_format($labaBersihUsaha, 2, ',', '.') }}
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-center">
                                <button type="submit" class="bg-[#d4af37] hover:bg-[#b8962d] text-white font-bold py-3 px-12 rounded-full shadow-lg transform transition-all hover:scale-105 active:scale-95 flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    <span>Simpan Laporan Manual</span>
                                </button>
                            </div>
                        </form>

                        <div class="mt-12 flex justify-end space-x-4">
                            <a href="{{ route('laporan.laba-rugi.export.pdf', ['periode' => $periode]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                Export PDF
                            </a>
                            <a href="{{ route('laporan.laba-rugi.export.excel', ['periode' => $periode]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                Export Excel
                            </a>
                        </div>
                    </div>

                    <script>
                        function formatRupiah(number) {
                            const isNegative = number < 0;
                            const absNumber = Math.abs(number);
                            const formatted = new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(absNumber);
                            return (isNegative ? '- Rp ' : 'Rp ') + formatted;
                        }

                        function calculateLabaRugi() {
                            // Get input values
                            const penjualanBersih = parseFloat(document.getElementById('penjualan_bersih').value) || 0;
                            const pjaAwal = parseFloat(document.getElementById('persediaan_produk_jadi_awal').value) || 0;
                            const bdpAwal = parseFloat(document.getElementById('persediaan_bdp_awal').value) || 0;
                            const biayaBB = parseFloat(document.getElementById('biaya_bahan_baku').value) || 0;
                            const biayaBTKL = parseFloat(document.getElementById('biaya_tenaga_kerja_langsung').value) || 0;
                            const biayaBOP = parseFloat(document.getElementById('biaya_overhead_pabrik').value) || 0;
                            const bdpAkhir = parseFloat(document.getElementById('persediaan_bdp_akhir').value) || 0;
                            const pjaAkhir = parseFloat(document.getElementById('persediaan_produk_jadi_akhir').value) || 0;
                            const biayaPem = parseFloat(document.getElementById('biaya_pemasaran').value) || 0;
                            const biayaAdm = parseFloat(document.getElementById('biaya_adm_umum').value) || 0;

                            // 1. Total Biaya Produksi
                            const totalBiayaProduksi = biayaBB + biayaBTKL + biayaBOP;
                            document.getElementById('display_total_biaya_produksi').innerText = formatRupiah(totalBiayaProduksi);

                            // 2. Jumlah Biaya Produksi
                            const jumlahBiayaProduksi = bdpAwal + totalBiayaProduksi;
                            document.getElementById('display_jumlah_biaya_produksi').innerText = formatRupiah(jumlahBiayaProduksi);

                            // 3. Harga Pokok Produksi (Now manual, but can auto-calculate if needed)
                            // const hargaPokokProduksi = jumlahBiayaProduksi - bdpAkhir;
                            // document.getElementById('harga_pokok_produksi').value = hargaPokokProduksi.toFixed(2);
                            const hargaPokokProduksi = parseFloat(document.getElementById('harga_pokok_produksi').value) || 0;

                            // 4. Barang Tersedia Dijual
                            const barangTersediaDijual = pjaAwal + hargaPokokProduksi;
                            document.getElementById('display_barang_tersedia_dijual').innerText = formatRupiah(barangTersediaDijual);

                            // 5. Harga Pokok Penjualan (Now manual)
                            // const hargaPokokPenjualan = barangTersediaDijual - pjaAkhir;
                            // document.getElementById('harga_pokok_penjualan').value = hargaPokokPenjualan.toFixed(2);
                            const hargaPokokPenjualan = parseFloat(document.getElementById('harga_pokok_penjualan').value) || 0;

                            // 6. Laba Kotor
                            const labaKotor = penjualanBersih - hargaPokokPenjualan;
                            document.getElementById('display_laba_kotor').innerText = formatRupiah(labaKotor);

                            // 7. Biaya Usaha
                            const totalBiayaUsaha = biayaPem + biayaAdm;
                            document.getElementById('display_total_biaya_usaha').innerText = formatRupiah(totalBiayaUsaha);

                            // 8. Laba Bersih Usaha
                            const labaBersihUsaha = labaKotor - totalBiayaUsaha;
                            document.getElementById('display_laba_bersih_usaha').innerText = formatRupiah(labaBersihUsaha);
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
