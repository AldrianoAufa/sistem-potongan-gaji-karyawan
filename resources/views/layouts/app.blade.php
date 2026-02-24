<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Potongan Gaji') — PT Primatex Indonesia</title>
    <meta name="description" content="Sistem informasi pengelolaan potongan gaji karyawan PT Primatex Indonesia">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1E3A5F;
            --primary-light: #4A90D9;
            --sidebar-bg: #1A2332;
            --sidebar-hover: #263548;
            --sidebar-active: #1E3A5F;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #F0F2F5;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.5rem 1rem;
        }

        .navbar-custom .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .navbar-custom .nav-link,
        .navbar-custom .dropdown-toggle {
            color: rgba(255,255,255,0.9) !important;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            overflow-y: auto;
            min-height: calc(100vh - 56px);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: var(--sidebar-hover);
            border-left-color: var(--primary-light);
        }

        .sidebar .nav-link.active {
            color: white;
            background: var(--sidebar-active);
            border-left-color: var(--primary-light);
            font-weight: 600;
        }

        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
            font-size: 1rem;
        }

        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h6 {
            color: rgba(255,255,255,0.5);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 1.5rem;
            min-height: calc(100vh - 56px);
            overflow-x: hidden;
        }

        /* Stat cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .stat-card .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #6C757D;
            margin-top: 2px;
        }

        /* Custom card */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .card-custom .card-header {
            background: white;
            border-bottom: 1px solid #E9ECEF;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Table */
        .table-custom {
            font-size: 0.875rem;
        }

        .table-custom th {
            background: #F8F9FA;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6C757D;
            border-bottom: 2px solid #DEE2E6;
        }

        .table-custom td {
            vertical-align: middle;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
        }

        /* Alert auto-dismiss */
        .alert-auto-dismiss {
            animation: fadeOut 0.5s ease-in-out 5s forwards;
        }

        @keyframes fadeOut {
            to { opacity: 0; height: 0; padding: 0; margin: 0; overflow: hidden; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .sidebar.show {
                display: block;
                position: fixed;
                top: 56px;
                left: 0;
                width: 260px;
                height: calc(100vh - 56px);
                z-index: 1050;
            }
        }

        /* Page header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h4 {
            font-weight: 700;
            color: #212529;
            margin: 0;
        }

        .page-header .breadcrumb {
            font-size: 0.8rem;
            margin: 0;
        }

        /* Badge */
        .badge-role {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link text-white d-lg-none me-2" id="sidebarToggle" style="font-size: 1.2rem;">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="#">
                <i class="bi bi-building me-2"></i>Sistem Potongan Gaji
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth()->user()->username ?? 'User' }}
                        @if(auth()->user()->isAdmin())
                            <span class="badge bg-warning text-dark badge-role ms-1">Admin</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div style="margin-top: 56px; display: flex; align-items: stretch;">
        @yield('body')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar toggle -->
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar')?.classList.toggle('show');
        });
    </script>

    @stack('scripts')
</body>
</html>
