<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Potongan Gaji PT Primatex Indonesia</title>
    <meta name="description" content="Login ke Sistem Potongan Gaji PT Primatex Indonesia">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Background dengan gambar dan gradient overlay */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/img/primatex-slide.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }


        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                inset 0 1px 1px rgba(255, 255, 255, 0.6);
            padding: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo section */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1e3a5f, #2c5a8c);
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: 0 10px 20px -5px rgba(30, 58, 95, 0.3);
            position: relative;
            overflow: hidden;
        }

        .logo-wrapper::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 26px;
            background: linear-gradient(135deg, rgba(255,255,255,0.5), rgba(255,255,255,0));
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .logo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-header h4 {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 400;
        }

        /* Form styling */
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.2s ease;
            z-index: 4;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            font-size: 0.95rem;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            background: white;
            transition: all 0.2s ease;
            color: #1e293b;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #1e3a5f;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
        }

        .form-control-custom:focus + .input-icon {
            color: #1e3a5f;
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
            font-weight: 300;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s ease;
            z-index: 4;
            padding: 0.25rem;
        }

        .password-toggle:hover {
            color: #1e3a5f;
        }

        /* Checkbox styling */
        .form-check-custom {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1.5rem 0;
        }

        .form-check-input-custom {
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 6px;
            border: 2px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-check-input-custom:checked {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
        }

        .form-check-label-custom {
            color: #475569;
            font-size: 0.9rem;
            font-weight: 400;
            cursor: pointer;
        }

        /* Button styling */
        .btn-login {
            background: linear-gradient(135deg, #1e3a5f, #2c5a8c);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 95, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(30, 58, 95, 0.4);
            background: linear-gradient(135deg, #1e3a5f, #2c5a8c);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px -1px rgba(30, 58, 95, 0.2);
        }

        .btn-login i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        /* Alert styling */
        .alert-custom {
            background: #fee2e2;
            border: 2px solid #fecaca;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #991b1b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .alert-custom i {
            font-size: 1.1rem;
            color: #dc2626;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 400;
        }

        /* Additional info */
        .login-info {
            text-align: center;
            margin-top: 2rem;
            color: #475569;
            font-size: 0.85rem;
        }

        .login-info a {
            color: #1e3a5f;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .login-info a:hover {
            color: #2c5a8c;
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem;
            }

            .logo-wrapper {
                width: 64px;
                height: 64px;
                border-radius: 20px;
            }

            .logo-wrapper img {
                font-size: 2rem;
            }

            .login-header h4 {
                font-size: 1.25rem;
            }

            /* Mobile background fix - full screen */
            .background {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }

            .bg-image {
                background-size: cover;
                background-position: center center;
                width: 100%;
                height: 100%;
            }

            .bg-gradient {
                width: 100%;
                height: 100%;
            }

            .grid-pattern {
                width: 100%;
                height: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="background">
        <div class="bg-image"></div>
        <div class="bg-gradient"></div>
        <div class="grid-pattern"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="logo-wrapper">
                    <img src="/img/logo.jpeg" alt="Logo PT Primatex">
                </div>
                <h4>PT. Primatexco Indonesia</h4>
                <p>Sistem Potongan Gaji</p>
            </div>

            <!-- Error Alert -->
            @if($errors->any())
                <div class="alert-custom">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Username Field -->
                <div class="input-group-custom">
                    <label class="form-label">Username</label>
                    <div style="position: relative;">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" 
                               class="form-control-custom" 
                               name="username" 
                               value="{{ old('username') }}" 
                               placeholder="Masukkan username"
                               required 
                               autofocus
                               id="usernameInput">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="input-group-custom">
                    <label class="form-label">Password</label>
                    <div style="position: relative;">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" 
                               class="form-control-custom" 
                               name="password" 
                               placeholder="Masukkan password" 
                               required 
                               id="passwordInput">
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-check-custom">
                    <input type="checkbox" class="form-check-input-custom" name="remember" id="remember">
                    <label class="form-check-label-custom" for="remember">Ingat saya</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Additional Links -->
            <div class="login-info">
                <p>Lupa password? Hubungi <a href="#">Administrator</a></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            © {{ date('Y') }} PT Primatex Indonesia. All rights reserved.
        </div>
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

        // Optional: Add smooth validation feedback
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('usernameInput');
            const password = document.getElementById('passwordInput');
            
            if (!username.value.trim() || !password.value.trim()) {
                e.preventDefault();
                // You can add custom validation feedback here
            }
        });
    </script>
</body>
</html>