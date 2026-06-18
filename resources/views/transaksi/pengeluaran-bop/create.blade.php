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
                    
                    <div class="mb-3">
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
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Rincian Pengeluaran <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  name="keterangan" 
                                  rows="2"
                                  placeholder="Contoh: Tagihan Listrik Pabrik Bulan Mei 2026 atau Insentif Hadir Mingguan"
                                  required>{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Nominal (Rp) <span class="text-danger">*</span></label>
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
                        <small class="text-muted">Total yang harus dibayarkan</small>
                        @error('nominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Jenis Pengeluaran Aktual (Akun Debit) <span class="text-danger">*</span></label>
                        <select class="form-select @error('akun_debit') is-invalid @enderror" 
                                name="akun_debit" 
                                required>
                            <option value="">-- Pilih Jenis Pengeluaran --</option>
                            @foreach($akunDebit as $akun)
                                <option value="{{ $akun->id_akun }}" {{ old('akun_debit') == $akun->id_akun ? 'selected' : '' }}>
                                    [{{ $akun->kode_akun }}] {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                        @error('akun_debit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                            Item yang dipilih akan ditandai sebagai "Sudah Diaktualkan".
                        </small>
                        
                        <div class="border rounded p-3" style="max-height: 280px; overflow-y: auto; background: #fafbfc;">
                            @foreach($bopBelumAktual as $bop)
                            <div class="form-check mb-2 p-2 rounded {{ in_array($bop->id_overhead, old('bop_terkait', [])) ? 'bg-primary bg-opacity-10 border border-primary' : 'bg-white border' }}">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="bop_terkait[]" 
                                       value="{{ $bop->id_overhead }}" 
                                       id="bop_{{ $bop->id_overhead }}"
                                       {{ in_array($bop->id_overhead, old('bop_terkait', [])) ? 'checked' : '' }}>
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
