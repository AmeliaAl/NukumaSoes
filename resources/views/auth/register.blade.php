<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Nukuma Cantique</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-header {
            background: white;
            padding: 40px 30px 30px;
            text-align: center;
        }
        
        /* Logo Bulat Styling */
        .logo-container {
            margin-bottom: 20px;
            display: inline-block;
            position: relative;
        }
        
        .logo-nukuma {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #667eea;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        
        .logo-nukuma:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
        }
        
        .register-header h3 {
            color: #333;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .register-header p {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }
        
        .register-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 15px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .register-body {
            padding: 40px;
            background: white;
        }
        
        .register-body h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
        }
        
        .form-label {
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .input-group {
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .input-group-text {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-right: none;
            color: #667eea;
        }
        
        .form-control {
            border-left: none;
            border: 1px solid #dee2e6;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
        }
        
        .btn-register:active {
            transform: translateY(-1px);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card register-card">
                    <div class="register-header">
                        <!-- Logo Nukuma Cantique Bulat -->
                        <div class="logo-container">
                            <img src="{{ asset('images/logo-nukuma.png') }}" 
                                 alt="Nukuma Cantique" 
                                 class="logo-nukuma">
                        </div>
                        
                        <h3>Nukuma Cantique</h3>
                        <p>Sistem Biaya Produksi</p>
                        <p class="small text-muted">FIFO & Job Order Costing</p>
                        
                        <div class="register-badge">
                            <i class="fas fa-user-plus me-2"></i>
                            DAFTAR AKUN BARU
                        </div>
                    </div>
                    
                    <div class="register-body">
                        <h4 class="text-center">
                            <i class="fas fa-user-plus me-2 text-success"></i>
                            Buat Akun Baru
                        </h4>
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Pendaftaran Gagal!</strong>
                                <ul class="mb-0 mt-2 small">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-id-card me-2"></i>Nama Lengkap
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                           name="nama_lengkap" 
                                           value="{{ old('nama_lengkap') }}"
                                           placeholder="Masukkan nama lengkap"
                                           required 
                                           autofocus>
                                </div>
                                @error('nama_lengkap')
                                    <small class="text-danger mt-1 d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           name="username" 
                                           value="{{ old('username') }}"
                                           placeholder="Masukkan username (huruf & angka)"
                                           required>
                                </div>
                                @error('username')
                                    <small class="text-danger mt-1 d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           placeholder="Masukkan password (min. 6 karakter)"
                                           required>
                                </div>
                                @error('password')
                                    <small class="text-danger mt-1 d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-lock me-2"></i>Konfirmasi Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-check-double"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           placeholder="Ulangi password"
                                           required>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-register">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Daftar Akun
                                </button>
                            </div>
                        </form>
                        
                        <div class="login-link">
                            <p class="mb-0">
                                Sudah punya akun? 
                                <a href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Masuk di sini
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="footer-text">
                    <p class="mb-0">
                        <i class="fas fa-copyright me-1"></i>
                        2026 <strong>Nukuma Cantique</strong>
                    </p>
                    <p class="small mt-1 mb-0">All rights reserved</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
