<div class="sidebar">
    <!-- Brand Header dengan Logo Nukuma Cantique Bulat -->
    <div class="p-4 border-bottom border-white border-opacity-10">
        <div class="text-center">
            <!-- Logo Bulat Nukuma Cantique -->
            <div class="logo-container mb-3">
                <img src="{{ asset('images/logo-nukuma.png') }}" 
                     alt="Nukuma Cantique" 
                     class="logo-nukuma">
            </div>
            <h6 class="text-white fw-bold mb-1">Nukuma Cantique</h6>
            <small class="text-white-50" style="font-size: 11px;">Sistem Biaya Produksi</small>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-3">
        <ul class="list-unstyled">
            <!-- Dashboard -->
            <li class="mb-2">
                <a href="{{ route('dashboard') }}" 
                   class="menu-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Master Data -->
            <li class="mb-2">
                <div class="menu-header">
                    <i class="fas fa-database me-2"></i>
                    Master Data
                </div>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('bahan-baku.index') }}" 
                   class="menu-item {{ request()->routeIs('bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    <span>Bahan Baku</span>
                </a>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('tenaga-kerja.index') }}" 
                   class="menu-item {{ request()->routeIs('tenaga-kerja*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Tenaga Kerja</span>
                </a>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('produk.index') }}" 
                   class="menu-item {{ request()->routeIs('produk*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produk</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('stok-produk.index') }}" 
                   class="menu-item {{ request()->routeIs('stok-produk*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Stok Produk</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('akun.index') }}" 
                   class="menu-item {{ request()->routeIs('akun*') ? 'active' : '' }}">
                    <i class="fas fa-list-ul"></i>
                    <span>Master Akun</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('kategori-bop.index') }}" 
                   class="menu-item {{ request()->routeIs('kategori-bop*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Kategori BOP</span>
                </a>
            </li>

            <!-- Transaksi -->
            <li class="mb-2">
                <div class="menu-header">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Transaksi
                </div>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('permintaan-bahan-baku.index') }}" 
                   class="menu-item {{ request()->routeIs('permintaan-bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Permintaan Bahan</span>
                </a>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('penerimaan-bahan-baku.index') }}" 
                   class="menu-item {{ request()->routeIs('penerimaan-bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i>
                    <span>Penerimaan Bahan</span>
                </a>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('penerimaan-order-produksi.index') }}" 
                   class="menu-item {{ request()->routeIs('penerimaan-order-produksi*') ? 'active' : '' }}">
                    <i class="fas fa-inbox"></i>
                    <span>Terima Order</span>
                </a>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('permintaan-produksi.index') }}" 
                   class="menu-item {{ request()->routeIs('permintaan-produksi*') ? 'active' : '' }}">
                    <i class="fas fa-cogs"></i>
                    <span>Job Order</span>
                </a>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('pemakaian-bahan-baku.index') }}" 
                   class="menu-item {{ request()->routeIs('pemakaian-bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-arrow-circle-down"></i>
                    <span>Pemakaian Bahan</span>
                </a>
            </li>

            <!-- Biaya -->
            <li class="mb-2">
                <div class="menu-header">
                    <i class="fas fa-calculator me-2"></i>
                    Biaya
                </div>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('biaya-tenaga-kerja.index') }}" 
                   class="menu-item {{ request()->routeIs('biaya-tenaga-kerja*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Biaya Tenaga Kerja</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('kehadiran.index') }}" 
                   class="menu-item {{ request()->routeIs('kehadiran.index') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Absensi Harian</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('kehadiran.rekap-mingguan') }}" 
                   class="menu-item {{ request()->routeIs('kehadiran.rekap-mingguan') ? 'active' : '' }}">
                    <i class="fas fa-award"></i>
                    <span>Rekap & Insentif</span>
                </a>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('biaya-overhead-pabrik.index') }}" 
                   class="menu-item {{ request()->routeIs('biaya-overhead-pabrik*') ? 'active' : '' }}">
                    <i class="fas fa-industry"></i>
                    <span>Biaya Overhead</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('pengeluaran-bop.index') }}" 
                   class="menu-item {{ request()->routeIs('pengeluaran-bop*') ? 'active' : '' }}">
                    <i class="fas fa-money-check-alt"></i>
                    <span>BOP Aktual</span>
                </a>
            </li>

            <!-- Laporan -->
            <li class="mb-2">
                <div class="menu-header">
                    <i class="fas fa-chart-line me-2"></i>
                    Laporan
                </div>
            </li>
            
            <li class="mb-1">
                <a href="{{ route('jurnal-umum.index') }}" 
                   class="menu-item {{ request()->routeIs('jurnal-umum*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i>
                    <span>Jurnal Umum</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('buku-besar.index') }}" 
                   class="menu-item {{ request()->routeIs('buku-besar*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Buku Besar</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('laporan.neraca-saldo') }}" 
                   class="menu-item {{ request()->routeIs('laporan.neraca-saldo*') ? 'active' : '' }}">
                    <i class="fas fa-balance-scale"></i>
                    <span>Neraca Lajur</span>
                </a>
            </li>

            <li class="mb-1">
                <a href="{{ route('laporan.biaya-produksi.index') }}" 
                   class="menu-item {{ request()->routeIs('laporan.biaya-produksi*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Laporan Biaya</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
    /* Logo Bulat Styling */
    .logo-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .logo-nukuma {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .logo-nukuma:hover {
        transform: scale(1.05);
        border-color: rgba(255, 255, 255, 0.4);
    }
    
    /* Menu Header Styling */
    .menu-header {
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 700;
        padding: 8px 12px;
        margin-top: 15px;
        letter-spacing: 0.5px;
    }
    
    /* Menu Item Styling */
    .menu-item {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s ease;
        font-size: 14px;
    }
    
    .menu-item i {
        width: 20px;
        margin-right: 12px;
        text-align: center;
        font-size: 16px;
    }
    
    .menu-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(5px);
    }
    
    .menu-item.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .menu-item.active i {
        color: #F8B803;
    }
</style>