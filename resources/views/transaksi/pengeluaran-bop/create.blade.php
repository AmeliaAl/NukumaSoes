@extends('layouts.app')

@section('title', 'Input BOP Aktual')
@section('page-title', 'Input Pengeluaran BOP Aktual')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Input Pengeluaran BOP Aktual</h4>
            <p class="text-muted mb-0">Input pembayaran tagihan pabrik (Listrik, Air, Insentif Mingguan, dll)</p>
        </div>
        <a href="{{ route('pengeluaran-bop.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('pengeluaran-bop.store') }}" method="POST">
                    @csrf
                    
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i class="fas fa-info-circle mt-1 me-2 fs-5"></i>
                        <div>
                            <strong>Informasi Jurnal:</strong><br>
                            Sesuai batasan lingkup HPP, input di bawah ini akan membebankan pengeluaran aktual (Debit) sesuai akun yang dipilih, dan secara otomatis mengkreditkan <b>Hutang Lainnya (Kode: 212)</b> tanpa melibatkan kas/bank Anda langsung.
                        </div>
                    </div>
                    
                    <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Data Pembayaran</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Pembayaran <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nomor_pembayaran') is-invalid @enderror"
                                   name="nomor_pembayaran"
                                   value="{{ old('nomor_pembayaran') }}"
                                   placeholder="Masukkan nomor pembayaran dari aplikasi teman"
                                   pattern="[A-Za-z0-9._/-]+"
                                   title="Gunakan huruf, angka, titik, garis miring, tanda minus, atau underscore"
                                   required>
                            <small class="text-muted">Gunakan nomor pembayaran dari aplikasi teman. Nomor yang sama tidak dapat dicatat dua kali.</small>
                            @error('nomor_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   name="tanggal"
                                   value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                                   required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-warning mt-2">
                        <strong>Periode Produksi - diisi oleh tim produksi</strong><br>
                        Sistem akan membagi tagihan ke job order yang periode produksinya bertumpang tindih dengan rentang ini, proporsional berdasarkan jumlah batch.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode Mulai <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('periode_mulai') is-invalid @enderror"
                                   name="periode_mulai"
                                   value="{{ old('periode_mulai', now()->startOfMonth()->format('Y-m-d')) }}"
                                   required>
                            @error('periode_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode Selesai <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('periode_selesai') is-invalid @enderror"
                                   name="periode_selesai"
                                   value="{{ old('periode_selesai', now()->endOfMonth()->format('Y-m-d')) }}"
                                   required>
                            @error('periode_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Rincian Pengeluaran <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  name="keterangan" 
                                  rows="2"
                                  placeholder="Contoh: Tagihan Gas LPG untuk oven produksi soes kering bulan Mei 2026"
                                  required>{{ old('keterangan') }}</textarea>
                        <small class="text-muted">Mesin/oven tidak dicatat sebagai BOP. Catat biaya operasionalnya, misalnya gas untuk oven tertentu.</small>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga / Total Tagihan Aktual (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('nominal') is-invalid @enderror" 
                                   name="nominal" 
                                   value="{{ old('nominal') }}"
                                   min="1"
                                   step="1000"
                                   placeholder="Contoh: 1500000"
                                   required>
                        </div>
                        <small class="text-muted">Isi nilai tagihan yang benar-benar terjadi pada rentang periode di atas.</small>
                        @error('nominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Kategori BOP <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_kategori_bop') is-invalid @enderror"
                                name="id_kategori_bop" required>
                            <option value="">-- Pilih Kategori BOP --</option>
                            @foreach($kategoriBop as $kategori)
                                <option value="{{ $kategori->id_kategori_bop }}"
                                        data-account="{{ $kategori->akun ? '[' . $kategori->akun->kode_akun . '] ' . $kategori->akun->nama_akun : 'Belum dipetakan' }}"
                                        {{ old('id_kategori_bop', $selectedCategoryId) == $kategori->id_kategori_bop ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kategori terkait mesin/aset tidak ditampilkan karena dikelola pada modul aset tim lain.</small>
                        @error('id_kategori_bop')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Akun debit mengikuti pemetaan pada master kategori BOP.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Akun COA Debit</label>
                        <input type="text" class="form-control bg-light" id="mappedAccount" readonly value="Pilih kategori BOP">
                    </div>

                    <div class="card border-primary mb-4" id="allocationPreviewCard">
                        <div class="card-header bg-primary text-white">
                            <strong><i class="fas fa-sitemap me-2"></i>Pratinjau Alokasi Otomatis</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Job Order</th>
                                            <th>Tanggal Mulai</th>
                                            <th class="text-end">Batch</th>
                                            <th class="text-end">Alokasi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allocationPreviewBody">
                                        <tr><td colspan="4" class="text-center text-muted py-3">Isi periode dan nominal untuk melihat pembagian.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Opsi: Link ke BOP yang belum diaktualkan --}}
                    @if($bopBelumAktual->count() > 0)
                    <hr>
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-link me-1 text-primary"></i>
                            Kaitkan dengan BOP Dianggarkan 
                            <span class="badge bg-secondary">Opsional</span>
                        </label>
                        <small class="d-block text-muted mb-3">
                            Pilih item BOP yang dianggarkan yang terkait dengan pembayaran ini. 
                            Item yang dipilih akan ditandai sebagai "Sudah Diaktualkan" dan menggunakan nomor pembayaran yang diisi di atas. Biarkan semuanya kosong untuk memakai alokasi otomatis berdasarkan periode.
                        </small>
                        
                        <div class="border rounded p-3" style="max-height: 280px; overflow-y: auto; background: #fafbfc;">
                            @foreach($bopBelumAktual as $bop)
                            @php
                                $bopTerpilih = in_array($bop->id_overhead, old('bop_terkait', request('bop_id') ? [(int) request('bop_id')] : []));
                            @endphp
                            <div class="form-check mb-2 p-2 rounded {{ $bopTerpilih ? 'bg-primary bg-opacity-10 border border-primary' : 'bg-white border' }}">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="bop_terkait[]" 
                                       value="{{ $bop->id_overhead }}" 
                                       data-category="{{ $bop->id_kategori_bop }}"
                                       id="bop_{{ $bop->id_overhead }}"
                                       {{ $bopTerpilih ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="bop_{{ $bop->id_overhead }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-info text-dark me-1">{{ $bop->jenis_overhead }}</span>
                                            <span class="text-muted small">
                                                {{ $bop->permintaanProduksi->nomor_job ?? '-' }} 
                                                — {{ $bop->permintaanProduksi->produk->nama_produk ?? '-' }}
                                            </span>
                                        </div>
                                            <strong class="text-primary">Rp {{ number_format($bop->nominal, 0, ',', '.') }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($bop->tanggal_overhead)->format('d/m/Y') }}
                                        &bull; {{ $bop->jumlah_batch }} {{ $bop->satuan_periode_label }}
                                        @if($bop->keterangan)
                                            — {{ Str::limit($bop->keterangan, 50) }}
                                        @endif
                                    </small>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('pengeluaran-bop.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan dan Jurnal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const jobOrders = {{ Illuminate\Support\Js::from($jobOrderPreview) }};

    const periodeMulai = document.querySelector('[name="periode_mulai"]');
    const periodeSelesai = document.querySelector('[name="periode_selesai"]');
    const nominal = document.querySelector('[name="nominal"]');
    const previewBody = document.getElementById('allocationPreviewBody');
    const categorySelect = document.querySelector('[name="id_kategori_bop"]');
    const mappedAccount = document.getElementById('mappedAccount');

    function updateMappedAccount() {
        const option = categorySelect.options[categorySelect.selectedIndex];
        mappedAccount.value = option?.dataset.account || 'Pilih kategori BOP';
    }

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
    }

    function updateAllocationPreview() {
        const linkedBopSelected = document.querySelector('input[name="bop_terkait[]"]:checked');
        if (linkedBopSelected) {
            previewBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Alokasi otomatis tidak digunakan karena pembayaran dikaitkan ke BOP yang sudah dianggarkan.</td></tr>';
            return;
        }

        const start = periodeMulai.value;
        const end = periodeSelesai.value;
        const total = Number(nominal.value) || 0;
        const matches = jobOrders.filter(job =>
            job.tanggal_mulai <= end && (!job.tanggal_selesai || job.tanggal_selesai >= start)
        );
        const totalBatch = matches.reduce((sum, job) => sum + job.jumlah_batch, 0);

        if (!start || !end || total <= 0 || matches.length === 0) {
            previewBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada job order pada periode ini atau nominal belum diisi.</td></tr>';
            return;
        }

        previewBody.innerHTML = matches.map(job => {
            const allocation = total * job.jumlah_batch / totalBatch;
            return `<tr>
                <td><strong>${job.nomor_job}</strong><br><small class="text-muted">${job.produk}</small></td>
                <td>${job.tanggal_mulai.split('-').reverse().join('/')}</td>
                <td class="text-end">${job.jumlah_batch}</td>
                <td class="text-end fw-bold">${formatRupiah(allocation)}</td>
            </tr>`;
        }).join('');
    }

    [periodeMulai, periodeSelesai, nominal].forEach(input => input.addEventListener('input', updateAllocationPreview));
    categorySelect.addEventListener('change', updateMappedAccount);
    updateAllocationPreview();
    updateMappedAccount();

    document.querySelectorAll('input[name="bop_terkait[]"]').forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                const checkedCategories = new Set(
                    [...document.querySelectorAll('input[name="bop_terkait[]"]:checked')]
                        .map(input => input.dataset.category)
                );
                if (checkedCategories.size > 1) {
                    this.checked = false;
                    alert('Satu pembayaran hanya dapat dikaitkan dengan BOP dalam kategori yang sama.');
                    return;
                }
                categorySelect.value = this.dataset.category;
                categorySelect.dispatchEvent(new Event('change'));
            }
            this.closest('.form-check').classList.toggle('bg-primary', this.checked);
            this.closest('.form-check').classList.toggle('bg-opacity-10', this.checked);
            this.closest('.form-check').classList.toggle('border-primary', this.checked);
            updateAllocationPreview();
        });
    });
</script>
@endsection
