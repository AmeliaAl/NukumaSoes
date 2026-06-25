<x-app-layout>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-10">
            
            <!-- HEADER SECTION -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="space-y-1">
                    <h2 class="text-4xl font-serif font-black text-[#d4af37] drop-shadow-lg tracking-tight">Monitoring FEFO</h2>
                    <p class="text-white/70 font-medium tracking-wide uppercase text-xs">Sistem Kendali Produk Berdasarkan First-Expired First-Out</p>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Produk -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-red-800 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Terdaftar</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $totalProdukTerdaftar }}</h3>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg shadow-sm">
                            <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Aman -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-green-600 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Produk Aman</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $produkAman }}</h3>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Akan Expired -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-yellow-500 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Akan Expired</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $produkAkanExpired }}</h3>
                            <p class="text-xs text-yellow-600 font-medium mt-1 italic">Tindakan Segera</p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-lg shadow-sm">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Expired -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-red-600 transition-transform hover:scale-[1.02]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Expired</p>
                            <h3 class="text-3xl font-serif font-bold text-gray-900">{{ $produkExpired }}</h3>
                            <p class="text-xs text-red-600 font-medium mt-1 italic">Kritis</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg shadow-sm">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN TABLE -->
            <div class="bg-white/95 backdrop-blur-md rounded-[40px] border-2 border-[#d4af37]/60 shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6] border-b-2 border-[#d4af37]/20">
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">ID</th>
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">PRODUK</th>
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KATEGORI</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KEMAS</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">EXP</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">SISA</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STATUS</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse($products as $index => $product)
                                <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                    <td class="px-6 py-5 text-center border-r border-[#d4af37]/10">
                                        <div class="w-10 h-10 rounded-full bg-[#7a0e14] flex items-center justify-center text-white font-bold text-xs shadow-md mx-auto border-2 border-[#d4af37]/30">
                                            {{ $product->kode_produk }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 border-r border-[#d4af37]/10">
                                        <span class="text-base font-bold text-gray-900 group-hover:text-[#7a0e14] transition-colors line-clamp-1 uppercase tracking-tight">{{ $product->nama_produk }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 truncate max-w-[150px] uppercase tracking-tighter">{{ $product->kategori }}</td>
                                    <td class="px-6 py-5 text-sm text-gray-700 border-r border-[#d4af37]/10 text-center font-bold">{{ $product->tgl_masuk ? $product->tgl_masuk->format('d/m/y') : '-' }}</td>
                                    <td class="px-6 py-5 text-sm text-black border-r border-[#d4af37]/10 text-center font-black">{{ $product->tgl_expired ? $product->tgl_expired->format('d/m/y') : '-' }}</td>
                                    <td class="px-6 py-5 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center uppercase tracking-tighter">{{ $product->sisa_hari ?? '-' }} Hr</td>
                                    <td class="px-6 py-5 text-center border-r border-[#d4af37]/10">
                                        @if($product->tgl_expired)
                                            @php
                                                $now = \Carbon\Carbon::now();
                                                $oneMonthFromNow = \Carbon\Carbon::now()->addMonth();
                                            @endphp
                                            @if($product->tgl_expired->gt($oneMonthFromNow))
                                                <div class="px-3 py-1 rounded-full bg-green-500 text-white text-[10px] font-black uppercase shadow-sm">Aman</div>
                                            @elseif($product->tgl_expired->isAfter($now))
                                                <div class="px-3 py-1 rounded-full bg-orange-500 text-white text-[10px] font-black uppercase shadow-sm animate-pulse">Peringatan</div>
                                            @elseif($product->tgl_expired->isToday())
                                                <div class="px-3 py-1 rounded-full bg-red-500 text-white text-[10px] font-black uppercase shadow-sm pulse">Hari Ini</div>
                                            @else
                                                <div class="px-3 py-1 rounded-full bg-red-600 text-white text-[10px] font-black uppercase shadow-sm">Expired</div>
                                            @endif
                                        @else
                                            <span class="text-xs font-semibold text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('inventory-entry.edit', $product->id) }}" class="p-2 rounded-xl text-[#b89553] hover:bg-[#d4af37] hover:text-white transition-all transform hover:scale-110" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path></svg>
                                            </a>
                                            <form action="{{ route('inventory-entry.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-xl text-[#ff6b6b] hover:bg-[#ff6b6b] hover:text-white transition-all transform hover:scale-110" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-gray-400 italic font-medium">Data monitoring FEFO belum tersedia.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CALCULATOR SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 pb-10">
                <div class="bg-white/10 backdrop-blur-2xl rounded-[40px] p-10 border border-white/20 shadow-xl">
                    <div class="relative z-10 space-y-8">
                        <div class="border-b border-white/10 pb-6">
                            <h2 class="text-3xl font-serif font-black text-[#d4af37] drop-shadow-sm">Kalkulator Estimasi Expired</h2>
                            <p class="text-white/50 text-[10px] uppercase tracking-widest mt-1">Gunakan untuk memproyeksikan tanggal kedaluwarsa</p>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Produk Select -->
                            <div class="space-y-3">
                                <label for="product_id" class="block text-[10px] font-black text-[#f3d9a2] uppercase tracking-[0.2em] ml-1">Pilih Produk</label>
                                <div class="relative group">
                                    <select id="product_id" class="w-full bg-white border-2 border-[#d4af37]/30 rounded-[22px] px-6 py-5 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none">
                                        <option value="">Status Manual / Pilih Produk</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-entry-date="{{ $product->tgl_masuk ? $product->tgl_masuk->format('Y-m-d') : '' }}" 
                                                    data-expiry-date="{{ $product->tgl_expired ? $product->tgl_expired->format('Y-m-d') : '' }}" 
                                                    data-name="{{ $product->nama_produk }}"
                                                    data-category="{{ $product->kategori }}">
                                                {{ $product->kode_produk }} - {{ $product->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-6 pointer-events-none text-gray-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black text-[#f3d9a2] uppercase tracking-[0.2em] ml-1">Nama Produk</label>
                                    <input type="text" id="product_name_display" readonly placeholder="..." class="w-full bg-white/10 border-2 border-white/5 rounded-[22px] px-6 py-5 text-white font-bold text-center">
                                </div>
                                <div class="space-y-3">
                                    <label for="entry_date" class="block text-[10px] font-black text-[#f3d9a2] uppercase tracking-[0.2em] ml-1">Tgl Kemas</label>
                                    <input type="text" id="entry_date" placeholder="dd/mm/yyyy" class="w-full bg-white border-2 border-white/20 rounded-[22px] px-6 py-5 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37]">
                                </div>
                            </div>

                            <div class="grid grid-cols-5 gap-6">
                                <div class="col-span-3 space-y-3">
                                    <label for="shelf_life" class="block text-[10px] font-black text-[#f3d9a2] uppercase tracking-[0.2em] ml-1">Masa Simpan</label>
                                    <input type="number" id="shelf_life" min="1" class="w-full bg-white border-2 border-white/20 rounded-[22px] px-6 py-5 text-gray-700 font-black text-2xl text-center focus:ring-4 focus:ring-[#d4af37]/20">
                                </div>
                                <div class="col-span-2 space-y-3">
                                    <label for="unit" class="block text-[10px] font-black text-[#f3d9a2] uppercase tracking-[0.2em] ml-1">Satuan</label>
                                    <div class="relative">
                                        <select id="unit" class="w-full bg-white border-2 border-[#d4af37]/30 rounded-[22px] px-6 py-5 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 appearance-none">
                                            <option value="days">Hari</option>
                                            <option value="weeks">Minggu</option>
                                            <option value="months">Bulan</option>
                                            <option value="years">Tahun</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button id="calculate_btn" class="w-full bg-[#5a0f12] hover:bg-[#4a080c] text-[#d4af37] font-black py-7 rounded-[28px] border-2 border-[#8b6e22] shadow-2xl transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-[0.2em] text-sm transform hover:scale-[1.03] active:scale-95 group">
                                <svg class="w-7 h-7 mb-1 group-hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <span>Hitung & Sinkronkan</span>
                            </button>

                            <div id="result" class="mt-8 p-8 bg-black/40 rounded-[30px] border-2 border-[#d4af37]/30 hidden animate-fadeIn scale-up">
                                <div class="grid grid-cols-2 gap-8 mb-6 pb-6 border-b border-white/10 font-serif items-center">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-[#d4af37] uppercase tracking-[0.2em]">Estimasi Expired</span>
                                        <span id="expiry_date" class="text-2xl font-black text-white"></span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-[9px] font-black text-[#d4af37] uppercase tracking-[0.2em]">Sisa Masa</span>
                                        <div class="flex items-baseline gap-1">
                                            <span id="days_remaining" class="text-4xl font-black text-white leading-none"></span>
                                            <span class="text-xs font-bold text-white/40 uppercase">Hari</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center gap-4">
                                    <span class="text-[9px] font-black text-[#f3d9a2] uppercase tracking-[0.3em]">Proyeksi Status</span>
                                    <div id="status_container" class="w-full flex justify-center">
                                        <div id="status_badge"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>

    <script>
        @if(isset($selectedProduct) && $selectedProduct)
            document.addEventListener('DOMContentLoaded', function() {
                const entryDateInput = document.getElementById('entry_date');
                const entryDate = '{{ $selectedProduct->tgl_masuk ? $selectedProduct->tgl_masuk->format('d/m/Y') : '' }}';
                if (entryDate) {
                    entryDateInput.value = entryDate;
                }
            });
        @endif

        document.getElementById('product_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const entryDateInput = document.getElementById('entry_date');
            const resultDiv = document.getElementById('result');
            const nameDisplay = document.getElementById('product_name_display');
            const shelfLifeInput = document.getElementById('shelf_life');
            const unitSelect = document.getElementById('unit');

            if (this.value === '') {
                entryDateInput.value = '';
                nameDisplay.value = '';
                shelfLifeInput.value = '';
                resultDiv.classList.add('hidden');
                return;
            }

            const name = selectedOption.getAttribute('data-name');
            nameDisplay.value = name || '';

            const category = selectedOption.getAttribute('data-category') || '';
            
            // Auto-fill shelf life based on category/packaging
            if (category.toLowerCase().includes('toples')) {
                shelfLifeInput.value = 6;
                unitSelect.value = 'months';
            } else if (category.toLowerCase().includes('pouch') || category.toLowerCase().includes('plastik')) {
                shelfLifeInput.value = 10;
                unitSelect.value = 'months';
            }

            const entryDate = selectedOption.getAttribute('data-entry-date');
            if (entryDate) {
                const [y, m, d] = entryDate.split('-');
                entryDateInput.value = `${d}/${m}/${y}`;
            }

            const expiryDate = selectedOption.getAttribute('data-expiry-date');
            if (expiryDate) {
                const expiryDateObj = new Date(expiryDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const diffTime = expiryDateObj.getTime() - today.getTime();
                const daysRemaining = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                updateCalculatorResult(expiryDateObj, daysRemaining);
            } else {
                resultDiv.classList.add('hidden');
            }
        });

        function updateCalculatorResult(expiryDate, daysRemaining) {
            const resultDiv = document.getElementById('result');
            const expiryDateSpan = document.getElementById('expiry_date');
            const daysRemainingSpan = document.getElementById('days_remaining');
            const statusBadge = document.getElementById('status_badge');

            const day = String(expiryDate.getDate()).padStart(2, '0');
            const month = String(expiryDate.getMonth() + 1).padStart(2, '0');
            const year = expiryDate.getFullYear();
            expiryDateSpan.textContent = `${day}/${month}/${year}`;

            daysRemainingSpan.textContent = daysRemaining;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const midNightExpiry = new Date(expiryDate);
            midNightExpiry.setHours(0,0,0,0);

            const oneMonthSoon = new Date(today);
            oneMonthSoon.setMonth(oneMonthSoon.getMonth() + 1);

            let badgeHTML = '';
            if (midNightExpiry > oneMonthSoon) {
                badgeHTML = `
                    <div class="px-10 py-3 rounded-full bg-green-500 text-white text-xs font-black uppercase shadow-lg">AMAN</div>`;
            } else if (daysRemaining > 0) {
                badgeHTML = `
                    <div class="px-10 py-3 rounded-full bg-orange-500 text-white text-xs font-black uppercase shadow-lg animate-pulse">AKAN EXPIRED</div>`;
            } else if (midNightExpiry.getTime() === today.getTime()) {
                badgeHTML = `
                    <div class="px-10 py-3 rounded-full bg-red-500 text-white text-xs font-black uppercase shadow-lg">EXPIRED HARI INI</div>`;
            } else {
                badgeHTML = `
                    <div class="px-10 py-3 rounded-full bg-red-600 text-white text-xs font-black uppercase shadow-lg">EXPIRED</div>`;
            }

            statusBadge.innerHTML = badgeHTML;
            resultDiv.classList.remove('hidden');
        }

        document.getElementById('calculate_btn').addEventListener('click', function() {
            const productIdSelect = document.getElementById('product_id');
            const entryDateInput = document.getElementById('entry_date');
            const shelfLifeInput = document.getElementById('shelf_life');
            const unitSelect = document.getElementById('unit');

            if (!entryDateInput.value || !shelfLifeInput.value) {
                alert('Silakan isi tanggal pengemasan dan masa simpan.');
                return;
            }

            const dateParts = entryDateInput.value.split('/');
            if (dateParts.length !== 3) {
                alert('Format tanggal harus dd/mm/yyyy');
                return;
            }
            
            const entryDay = parseInt(dateParts[0]);
            const entryMonth = parseInt(dateParts[1]) - 1;
            const entryYear = parseInt(dateParts[2]);
            const entryDate = new Date(entryYear, entryMonth, entryDay);
            
            if (isNaN(entryDate.getTime())) {
                alert('Tanggal tidak valid');
                return;
            }

            const shelfLife = parseInt(shelfLifeInput.value);
            const unit = unitSelect.value;
            const expiryDate = new Date(entryDate);

            switch (unit) {
                case 'days': expiryDate.setDate(expiryDate.getDate() + shelfLife); break;
                case 'weeks': expiryDate.setDate(expiryDate.getDate() + (shelfLife * 7)); break;
                case 'months': expiryDate.setMonth(expiryDate.getMonth() + shelfLife); break;
                case 'years': expiryDate.setFullYear(expiryDate.getFullYear() + shelfLife); break;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const midNightExpiry = new Date(expiryDate);
            midNightExpiry.setHours(0,0,0,0);
            
            const diffTime = midNightExpiry.getTime() - today.getTime();
            const daysRemaining = Math.floor(diffTime / (1000 * 60 * 60 * 24));

            updateCalculatorResult(expiryDate, daysRemaining);

            if (productIdSelect.value && productIdSelect.value !== '') {
                const expiryDateForAPI = expiryDate.toISOString().split('T')[0];

                fetch('/monitoring/update-expiry', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productIdSelect.value,
                        expiry_date: expiryDateForAPI
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const resultDiv = document.getElementById('result');
                        const successMessage = document.createElement('div');
                        successMessage.className = 'mt-6 p-4 bg-green-500/10 border border-green-500/40 text-green-400 rounded-2xl text-center text-[10px] font-black uppercase tracking-widest';
                        successMessage.innerHTML = '<span class="mr-2">✓</span> Data Berhasil Disinkronkan';
                        resultDiv.appendChild(successMessage);
                        setTimeout(() => { window.location.reload(); }, 1500);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    </script>
</x-app-layout>
