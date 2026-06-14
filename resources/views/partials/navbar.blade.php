<nav class="main-navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between h-100">
            <!-- Mobile Menu Toggle -->
            <button class="btn btn-link d-md-none text-dark p-0" onclick="toggleSidebar()">
                <i class="fas fa-bars fs-5"></i>
            </button>
            
            <!-- Page Title -->
            <div class="d-none d-md-block">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-home me-2 text-primary"></i>
                    @yield('page-title', 'Dashboard')
                </h5>
            </div>
            
            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
    @php
        $stokMenipisNav = \App\Models\BahanBaku::where('status', 'aktif')
            ->whereColumn('stok_saat_ini', '<', 'stok_minimum')
            ->get();
        $stokMenipisCount = $stokMenipisNav->count();
    @endphp
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative p-2" 
                            type="button" 
                            data-bs-toggle="dropdown"
                            style="text-decoration: none;">
                        <i class="fas fa-bell fs-5"></i>
                        @if($stokMenipisCount > 0)
                        <span class="notification-badge" id="notifBadge">{{ $stokMenipisCount }}</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg notification-dropdown">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Stok Menipis
                            </span>
                            <span class="badge bg-danger rounded-pill">{{ $stokMenipisCount }}</span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        @if($stokMenipisCount > 0)
                            @foreach($stokMenipisNav as $bahanNotif)
                            <li>
                                <a class="dropdown-item notification-item" href="{{ route('bahan-baku.index') }}">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon bg-danger">
                                            <i class="fas fa-exclamation"></i>
                                        </div>
                                        <div class="notification-content">
                                            <p class="notification-title">{{ $bahanNotif->nama_bahan }}</p>
                                            <p class="notification-text">
                                                Stok: <strong class="text-danger">{{ number_format($bahanNotif->stok_saat_ini, 0) }}</strong>
                                                / Min: {{ number_format($bahanNotif->stok_minimum, 0) }} {{ $bahanNotif->satuan }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        @else
                            <li>
                                <div class="dropdown-item text-center py-3 text-muted">
                                    <i class="fas fa-check-circle text-success fa-lg mb-1 d-block"></i>
                                    Semua stok aman!
                                </div>
                            </li>
                        @endif
                        
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary fw-semibold" href="{{ route('bahan-baku.index') }}">
                                <i class="fas fa-boxes me-2"></i>Lihat Semua Bahan Baku
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark text-decoration-none d-flex align-items-center gap-2 p-0" 
                            type="button" 
                            data-bs-toggle="dropdown">
                        <!-- Avatar Bulat -->
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        
                        <!-- User Info -->
                        <div class="d-none d-md-block text-start">
                            <div class="user-name">{{ Auth::guard('admin')->user()->nama_lengkap }}</div>
                            <div class="user-role">
                                <i class="fas fa-shield-alt me-1"></i>
                                {{ ucfirst(Auth::guard('admin')->user()->role) }}
                            </div>
                        </div>
                        
                        <i class="fas fa-chevron-down small text-muted"></i>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg user-dropdown">
                        <li class="dropdown-header">
                            <div class="text-center mb-2">
                                <div class="user-avatar-large mx-auto mb-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="fw-bold">{{ Auth::guard('admin')->user()->nama_lengkap }}</div>
                                <small class="text-muted">{{ Auth::guard('admin')->user()->username }}</small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-circle me-2"></i> Profile Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('change-password') }}">
                                <i class="fas fa-key me-2"></i> Ubah Password
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('settings') }}">
                                <i class="fas fa-cog me-2"></i> Pengaturan
                            </a>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Notification Badge */
    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
    }
    
    /* Notification Dropdown */
    .notification-dropdown {
        width: 350px !important;
        max-height: 500px;
        overflow-y: auto;
        border: none;
        border-radius: 12px;
        padding: 0;
    }
    
    .notification-item {
        padding: 12px 15px;
        transition: all 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .notification-item:hover {
        background: #f8f9fa;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .notification-icon i {
        color: white;
        font-size: 18px;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-title {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 14px;
        color: #333;
    }
    
    .notification-text {
        font-size: 13px;
        color: #666;
        margin-bottom: 4px;
    }
    
    .notification-time {
        font-size: 11px;
        color: #999;
        margin: 0;
    }
    
    /* User Avatar */
    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        transition: all 0.3s;
    }
    
    .user-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.5);
    }
    
    .user-avatar-large {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        line-height: 1.2;
    }
    
    .user-role {
        font-size: 11px;
        color: #667eea;
        font-weight: 600;
        line-height: 1.2;
    }
    
    /* User Dropdown */
    .user-dropdown {
        width: 280px !important;
        border: none;
        border-radius: 12px;
        padding: 10px 0;
    }
    
    .dropdown-item {
        padding: 10px 20px;
        transition: all 0.2s;
        font-size: 14px;
    }
    
    .dropdown-item:hover {
        background: #f8f9fa;
        padding-left: 25px;
    }
    
    .dropdown-item i {
        width: 20px;
        text-align: center;
    }
    
    .dropdown-header {
        padding: 15px 20px;
    }
    
    /* Dropdown Animations */
    .dropdown-menu {
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>