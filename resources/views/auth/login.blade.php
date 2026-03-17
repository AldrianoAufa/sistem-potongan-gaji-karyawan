<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Potongan Gaji PT Primatex Indonesia</title>
    <meta name="description" content="Login ke Sistem Potongan Gaji PT Primatex Indonesia">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #137fec;
            --primary-dark: #0f66be;
            --primary-light: #e6f2fe;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem;
            color: var(--text-dark);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.04);
            padding: 2.25rem 2rem;
            animation: fadeIn 0.25s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .login-header { text-align: center; margin-bottom: 1.75rem; }

        .logo-wrapper {
            width: 60px; height: 60px;
            background: var(--primary);
            border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            font-weight: 800; color: white; font-size: 1.5rem;
        }

        .login-header h4 {
            font-weight: 700; font-size: 1.25rem;
            color: var(--text-dark); margin-bottom: 0.25rem;
        }
        .login-header p { color: var(--text-muted); font-size: 0.875rem; }

        /* Form */
        .form-label { font-weight: 600; font-size: 0.82rem; color: #475569; margin-bottom: 0.4rem; }

        .input-group-custom { position: relative; margin-bottom: 1.1rem; }
        .input-icon {
            position: absolute; left: 0.85rem; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; font-size: 1rem; z-index: 4;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.65rem 0.9rem 0.65rem 2.5rem;
            font-size: 0.9rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: var(--text-dark);
            font-weight: 500;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control-custom:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .form-control-custom::placeholder { color: #94a3b8; font-weight: 400; }

        .password-toggle {
            position: absolute; right: 0.5rem; top: 50%;
            transform: translateY(-50%);
            cursor: pointer; color: #94a3b8; z-index: 4;
            padding: 0.4rem 0.5rem; border-radius: 6px;
            transition: color 0.15s ease;
        }
        .password-toggle:hover { color: var(--primary); }

        /* Checkbox */
        .form-check-custom { display: flex; align-items: center; gap: 0.5rem; margin: 1.1rem 0; }
        .form-check-label-custom { color: #475569; font-size: 0.875rem; font-weight: 600; cursor: pointer; }

        /* Button */
        .btn-login {
            background: var(--primary);
            color: white; border: none;
            border-radius: 8px;
            padding: 0.7rem;
            font-weight: 700; font-size: 0.95rem;
            width: 100%; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
            transition: background 0.15s ease;
        }
        .btn-login:hover { background: var(--primary-dark); }
        .btn-login:active { transform: scale(0.99); }
        .btn-login.loading { opacity: 0.75; pointer-events: none; }

        /* Alert */
        .alert-custom {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 8px; padding: 0.7rem 0.9rem;
            margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 0.6rem;
            color: #991b1b; font-size: 0.82rem; font-weight: 600;
        }

        /* Footer */
        .login-footer {
            text-align: center; margin-top: 1.5rem;
            color: #94a3b8; font-size: 0.78rem;
        }
        .login-info {
            text-align: center; margin-top: 1.25rem;
            color: #64748b; font-size: 0.82rem; font-weight: 500;
        }
        .login-info a { color: var(--primary); text-decoration: none; font-weight: 700; }
        .login-info a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .login-card { padding: 1.75rem 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            {{-- Header --}}
            <div class="login-header">
                <div class="logo-wrapper"><span>PT</span></div>
                <h4>PT. Primatexco Indonesia</h4>
                <p>Portal e-Slip &amp; Potongan Gaji</p>
            </div>

            {{-- Error Alert --}}
            @if($errors->any())
                <div class="alert-custom">
                    <i class="bi bi-x-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="input-group-custom">
                    <label class="form-label" for="usernameInput">Username / NIK</label>
                    <div style="position:relative;">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text"
                               class="form-control-custom"
                               name="username"
                               value="{{ old('username') }}"
                               placeholder="Masukkan username Anda"
                               required autofocus id="usernameInput">
                    </div>
                </div>

                <div class="input-group-custom">
                    <label class="form-label" for="passwordInput">Password</label>
                    <div style="position:relative;">
                        <i class="bi bi-shield-lock input-icon"></i>
                        <input type="password"
                               class="form-control-custom"
                               name="password"
                               placeholder="Masukkan password Anda"
                               required id="passwordInput">
                        <span class="password-toggle" onclick="togglePassword()" title="Tampilkan/Sembunyikan">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-check-custom">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label-custom" for="remember">Ingat perangkat ini</label>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <span id="btnText">Masuk ke Sistem</span>
                    <i class="bi bi-arrow-right-short" style="font-size:1.1rem;" id="btnIcon"></i>
                </button>
            </form>

            <div class="login-info">
                <span>Lupa password? </span><a href="#">Hubungi Administrator</a>
            </div>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} PT Primatex Indonesia. All rights reserved.
        </div>
    </div>

    <script>
        // Toggle password
        function togglePassword() {
            var input = document.getElementById('passwordInput');
            var icon  = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash-fill';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Submit loading state on button
        document.getElementById('loginForm').addEventListener('submit', function() {
            var btn = document.getElementById('btnLogin');
            document.getElementById('btnText').textContent = 'Memproses...';
            document.getElementById('btnIcon').className = 'bi bi-hourglass-split';
            if (btn) btn.style.opacity = '0.75';
        });
    </script>
</body>
</html>