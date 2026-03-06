<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Potongan Gaji') — PT Primatex Indonesia</title>
    <meta name="description" content="Sistem informasi pengelolaan potongan gaji karyawan PT Primatex Indonesia">

    {{-- Bootstrap 5 CSS (lokal via CDN sekali cache) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ===== SPINNER LOADER ===== */
        #page-loader {
            position: fixed;
            inset: 0;
            background: rgba(248, 250, 252, 0.88);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            transition: opacity 0.35s ease;
        }
        #page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        .loader-ring {
            position: relative;
            width: 72px;
            height: 72px;
        }
        .loader-ring svg {
            position: absolute;
            inset: 0;
            transform: rotate(-90deg);
        }
        .loader-ring-track {
            fill: none;
            stroke: #e2e8f0;
            stroke-width: 5;
        }
        .loader-ring-fill {
            fill: none;
            stroke: #137fec;
            stroke-width: 5;
            stroke-linecap: round;
            stroke-dasharray: 188.5;
            stroke-dashoffset: 188.5;
            transition: stroke-dashoffset 0.15s linear;
        }
        .loader-pct {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #137fec;
        }
        .loader-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        /* ===== DESIGN TOKENS ===== */
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

        /* System font stack – menghilangkan ketergantungan Google Fonts */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            min-height: 100vh;
            color: var(--text-main);
            font-size: 0.93rem;
        }

        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: #ffffff;
            box-shadow: 0 1px 0 var(--border-color);
            padding: 0.65rem 1.5rem;
            z-index: 1020;
        }
        .navbar-custom .navbar-brand {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .navbar-custom .nav-link,
        .navbar-custom .dropdown-toggle {
            color: #475569 !important;
            font-weight: 600;
        }
        .navbar-custom .dropdown-toggle:hover { color: var(--primary) !important; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 240px;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            min-height: calc(100vh - 62px);
            z-index: 100;
        }
        .sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 0.6rem 1rem;
            font-size: 0.88rem;
            font-weight: 600;
            border-radius: 6px;
            margin: 2px 10px;
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
            width: 22px;
            text-align: center;
            margin-right: 8px;
            font-size: 1rem;
        }
        .sidebar-header  { padding: 1.2rem 1rem 0.75rem; }
        .sidebar-header h6 {
            color: #94a3b8;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            padding-left: 0.75rem;
        }
        .sidebar-heading {
            color: #94a3b8;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 1.25rem 1rem 0.4rem;
            margin-left: 0.75rem;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            padding: 1.5rem;
            min-height: calc(100vh - 62px);
            overflow-x: hidden;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: white;
        }
        .stat-card .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }
        .stat-card .stat-value {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text-main);
        }
        .stat-card .stat-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
        }

        /* ===== CUSTOM CARD ===== */
        .card-custom {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: white;
        }
        .card-custom .card-header {
            background: #fafafa;
            border-bottom: 1px solid var(--border-color);
            border-radius: 10px 10px 0 0 !important;
            padding: 0.9rem 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        /* ===== TABLE ===== */
        .table-custom { font-size: 0.875rem; }
        .table-custom th {
            background: var(--bg-color);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
        }
        .table-custom td {
            vertical-align: middle;
            padding: 0.75rem 1rem;
            color: #334155;
            border-bottom: 1px solid var(--border-color);
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
            border-radius: 6px;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        .btn-sm   { border-radius: 5px; }
        .btn-sm.btn-primary, .btn-sm.btn-outline-primary { font-size: 0.8rem; }

        /* ===== FORM CONTROLS ===== */
        .form-control, .form-select {
            border-radius: 6px;
            border-color: #cbd5e1;
            padding: 0.5rem 0.85rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        /* ===== ALERTS ===== */
        .alert {
            border-radius: 8px;
            font-weight: 600;
            border: none;
            font-size: 0.875rem;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .alert-auto-dismiss { animation: fadeOut 0.4s ease 4s forwards; }
        @keyframes fadeOut { to { opacity: 0; height: 0; padding: 0; margin: 0; overflow: hidden; } }

        /* ===== BADGES ===== */
        .badge-role {
            font-size: 0.68rem; padding: 3px 7px;
            border-radius: 5px; font-weight: 700;
        }
        .badge { border-radius: 5px; font-weight: 600; }

        /* ===== PAGE HEADER ===== */
        .page-header { margin-bottom: 1.5rem; }
        .page-header h4 {
            font-weight: 800; color: var(--text-main);
            margin: 0; font-size: 1.15rem;
        }
        .page-header .breadcrumb { font-size: 0.82rem; margin: 0; font-weight: 500; }
        .breadcrumb-item a      { color: var(--primary); text-decoration: none; }
        .breadcrumb-item.active { color: #64748b; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 62px; left: -240px;
                width: 240px;
                height: calc(100vh - 62px);
                z-index: 1050;
                box-shadow: 2px 0 8px rgba(0,0,0,0.08);
                transition: transform 0.25s ease;
            }
            .sidebar.show { transform: translateX(240px); }
            .main-content  { padding: 1rem; }
        }
    </style>

    @stack('styles')
</head>
<body>
    {{-- Spinner Loader --}}
    <div id="page-loader">
        <div class="loader-ring">
            <svg viewBox="0 0 64 64" width="72" height="72">
                <circle class="loader-ring-track" cx="32" cy="32" r="30"/>
                <circle class="loader-ring-fill" id="loader-fill" cx="32" cy="32" r="30"/>
            </svg>
            <div class="loader-pct"><span id="loader-pct-num">0</span>%</div>
        </div>
        <div class="loader-label">Memuat halaman...</div>
    </div>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link text-dark d-lg-none me-2 p-0" id="sidebarToggle" style="font-size: 1.4rem;">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span style="background: var(--primary); color: white; border-radius: 6px; padding: 3px 7px; margin-right: 8px; font-size: 0.9rem; font-weight: 700;">PT</span>
                Sistem e-Slip
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px; border: 1px solid #e2e8f0;">
                            <i class="bi bi-person-fill text-secondary" style="font-size: 0.85rem;"></i>
                        </div>
                        <span class="d-none d-sm-inline" style="font-size: 0.875rem;">{{ auth()->user()->username ?? 'User' }}</span>
                        @if(auth()->user()->isAdmin())
                            <span class="badge bg-primary text-white badge-role ms-2 d-none d-sm-inline">Admin</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px; border: 1px solid var(--border-color); padding: 0.4rem; min-width: 180px;">
                        <li>
                            <a class="dropdown-item rounded" href="{{ route('password.form') }}" style="font-size: 0.875rem;">
                                <i class="bi bi-shield-lock me-2 text-secondary"></i>Ganti Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded text-danger fw-bold" style="font-size: 0.875rem;">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div style="margin-top: 62px; display: flex; align-items: stretch; position: relative;">
        @yield('body')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== SPINNER LOADER =====
        (function() {
            var loader  = document.getElementById('page-loader');
            var fill    = document.getElementById('loader-fill');
            var numEl   = document.getElementById('loader-pct-num');
            var circ    = 188.5;   // 2 * pi * 30
            var pct     = 0;
            var timer   = null;
            var done    = false;

            function setPercent(p) {
                pct = Math.min(p, 99);
                if (numEl) numEl.textContent = Math.round(pct);
                if (fill)  fill.style.strokeDashoffset = circ - (circ * pct / 100);
            }

            function tick() {
                // Accelerate toward 90% gradually
                var step = pct < 30  ? 3 :
                           pct < 60  ? 2 :
                           pct < 80  ? 1 :
                           pct < 90  ? 0.4 : 0.1;
                setPercent(pct + step);
                if (pct < 90) timer = setTimeout(tick, 60);
            }

            function startLoader() {
                if (loader) loader.classList.remove('hidden');
                pct  = 0; done = false;
                clearTimeout(timer);
                setPercent(0);
                tick();
            }

            function finishLoader() {
                if (done) return;
                done = true;
                clearTimeout(timer);
                setPercent(100);
                setTimeout(function() {
                    if (loader) loader.classList.add('hidden');
                    setTimeout(function() {
                        if (loader) loader.style.display = 'none';
                    }, 380);
                }, 200);
            }

            // Boot
            startLoader();
            window.addEventListener('load', finishLoader);

            // Navigation trigger for links
            document.addEventListener('click', function(e) {
                var a = e.target.closest('a[href]');
                if (a && !a.target && !a.dataset.bsToggle &&
                    a.href && !a.href.startsWith('#') &&
                    !a.href.startsWith('javascript') && !a.href.startsWith('mailto') &&
                    a.origin === location.origin) {
                    loader.style.display = '';
                    startLoader();
                }
            });

            // Navigation trigger for forms
            document.addEventListener('submit', function(e) {
                loader.style.display = '';
                startLoader();
            });
        })();

        // ===== SIDEBAR TOGGLE =====
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar')?.classList.toggle('show');
        });

        // ===== ALERT AUTO DISMISS =====
        setTimeout(function() {
            document.querySelectorAll('.alert-auto-dismiss').forEach(function(el) {
                el.style.transition = 'opacity 0.4s';
                el.style.opacity = '0';
                setTimeout(function(){ el.remove(); }, 400);
            });
        }, 4000);
    </script>

    @stack('scripts')
</body>
</html>
