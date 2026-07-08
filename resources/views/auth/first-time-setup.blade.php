<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Awal - Sistem Biaya Produksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .setup-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .setup-header i {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .setup-body {
            padding: 40px 30px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-setup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-setup:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-card mx-auto">
            <div class="setup-header">
                <i class="fas fa-cog"></i>
                <h2 class="mb-2">Setup Awal Sistem</h2>
                <p class="mb-0">Buat akun administrator pertama</p>
            </div>
            
            <div class="setup-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Selamat datang!</strong> Sistem mendeteksi ini adalah pertama kalinya aplikasi dijalankan. 
                    Silakan buat akun administrator pertama untuk melanjutkan.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('setup.store-first-admin') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user me-2"></i>Nama Lengkap
                        </label>
                        <input type="text" 
                               class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               name="nama_lengkap" 
                               value="{{ old('nama_lengkap') }}"
                               placeholder="Contoh: Admin Produksi"
                               required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-id-badge me-2"></i>Username
                        </label>
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               name="username" 
                               value="{{ old('username') }}"
                               placeholder="admin"
                               required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password"
                               placeholder="Minimal 6 karakter"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock me-2"></i>Konfirmasi Password
                        </label>
                        <input type="password" 
                               class="form-control" 
                               name="password_confirmation"
                               placeholder="Ketik ulang password"
                               required>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Penting!</strong> Setelah akun ini dibuat, halaman setup akan otomatis disabled. 
                        Admin baru harus dibuat dari dashboard.
                    </div>

                    <button type="submit" class="btn btn-primary btn-setup w-100">
                        <i class="fas fa-check-circle me-2"></i>Buat Admin & Mulai Aplikasi
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-4 text-white">
            <p class="mb-0">
                <i class="fas fa-shield-alt me-2"></i>
                Sistem Biaya Produksi - FIFO & Job Order Costing
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>