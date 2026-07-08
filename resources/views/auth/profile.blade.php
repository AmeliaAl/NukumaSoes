@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Profile Info Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm text-center" style="border-radius: 16px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-body py-5">
                    <div class="mx-auto mb-3" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 42px; box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);">
                        <i class="fas fa-user"></i>
                    </div>
                    
                    <h4 class="fw-bold mb-1" style="color: #333;">{{ $admin->nama_lengkap }}</h4>
                    <p class="text-muted mb-3">{{ '@' . $admin->username }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge px-3 py-2 bg-primary bg-opacity-10 text-primary fw-semibold" style="border-radius: 20px;">
                            <i class="fas fa-shield-alt me-1"></i> {{ ucfirst($admin->role) }}
                        </span>
                        <span class="badge px-3 py-2 bg-success bg-opacity-10 text-success fw-semibold" style="border-radius: 20px;">
                            <i class="fas fa-check-circle me-1"></i> {{ ucfirst($admin->status) }}
                        </span>
                    </div>
                    
                    <hr class="my-4 text-muted opacity-25">
                    
                    <div class="text-start px-3">
                        <div class="mb-3 d-flex align-items-center">
                            <div class="icon-box bg-light text-primary me-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 10px;">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">ID Administrator</small>
                                <strong class="text-dark">ADM-{{ str_pad($admin->id_admin, 3, '0', STR_PAD_LEFT) }}</strong>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-light text-primary me-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 10px;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Terdaftar Sejak</small>
                                <strong class="text-dark">{{ $admin->created_at->format('d F Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Edit Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-user-edit text-primary me-2"></i> Perbarui Profil
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Ubah nama lengkap dan username Anda yang terdaftar pada sistem.</p>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0" role="alert" style="border-radius: 12px; background: rgba(25, 135, 84, 0.1); color: #198754;">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-signature text-muted me-2"></i>Nama Lengkap
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                   name="nama_lengkap" 
                                   value="{{ old('nama_lengkap', $admin->nama_lengkap) }}" 
                                   required 
                                   style="border-radius: 10px; padding: 12px 15px; border: 1px solid #dee2e6;">
                            @error('nama_lengkap')
                                <div class="invalid-feedback mt-1">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-user-tag text-muted me-2"></i>Username
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border: 1px solid #dee2e6;">@</span>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror border-start-0" 
                                       name="username" 
                                       value="{{ old('username', $admin->username) }}" 
                                       required 
                                       style="border-radius: 0 10px 10px 0; padding: 12px 15px; border: 1px solid #dee2e6;">
                            </div>
                            <small class="text-muted mt-1 d-block">Hanya boleh menggunakan huruf dan angka, tanpa spasi atau simbol.</small>
                            @error('username')
                                <div class="text-danger small mt-1">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('change-password') }}" class="btn btn-outline-primary fw-semibold px-4" style="border-radius: 10px; padding: 10px 20px;">
                                <i class="fas fa-key me-2"></i> Ubah Password
                            </a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 10px; padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
