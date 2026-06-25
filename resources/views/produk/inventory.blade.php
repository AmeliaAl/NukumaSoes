<x-app-layout>
    <x-slot name="header">
        Persediaan Produk
    </x-slot>

    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
                <div>
                    <h1 class="text-4xl font-serif font-bold text-[#d4af37] drop-shadow-md">Detail Persediaan Produk</h1>
                    <p class="text-sm text-gray-300 mt-1 uppercase tracking-wider font-medium">Monitoring Stok Berdasarkan Batch & Tanggal Expired</p>
                </div>

                <div class="flex flex-wrap items-center justify-center md:justify-end gap-4">
                    <!-- Modern Filter Form -->
                    <form action="{{ route('persediaan-produk.index') }}" method="GET" class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-1.5 rounded-xl border border-white/20 shadow-xl">
                        <select name="kategori" onchange="this.form.submit()" class="bg-transparent border-none text-white font-bold text-sm focus:ring-0 cursor-pointer min-w-[160px]">
                            <option value="" class="bg-[#2d1b15]">Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->nama_kategori }}" {{ request('kategori') == $category->nama_kategori ? 'selected' : '' }} class="bg-[#2d1b15]">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </form>

                    <a href="{{ route('inventory-entry.create') }}" 
                       class="inline-flex items-center px-6 py-2.5 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] border border-[#d4af37] rounded-xl font-bold text-sm text-[#d4af37] uppercase tracking-wider hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all shadow-[0_0_15px_rgba(212,175,55,0.3)]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Persediaan
                    </a>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white/95 backdrop-blur-md rounded-[40px] border-2 border-[#d4af37]/60 shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden">
                <div class="overflow-x-auto overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <table class="w-full min-w-[1400px] border-collapse relative">
                        <thead class="sticky top-0 z-20 shadow-sm">
                            <tr class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6] border-b-2 border-[#d4af37]/20">
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">ID</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">BATCH</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">PRODUK</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">RASA</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KATEGORI</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">JENIS PRODUK</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STOK AWAL</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">JUMLAH PER BATCH</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STOK SAAT INI</th>
                                <th class="px-4 py-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">HARGA PER PCS</th>
                                <th class="px-4 py-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">HPP</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KEMAS</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">EXP</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STATUS</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">SISA</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">PERMINTAAN PRODUKSI</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse($inventories as $index => $inventory)
                                <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                    <td class="px-4 py-5 text-center border-r border-[#d4af37]/10">
                                        <div class="w-10 h-10 rounded-full bg-[#7a0e14] flex items-center justify-center text-white font-bold text-xs shadow-md mx-auto border-2 border-[#d4af37]/30">
                                            {{ $inventory->kode_produk }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 uppercase tracking-tight">{{ $inventory->no_batch ?? '-' }}</td>
                                    <td class="px-4 py-5 border-r border-[#d4af37]/10">
                                        <span class="text-base font-bold text-gray-900 group-hover:text-[#7a0e14] transition-colors line-clamp-1 uppercase tracking-tight">{{ $inventory->nama_produk }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-base text-gray-600 border-r border-[#d4af37]/10 italic font-medium">{{ $inventory->rasa_produk ?? '-' }}</td>
                                    <td class="px-4 py-5 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 truncate max-w-[120px] uppercase tracking-tighter">{{ $inventory->kategori }}</td>
                                    <td class="px-4 py-5 border-r border-[#d4af37]/10 text-center">
                                         <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase inline-block {{ ($inventory->jenis_produk ?? 'Brand Sendiri') === 'Maklon' ? 'bg-[#7a0e14]/10 text-[#7a0e14] border border-[#7a0e14]/20' : 'bg-green-500/10 text-green-700 border border-green-500/20' }}">
                                             {{ $inventory->jenis_produk ?? 'Brand Sendiri' }}
                                         </span>
                                         @if(($inventory->jenis_produk ?? 'Brand Sendiri') === 'Maklon' && $inventory->maklon_id)
                                             <div class="block text-[11px] font-bold text-gray-500 mt-1.5 font-mono bg-[#7a0e14]/5 rounded-lg px-2 py-0.5 border border-[#7a0e14]/10 w-max mx-auto uppercase">
                                                 {{ $inventory->maklon_id }}
                                             </div>
                                         @endif
                                     </td>
                                    <td class="px-4 py-5 text-base text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center bg-[#d4af37]/10 group-hover:bg-[#ffeec2]">
                                        {{ $inventory->stok_awal ?? 0 }} pack
                                    </td>
                                    <td class="px-4 py-5 text-base text-black font-black border-r border-[#d4af37]/10 text-center">{{ $inventory->jumlah_per_batch }} pack</td>
                                    <td class="px-4 py-5 text-base text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center bg-[#d4af37]/10">
                                        {{ $inventory->jumlah }} pack
                                    </td>
                                    <td class="px-4 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right font-mono tracking-tighter">Rp {{ number_format($inventory->harga_per_pcs ?? $inventory->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right font-mono tracking-tighter">{{ $inventory->hpp !== null ? 'Rp ' . number_format($inventory->hpp, 0, ',', '.') : '-' }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-700 border-r border-[#d4af37]/10 text-center font-bold">{{ $inventory->tgl_masuk ? \Carbon\Carbon::parse($inventory->tgl_masuk)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-5 text-sm text-black border-r border-[#d4af37]/10 text-center font-black">{{ $inventory->tgl_expired ? \Carbon\Carbon::parse($inventory->tgl_expired)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-5 text-center border-r border-[#d4af37]/10">
                                        @php
                                            $expiredDate = \Carbon\Carbon::parse($inventory->tgl_expired);
                                            $oneMonthFromNow = \Carbon\Carbon::now()->addMonth();
                                            $now = \Carbon\Carbon::now();
                                        @endphp
                                        @if($expiredDate->gt($oneMonthFromNow))
                                            <div class="px-3 py-1 rounded-full bg-green-500 text-white text-[10px] font-black uppercase shadow-sm">Aman</div>
                                        @elseif($expiredDate->isAfter($now->endOfDay()))
                                            <div class="px-3 py-1 rounded-full bg-orange-500 text-white text-[10px] font-black uppercase shadow-sm animate-pulse">Hampir Expired</div>
                                        @else
                                            <div class="px-3 py-1 rounded-full bg-red-600 text-white text-[10px] font-black uppercase shadow-sm">Expired</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center">
                                        {{ $inventory->sisa_hari }} Hr
                                    </td>
                                    <td class="px-4 py-5 text-center border-r border-[#d4af37]/10 whitespace-nowrap">
                                        @if($expiredDate->isAfter($now->endOfDay()))
                                            <div x-data="{ 
                                                hasRequested: false, 
                                                qtyRequested: 0,
                                                init() {
                                                    const key = 'prod_req_' + '{{ $inventory->id }}';
                                                    const saved = localStorage.getItem(key);
                                                    if (saved) {
                                                        const data = JSON.parse(saved);
                                                        this.hasRequested = true;
                                                        this.qtyRequested = data.qty;
                                                    }
                                                }
                                            }">
                                                <template x-if="!hasRequested">
                                                    <button @click="$dispatch('open-prod-modal', { id: '{{ $inventory->id }}', nama: '{{ $inventory->nama_produk }}', rasa: '{{ $inventory->rasa_produk ?? '-' }}', kategori: '{{ $inventory->kategori ?? '-' }}', stok: '{{ $inventory->jumlah }}' })" 
                                                            class="inline-flex items-center justify-center p-2.5 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] text-[#d4af37] rounded-xl border border-[#d4af37] hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all transform hover:scale-110 active:scale-95 shadow-md"
                                                            title="Kirim Permintaan Produksi">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </button>
                                                </template>
                                                <template x-if="hasRequested">
                                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/20 text-amber-700 border border-amber-500/30 text-xs font-black uppercase rounded-lg animate-pulse"
                                                         @click="$dispatch('open-prod-modal', { id: '{{ $inventory->id }}', nama: '{{ $inventory->nama_produk }}', rasa: '{{ $inventory->rasa_produk ?? '-' }}', kategori: '{{ $inventory->kategori ?? '-' }}', stok: '{{ $inventory->jumlah }}', readOnly: true })"
                                                         style="cursor: pointer;"
                                                         title="Klik untuk detail permintaan">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                        Diproses (<span x-text="qtyRequested"></span> Pack)
                                                    </div>
                                                </template>
                                            </div>
                                        @else
                                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm select-none">
                                                Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('inventory-entry.edit', $inventory) }}" class="p-2 rounded-xl text-[#b89553] hover:bg-[#d4af37] hover:text-white transition-all transform hover:scale-110" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path></svg>
                                            </a>
                                            <form method="POST" action="{{ route('inventory-entry.destroy', $inventory) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-xl text-[#ff6b6b] hover:bg-[#ff6b6b] hover:text-white transition-all transform hover:scale-110" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p class="text-gray-400 italic font-medium">Data persediaan produk belum tersedia.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Permintaan Produksi Modal -->
    <div x-data="{ 
            isOpen: false, 
            reqId: '',
            prodId: '', 
            prodNama: '', 
            prodRasa: '', 
            prodKategori: '',
            prodStok: 0,
            qty: 50,
            notes: '',
            targetDate: '',
            readOnly: false,
            loading: false,
            successMessage: '',
            
            init() {
                // Set default target date to 3 days from now
                const d = new Date();
                d.setDate(d.getDate() + 3);
                this.targetDate = d.toISOString().split('T')[0];
            },
            open(data) {
                this.prodId = data.detail.id;
                this.prodNama = data.detail.nama;
                this.prodRasa = data.detail.rasa;
                this.prodKategori = data.detail.kategori;
                this.prodStok = data.detail.stok;
                this.readOnly = !!data.detail.readOnly;
                
                const key = 'prod_req_' + this.prodId;
                const saved = localStorage.getItem(key);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    this.reqId = parsed.reqId || 'REQ-' + Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
                    this.qty = parsed.qty;
                    this.notes = parsed.notes;
                    this.targetDate = parsed.targetDate;
                } else {
                    const now = new Date();
                    const dStr = now.getFullYear().toString().substr(-2) + 
                                 String(now.getMonth()+1).padStart(2,'0') + 
                                 String(now.getDate()).padStart(2,'0');
                    const rand = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                    this.reqId = 'REQ-' + dStr + '-' + rand;
                    
                    this.qty = data.detail.initQty || 50;
                    this.notes = '';
                    const d = new Date();
                    d.setDate(d.getDate() + 3);
                    this.targetDate = d.toISOString().split('T')[0];
                }
                
                this.isOpen = true;
                this.successMessage = '';
            },
            submit() {
                if (this.readOnly) return;
                this.loading = true;
                setTimeout(() => {
                    const data = {
                        reqId: this.reqId,
                        qty: this.qty,
                        notes: this.notes,
                        targetDate: this.targetDate,
                        timestamp: new Date().getTime()
                    };
                    localStorage.setItem('prod_req_' + this.prodId, JSON.stringify(data));
                    
                    this.loading = false;
                    this.successMessage = 'Permintaan Produksi berhasil dikirim!';
                    
                    setTimeout(() => {
                        this.isOpen = false;
                        window.location.reload();
                    }, 1200);
                }, 1000);
            },
            cancelRequest() {
                if (confirm('Apakah Anda yakin ingin membatalkan permintaan produksi ini?')) {
                    localStorage.removeItem('prod_req_' + this.prodId);
                    this.isOpen = false;
                    window.location.reload();
                }
            }
         }" 
         @open-prod-modal.window="open($event)"
         x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white/95 backdrop-blur-md rounded-[30px] border-2 border-[#d4af37] w-full max-w-lg shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-[#7a0e14] to-[#4a080c] p-6 text-white border-b border-[#d4af37]">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-serif font-black text-[#d4af37] tracking-wide uppercase">Permintaan Produksi</h3>
                        <p class="text-xs text-gray-300 mt-1 uppercase tracking-widest font-bold">Kirim formulir pesanan ke bagian dapur/produksi</p>
                    </div>
                    <button @click="isOpen = false" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <form @submit.prevent="submit()" class="p-6 space-y-5">
                <!-- Info Section -->
                <div class="bg-[#fdf9eb] rounded-2xl p-4 border border-[#d4af37]/30 space-y-2">
                    <div class="flex justify-between items-center text-sm border-b border-[#d4af37]/20 pb-2 mb-2">
                        <span class="text-gray-500 font-bold uppercase text-[11px] tracking-wide">ID Permintaan</span>
                        <span class="text-[#7a0e14] font-black uppercase font-mono tracking-wider" x-text="reqId"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-bold uppercase text-[11px] tracking-wide">Nama Produk</span>
                        <span class="text-gray-900 font-extrabold uppercase" x-text="prodNama"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-bold uppercase text-[11px] tracking-wide">Varian Rasa</span>
                        <span class="text-gray-900 font-bold italic" x-text="prodRasa"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-bold uppercase text-[11px] tracking-wide">Kategori</span>
                        <span class="text-[#7a0e14] font-bold uppercase" x-text="prodKategori"></span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-[#d4af37]/20 pt-2 mt-2">
                        <span class="text-gray-500 font-bold uppercase text-[11px] tracking-wide">Stok Saat Ini</span>
                        <span class="text-[#7a0e14] font-black" x-text="prodStok + ' Pack'"></span>
                    </div>
                </div>

                <template x-if="successMessage">
                    <div class="bg-green-100 border-2 border-green-500/30 text-green-800 p-4 rounded-2xl text-center text-sm font-black uppercase tracking-wider animate-bounce">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-green-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="successMessage"></span>
                        </div>
                    </div>
                </template>

                <!-- Input Quantity -->
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-widest mb-2">Jumlah Permintaan (Pack)</label>
                    <input type="number" 
                           x-model="qty"
                           :disabled="readOnly"
                           required 
                           min="1" 
                           class="w-full rounded-xl border-[#d4af37]/40 shadow-sm focus:border-[#7a0e14] focus:ring-[#7a0e14] font-bold text-center text-lg bg-white p-3 disabled:bg-gray-100 disabled:text-gray-500">
                </div>




                <!-- Actions -->
                <div class="flex gap-3 pt-3">
                    <button type="button" @click="isOpen = false" 
                            class="flex-1 py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-colors uppercase tracking-widest text-center">
                        Tutup
                    </button>
                    <template x-if="!readOnly">
                        <button type="submit" :disabled="loading"
                                class="flex-1 py-3 px-4 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] hover:from-[#8b1117] hover:to-[#5c0a0f] border border-[#d4af37] text-[#d4af37] font-black rounded-xl text-sm transition-all uppercase tracking-widest text-center shadow-lg">
                            <span x-show="!loading">Kirim Formulir</span>
                            <span x-show="loading">Mengirim...</span>
                        </button>
                    </template>
                    <template x-if="readOnly">
                        <button type="button" @click="cancelRequest()"
                                class="flex-1 py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition-colors uppercase tracking-widest text-center shadow-lg">
                            Batalkan
                        </button>
                    </template>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
