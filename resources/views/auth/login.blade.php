<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Potongan Gaji PT Primatex Indonesia</title>
    <meta name="description" content="Login ke Sistem Potongan Gaji PT Primatex Indonesia">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1E3A5F 0%, #4A90D9 50%, #1E3A5F 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo .icon-wrapper {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1E3A5F, #4A90D9);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .login-logo .icon-wrapper i {
            font-size: 2rem;
            color: white;
        }

        .login-logo h4 {
            font-weight: 700;
            color: #1E3A5F;
            margin-bottom: 0.25rem;
        }

        .login-logo p {
            color: #6C757D;
            font-size: 0.85rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            border: 2px solid #E9ECEF;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #4A90D9;
            box-shadow: 0 0 0 3px rgba(74,144,217,0.15);
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6C757D;
            z-index: 4;
        }

        .btn-login {
            background: linear-gradient(135deg, #1E3A5F, #4A90D9);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30,58,95,0.3);
        }

        .footer-text {
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            margin-top: 2rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6C757D;
            z-index: 4;
        }
    </style>
</head>
<body>
    <div>
        <div class="login-card">
            <div class="login-logo">
                <div class="icon-wrapper">
                    <i class="bi bi-building"></i>
                </div>
                <h4>PT Primatex Indonesia</h4>
                <p>Sistem Potongan Gaji</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger py-2 px-3" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem;">Username</label>
                    <div class="input-group-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" name="username"
                               value="{{ old('username') }}" placeholder="Masukkan username"
                               required autofocus id="usernameInput">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem;">Password</label>
                    <div class="input-group-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" class="form-control" name="password"
                               placeholder="Masukkan password" required id="passwordInput">
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size: 0.85rem;">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>
        </div>

        <p class="footer-text">© {{ date('Y') }} PT Primatex Indonesia. All rights reserved.</p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
