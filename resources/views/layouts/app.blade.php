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
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            display: flex; 
            min-height: 100vh; 
            background: #ecf0f1;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .sidebar { 
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white; 
            position: fixed; 
            top: 0;
            left: 0;
            height: 100vh; 
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            transition: transform var(--transition-speed) ease, width var(--transition-speed) ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--sidebar-bg);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--sidebar-hover);
            border-radius: 3px;
        }

        .sidebar-header { 
            padding: 20px; 
            text-align: center; 
            border-bottom: 1px solid var(--sidebar-hover);
            position: sticky;
            top: 0;
            background: var(--sidebar-bg);
            z-index: 10;
        }

        .sidebar-header i {
            color: #3498db;
        }

        .sidebar-header h4 { 
            margin: 10px 0 5px 0;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .sidebar-header small {
            color: #95a5a6;
            font-size: 0.85rem;
        }

        .sidebar .nav-link { 
            color: #ecf0f1; 
            padding: 12px 20px; 
            border-radius: 5px; 
            margin: 5px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar .nav-link:hover { 
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .sidebar .nav-link i:first-child {
            width: 20px;
            text-align: center;
        }

        .sidebar .submenu { 
            padding-left: 15px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar .submenu .nav-link {
            padding: 8px 20px;
            font-size: 0.9rem;
        }

        .menu-toggle-icon {
            transition: transform var(--transition-speed);
            font-size: 0.8rem;
        }

        .menu-toggle-icon.rotated {
            transform: rotate(180deg);
        }

        /* ========== MAIN CONTENT ========== */
        .main-content { 
            margin-left: var(--sidebar-width);
            flex: 1; 
            padding: 20px; 
            background: #ecf0f1;
            width: calc(100% - var(--sidebar-width));
            transition: margin-left var(--transition-speed) ease, width var(--transition-speed) ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* ========== TOGGLE BUTTON ========== */
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--sidebar-bg);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all var(--transition-speed) ease;
        }

        .sidebar-toggle:hover {
            background: var(--sidebar-hover);
            transform: scale(1.1);
        }

        .sidebar-toggle.sidebar-open {
            left: calc(var(--sidebar-width) + 20px);
        }

        .sidebar-toggle i {
            font-size: 1.2rem;
            transition: transform var(--transition-speed);
        }

        .sidebar-toggle.sidebar-open i {
            transform: rotate(180deg);
        }

        /* ========== CARDS ========== */
        .card { 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
            border: none; 
            margin-bottom: 20px;
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .stat-card { 
            padding: 20px; 
            border-radius: 10px; 
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(45deg);
            transition: all 0.5s ease;
        }

        .stat-card:hover::before {
            top: -60%;
            right: -60%;
        }

        .stat-card h3 { 
            font-size: 2rem; 
            margin: 0;
            font-weight: bold;
        }

        .bg-primary-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-success-custom { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-warning-custom { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-info-custom { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

        /* ========== BADGES ========== */
        .badge-terlambat { background: #e74c3c; }
        .badge-ontime { background: #27ae60; }

        /* ========== ALERTS ========== */
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 200px;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.visible {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }

            .sidebar-toggle {
                left: 15px;
                top: 15px;
            }

            .sidebar-toggle.sidebar-open {
                left: calc(var(--sidebar-width) + 15px);
            }

            .stat-card h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            :root {
                --sidebar-width: 100%;
            }

            .sidebar-toggle.sidebar-open {
                left: 15px;
                background: #e74c3c;
            }

            .main-content {
                padding: 10px;
            }
        }

        /* ========== LOADING SPINNER ========== */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* ========== SMOOTH SCROLL ========== */
        html {
            scroll-behavior: smooth;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-id-card fa-3x mb-2"></i>
            <h4>Sistem RFID</h4>
            <small>{{ auth()->user()->name }}</small>
            <br>
            <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'danger' : 'primary' }} mt-2">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>
<nav class="nav flex-column mt-3">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <span><i class="fas fa-home me-2"></i> Dashboard</span>
    </a>
    
    <!-- Menu Presensi (SEMUA USER BISA AKSES) -->
    <a class="nav-link" href="#" onclick="toggleMenu('presensiMenu'); return false;">
        <span><i class="fas fa-clipboard-check me-2"></i> Presensi</span>
        <i class="fas fa-chevron-down menu-toggle-icon" id="presensiIcon"></i>
    </a>
    <div class="submenu" id="presensiMenu" style="display: {{ request()->is('presensi-*') ? 'block' : 'none' }};">
        <a class="nav-link {{ request()->routeIs('presensi.sekolah.*') ? 'active' : '' }}" 
           href="{{ route('presensi.sekolah.index') }}">
            <span><i class="fas fa-school me-2"></i> Sekolah</span>
        </a>
        <a class="nav-link {{ request()->routeIs('presensi.sholat.*') ? 'active' : '' }}" 
           href="{{ route('presensi.sholat.index') }}">
            <span><i class="fas fa-mosque me-2"></i> Sholat</span>
        </a>
        <a class="nav-link {{ request()->routeIs('presensi.kustom.*') ? 'active' : '' }}" 
           href="{{ route('presensi.kustom.index') }}">
            <span><i class="fas fa-clock me-2"></i> Kustom</span>
        </a>
    </div>

    <!-- Menu E-Kantin (✅ HANYA ADMIN) -->
    @if(auth()->user()->role === 'admin')
    <a class="nav-link" href="#" onclick="toggleMenu('kantinMenu'); return false;">
        <span><i class="fas fa-utensils me-2"></i> E-Kantin</span>
        <i class="fas fa-chevron-down menu-toggle-icon" id="kantinIcon"></i>
    </a>
    <div class="submenu" id="kantinMenu" style="display: {{ request()->is('kantin/*') ? 'block' : 'none' }};">
        <a class="nav-link {{ request()->routeIs('kantin.cek-saldo') ? 'active' : '' }}" 
           href="{{ route('kantin.cek-saldo') }}">
            <span><i class="fas fa-wallet me-2"></i> Cek Saldo</span>
        </a>
        <a class="nav-link {{ request()->routeIs('kantin.topup') ? 'active' : '' }}" 
           href="{{ route('kantin.topup') }}">
            <span><i class="fas fa-money-bill-wave me-2"></i> Top Up</span>
        </a>
        <a class="nav-link {{ request()->routeIs('kantin.bayar') ? 'active' : '' }}" 
           href="{{ route('kantin.bayar') }}">
            <span><i class="fas fa-cash-register me-2"></i> Bayar</span>
        </a>
        <a class="nav-link {{ request()->routeIs('kantin.riwayat') ? 'active' : '' }}" 
           href="{{ route('kantin.riwayat') }}">
            <span><i class="fas fa-history me-2"></i> Riwayat</span>
        </a>
    </div>
    @endif

    <!-- Menu Admin (HANYA ADMIN) -->
    @if(auth()->user()->role === 'admin')
    <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 10px;">
    
    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
       href="{{ route('users.index') }}">
        <span><i class="fas fa-users me-2"></i> Kelola User</span>
    </a>
    <a class="nav-link {{ request()->routeIs('jadwal-sholat.*') ? 'active' : '' }}" 
       href="{{ route('jadwal-sholat.index') }}">
        <span><i class="fas fa-calendar-alt me-2"></i> Jadwal Sholat</span>
    </a>
    <a class="nav-link {{ request()->routeIs('pengaturan.waktu.*') ? 'active' : '' }}" 
       href="{{ route('pengaturan.waktu.index') }}">
        <span><i class="fas fa-cog me-2"></i> Pengaturan Waktu</span>
    </a>
    @endif

    <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 10px;">

    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <span><i class="fas fa-sign-out-alt me-2"></i> Logout</span>
    </a>
</nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('visible');
            mainContent.classList.toggle('expanded');
            toggleBtn.classList.toggle('sidebar-open');

            // Save state to localStorage
            const isOpen = !sidebar.classList.contains('hidden');
            localStorage.setItem('sidebarOpen', isOpen);
        }

        // Toggle Submenu
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

            // Save submenu state
            localStorage.setItem(menuId, menu.style.display);
        }

        // Load sidebar state on page load
        window.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            // Load sidebar state
            const sidebarOpen = localStorage.getItem('sidebarOpen');
            if (sidebarOpen === 'false') {
                sidebar.classList.add('hidden');
                mainContent.classList.add('expanded');
            } else {
                toggleBtn.classList.add('sidebar-open');
                sidebar.classList.add('visible');
            }

            // Load submenu states
            ['presensiMenu', 'kantinMenu'].forEach(menuId => {
                const state = localStorage.getItem(menuId);
                if (state) {
                    const menu = document.getElementById(menuId);
                    const icon = document.getElementById(menuId.replace('Menu', 'Icon'));
                    menu.style.display = state;
                    if (state === 'block' && icon) {
                        icon.classList.add('rotated');
                    }
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Rotate icons for initially open submenus
            document.querySelectorAll('.submenu').forEach(submenu => {
                if (submenu.style.display === 'block') {
                    const menuId = submenu.id;
                    const icon = document.getElementById(menuId.replace('Menu', 'Icon'));
                    if (icon) icon.classList.add('rotated');
                }
            });
        });

        // Auto-close sidebar on mobile when clicking a link
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't close if it's a submenu toggle
                    if (this.getAttribute('href') === '#') return;
                    
                    setTimeout(() => {
                        toggleSidebar();
                    }, 300);
                });
            });
        }
    </script>
    @yield('scripts')
</body>
</html>