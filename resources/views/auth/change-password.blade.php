@extends('layouts.app')

@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-key text-primary me-2"></i> Ganti Password
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Harap ganti password Anda secara berkala demi keamanan akun.</p>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0" role="alert" style="border-radius: 12px; background: rgba(25, 135, 84, 0.1); color: #198754;">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0" role="alert" style="border-radius: 12px; background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Gagal Mengubah Password:</strong>
                            <ul class="mb-0 mt-2 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('change-password') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-lock text-muted me-2"></i>Password Lama
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   name="current_password" 
                                   placeholder="Masukkan password lama Anda"
                                   required 
                                   style="border-radius: 10px; padding: 12px 15px; border: 1px solid #dee2e6;">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-key text-muted me-2"></i>Password Baru
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   name="new_password" 
                                   placeholder="Masukkan password baru (min 6 karakter)"
                                   required 
                                   style="border-radius: 10px; padding: 12px 15px; border: 1px solid #dee2e6;">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-check-double text-muted me-2"></i>Konfirmasi Password Baru
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   name="new_password_confirmation" 
                                   placeholder="Ulangi password baru Anda"
                                   required 
                                   style="border-radius: 10px; padding: 12px 15px; border: 1px solid #dee2e6;">
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary fw-semibold px-4" style="border-radius: 10px; padding: 10px 20px;">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Profil
                            </a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 10px; padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                <i class="fas fa-save me-2"></i> Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
