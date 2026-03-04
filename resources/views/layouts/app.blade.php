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
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #137fec;
            --primary-dark: #0f66be;
            --primary-light: #e6f2fe;
            --sidebar-bg: #ffffff;
            --sidebar-text: #475569;
            --sidebar-hover: #f1f5f9;
            --sidebar-active: #e6f2fe;
            --sidebar-active-text: #137fec;
            --bg-color: #f8fafc;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
        }

        * {
            font-family: 'Manrope', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            color: var(--text-main);
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            z-index: 1020;
        }

        .navbar-custom .navbar-brand {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .navbar-custom .nav-link,
        .navbar-custom .dropdown-toggle {
            color: #475569 !important;
            font-weight: 600;
        }

        .navbar-custom .dropdown-toggle:hover {
            color: var(--primary) !important;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            min-height: calc(100vh - 65px);
            transition: transform 0.3s ease;
            z-index: 100;
        }

        .sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
            margin: 0.25rem 1rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            color: var(--primary-dark);
            background: var(--sidebar-hover);
        }

        .sidebar .nav-link.active {
            color: var(--sidebar-active-text);
            background: var(--sidebar-active);
            font-weight: 700;
        }

        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar-header {
            padding: 1.5rem 1rem 1rem;
        }

        .sidebar-header h6 {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            padding-left: 1rem;
        }

        .sidebar-heading {
            color: #94a3b8;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 1.5rem 1rem 0.5rem;
            margin-left: 1rem;
        }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 2rem;
            min-height: calc(100vh - 65px);
            overflow-x: hidden;
        }

        /* Stat cards */
        .stat-card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stat-card .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.03em;
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
        }

        /* Custom card */
        .card-custom {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            background: white;
        }

        .card-custom .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            border-radius: 16px 16px 0 0 !important;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Table */
        .table-custom {
            font-size: 0.9rem;
        }

        .table-custom th {
            background: var(--bg-color);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            border-bottom: 2px solid var(--border-color);
            padding: 1rem;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 1rem;
            color: #334155;
            border-bottom: 1px solid var(--border-color);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-sm {
            border-radius: 6px;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #cbd5e1;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        /* Alert auto-dismiss */
        .alert {
            border-radius: 12px;
            font-weight: 600;
            border: none;
        }
        
        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-auto-dismiss {
            animation: fadeOut 0.5s ease-in-out 5s forwards;
        }

        @keyframes fadeOut {
            to { opacity: 0; height: 0; padding: 0; margin: 0; overflow: hidden; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 65px;
                left: -260px;
                width: 260px;
                height: calc(100vh - 65px);
                z-index: 1050;
                box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            }
            .sidebar.show {
                transform: translateX(260px);
            }
            .main-content {
                padding: 1rem;
            }
        }

        /* Page header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h4 {
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .page-header .breadcrumb {
            font-size: 0.85rem;
            margin: 0;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #64748b;
        }

        /* Badge */
        .badge-role {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 700;
        }
        
        .badge {
            border-radius: 6px;
            font-weight: 600;
            padding: 0.35em 0.65em;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link text-dark d-lg-none me-2 p-0" id="sidebarToggle" style="font-size: 1.5rem;">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span style="background: var(--primary); color: white; border-radius: 8px; padding: 4px 8px; margin-right: 8px; font-size: 1rem;">PT</span>
                Sistem e-Slip
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px; border: 1px solid #e2e8f0;">
                            <i class="bi bi-person-fill text-secondary"></i>
                        </div>
                        <span class="d-none d-sm-inline">{{ auth()->user()->username ?? 'User' }}</span>
                        @if(auth()->user()->isAdmin())
                            <span class="badge bg-primary text-white badge-role ms-2 d-none d-sm-inline">Admin</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; border: 1px solid var(--border-color); padding: 0.5rem;">
                        <li>
                            <a class="dropdown-item rounded" href="{{ route('password.form') }}">
                                <i class="bi bi-shield-lock me-2 text-secondary"></i>Ganti Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded text-danger fw-bold">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div style="margin-top: 65px; display: flex; align-items: stretch; position: relative;">
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
