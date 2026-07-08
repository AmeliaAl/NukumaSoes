<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Dashboard') - Sistem Biaya Produksi</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --navbar-height: 60px;
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }
        
        /* Navbar */
        .main-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--navbar-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 999;
            transition: all 0.3s;
        }
        
        /* Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 25px;
            min-height: calc(100vh - var(--navbar-height));
            transition: all 0.3s;
        }
        
        /* Cards */
        .stat-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        /* Page Header */
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: -var(--sidebar-width);
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-navbar,
            .main-content {
                margin-left: 0;
            }
        }
        
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('partials.sidebar')
    
    <!-- Navbar -->
    @include('partials.navbar')
    
    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // ========================================
        // PREVENT BACK BUTTON AFTER LOGOUT
        // ========================================
        
        // Deteksi jika halaman dimuat dari cache browser (bfcache)
        window.addEventListener('pageshow', function(event) {
            // event.persisted = true berarti halaman dimuat dari bfcache
            // performance.navigation.type === 2 berarti user menekan tombol back
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                // Cek apakah user masih terautentikasi dengan melakukan AJAX request
                fetch('/dashboard', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (response.status === 401 || response.redirected) {
                        // Jika tidak terautentikasi, reload halaman
                        window.location.reload();
                    }
                })
                .catch(() => {
                    // Jika terjadi error, reload halaman untuk keamanan
                    window.location.reload();
                });
            }
        });

        // Deteksi browser back/forward button
        window.addEventListener('popstate', function(event) {
            // Cek autentikasi saat navigasi back/forward
            fetch('/dashboard', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.status === 401 || response.redirected) {
                    window.location.href = '/login';
                }
            })
            .catch(() => {
                window.location.href = '/login';
            });
        });

        // ========================================
        // EXISTING SCRIPTS
        // ========================================
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
        
        // Auto hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // DataTable default config
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            responsive: true
        });

        // Disable browser cache untuk form
        if (window.performance && window.performance.navigation.type === 1) {
            // Type 1 = reload
            console.log('Page was reloaded');
        }
    </script>
    
    @yield('scripts')
</body>
</html>
