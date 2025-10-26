<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Presensi RFID')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; margin: 0; }
        .sidebar { 
            width: 250px; 
            background: #2c3e50; 
            color: white; 
            position: fixed; 
            height: 100vh; 
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar .nav-link { 
            color: #ecf0f1; 
            padding: 12px 20px; 
            border-radius: 5px; 
            margin: 5px 10px;
            display: block;
            text-decoration: none;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            background: #34495e; 
            color: white; 
        }
        .sidebar .submenu { 
            padding-left: 15px; 
        }
        .sidebar .submenu .nav-link {
            padding: 8px 20px;
            font-size: 0.9rem;
        }
        .main-content { 
            margin-left: 250px; 
            flex: 1; 
            padding: 20px; 
            background: #ecf0f1;
            width: calc(100% - 250px);
        }
        .card { 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            border: none; 
            margin-bottom: 20px; 
        }
        .stat-card { 
            padding: 20px; 
            border-radius: 10px; 
            color: white; 
        }
        .stat-card h3 { 
            font-size: 2rem; 
            margin: 0; 
        }
        .bg-primary-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-success-custom { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-warning-custom { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-info-custom { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .sidebar-header { 
            padding: 20px; 
            text-align: center; 
            border-bottom: 1px solid #34495e; 
        }
        .sidebar-header h4 { 
            margin: 0; 
            font-weight: bold; 
        }
        .badge-terlambat { background: #e74c3c; }
        .badge-ontime { background: #27ae60; }
        .menu-toggle-icon {
            float: right;
            transition: transform 0.3s;
        }
        .menu-toggle-icon.rotated {
            transform: rotate(180deg);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-id-card fa-3x mb-2"></i>
            <h4>Sistem RFID</h4>
            <small>{{ auth()->user()->name }}</small>
        </div>
        <nav class="nav flex-column mt-3">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            
            <!-- Menu Presensi -->
            <a class="nav-link" href="#" onclick="toggleMenu('presensiMenu'); return false;">
                <i class="fas fa-clipboard-check me-2"></i> Presensi 
                <i class="fas fa-chevron-down menu-toggle-icon" id="presensiIcon"></i>
            </a>
            <div class="submenu" id="presensiMenu" style="display: {{ request()->is('presensi-*') ? 'block' : 'none' }};">
                <a class="nav-link {{ request()->routeIs('presensi.sekolah.*') ? 'active' : '' }}" 
                   href="{{ route('presensi.sekolah.index') }}">
                    <i class="fas fa-school me-2"></i> Sekolah
                </a>
                <a class="nav-link {{ request()->routeIs('presensi.sholat.*') ? 'active' : '' }}" 
                   href="{{ route('presensi.sholat.index') }}">
                    <i class="fas fa-mosque me-2"></i> Sholat
                </a>
                <a class="nav-link {{ request()->routeIs('presensi.kustom.*') ? 'active' : '' }}" 
                   href="{{ route('presensi.kustom.index') }}">
                    <i class="fas fa-clock me-2"></i> Kustom
                </a>
            </div>

            <!-- Menu E-Kantin -->
            <a class="nav-link" href="#" onclick="toggleMenu('kantinMenu'); return false;">
                <i class="fas fa-utensils me-2"></i> E-Kantin 
                <i class="fas fa-chevron-down menu-toggle-icon" id="kantinIcon"></i>
            </a>
            <div class="submenu" id="kantinMenu" style="display: {{ request()->is('kantin/*') ? 'block' : 'none' }};">
                <a class="nav-link {{ request()->routeIs('kantin.cek-saldo') ? 'active' : '' }}" 
                   href="{{ route('kantin.cek-saldo') }}">
                    <i class="fas fa-wallet me-2"></i> Cek Saldo
                </a>
                <a class="nav-link {{ request()->routeIs('kantin.topup') ? 'active' : '' }}" 
                   href="{{ route('kantin.topup') }}">
                    <i class="fas fa-money-bill-wave me-2"></i> Top Up
                </a>
                <a class="nav-link {{ request()->routeIs('kantin.bayar') ? 'active' : '' }}" 
                   href="{{ route('kantin.bayar') }}">
                    <i class="fas fa-cash-register me-2"></i> Bayar
                </a>
                <a class="nav-link {{ request()->routeIs('kantin.riwayat') ? 'active' : '' }}" 
                   href="{{ route('kantin.riwayat') }}">
                    <i class="fas fa-history me-2"></i> Riwayat
                </a>
            </div>

            @if(auth()->user()->role === 'admin')
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
               href="{{ route('users.index') }}">
                <i class="fas fa-users me-2"></i> Kelola User
            </a>
            <a class="nav-link {{ request()->routeIs('jadwal-sholat.*') ? 'active' : '' }}" 
               href="{{ route('jadwal-sholat.index') }}">
                <i class="fas fa-calendar-alt me-2"></i> Jadwal Sholat
            </a>
            <a class="nav-link {{ request()->routeIs('pengaturan.waktu.*') ? 'active' : '' }}" 
               href="{{ route('pengaturan.waktu.index') }}">
                <i class="fas fa-cog me-2"></i> Pengaturan Waktu
            </a>
            @endif

            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const icon = document.getElementById(menuId.replace('Menu', 'Icon'));
            
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                if (icon) icon.classList.add('rotated');
            } else {
                menu.style.display = 'none';
                if (icon) icon.classList.remove('rotated');
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
