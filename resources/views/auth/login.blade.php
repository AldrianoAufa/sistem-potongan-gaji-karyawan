<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Potongan Gaji PT Primatex Indonesia</title>
    <meta name="description" content="Login ke Sistem Potongan Gaji PT Primatex Indonesia">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #137fec;
            --primary-dark: #0f66be;
            --primary-light: #e6f2fe;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: "Manrope", sans-serif;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
            color: var(--text-dark);
        }

        /* Background elements */
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
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210,100%,92%,1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(210,88%,96%,1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(210,100%,94%,1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(210,80%,93%,1) 0px, transparent 50%);
            z-index: 1;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 24px;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 20px 40px -10px rgba(19, 127, 236, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            padding: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.7);
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Logo section */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrapper {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--primary), #489eff);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: 0 12px 24px -6px rgba(19, 127, 236, 0.4);
            position: relative;
            text-transform: uppercase;
            font-weight: 800;
            color: white;
            font-size: 1.8rem;
            letter-spacing: 1px;
        }

        .login-header h4 {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--text-dark);
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Form styling */
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
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
            transition: color 0.3s ease;
            z-index: 4;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            font-size: 0.95rem;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #ffffff;
            transition: all 0.3s ease;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
            background: #ffffff;
        }

        .form-control-custom:focus + .input-icon {
            color: var(--primary);
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s ease;
            z-index: 4;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .password-toggle:hover {
            color: var(--primary);
            background: var(--primary-light);
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
            border-radius: 4px;
            border: 2px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            display: grid;
            place-content: center;
            background-color: white;
            position: relative;
        }

        .form-check-input-custom::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em white;
            background-color: white;
            transform-origin: center;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        }

        .form-check-input-custom:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input-custom:checked::before {
            transform: scale(1);
        }

        .form-check-label-custom {
            color: #475569;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* Button styling */
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(19, 127, 236, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(19, 127, 236, 0.4);
            background: linear-gradient(135deg, #2b8ff4, var(--primary));
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(19, 127, 236, 0.2);
        }

        .btn-login i {
            font-size: 1.1rem;
        }

        /* Alert styling */
        .alert-custom {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #991b1b;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .alert-custom i {
            font-size: 1.1rem;
            color: #ef4444;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Additional info */
        .login-info {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .login-info a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .login-info a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }

            .logo-wrapper {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .login-header h4 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="background">
        <div class="bg-image"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="logo-wrapper">
                    <!-- Text logo instead of image for cleaner aesthetic -->
                    <span>PT</span>
                </div>
                <h4>PT. Primatexco Indonesia</h4>
                <p>Portal e-Slip & Potongan Gaji</p>
            </div>

            <!-- Error Alert -->
            @if($errors->any())
                <div class="alert-custom">
                    <i class="bi bi-x-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Username Field -->
                <div class="input-group-custom">
                    <label class="form-label" for="usernameInput">Username / NIK</label>
                    <div>
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" 
                               class="form-control-custom" 
                               name="username" 
                               value="{{ old('username') }}" 
                               placeholder="Masukkan username Anda"
                               required 
                               autofocus
                               id="usernameInput">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="input-group-custom">
                    <label class="form-label" for="passwordInput">Password</label>
                    <div>
                        <i class="bi bi-shield-lock input-icon"></i>
                        <input type="password" 
                               class="form-control-custom" 
                               name="password" 
                               placeholder="Masukkan password Anda" 
                               required 
                               id="passwordInput">
                        <span class="password-toggle" onclick="togglePassword()" title="Tampilkan/Sembunyikan Password">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-check-custom">
                    <input type="checkbox" class="form-check-input-custom" name="remember" id="remember">
                    <label class="form-check-label-custom" for="remember">Ingat perangkat ini</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login">
                    Masuk ke Sistem
                    <i class="bi bi-arrow-right-short fs-4"></i>
                </button>
            </form>

            <!-- Additional Links -->
            <div class="login-info">
                <span>Lupa password? </span><a href="#">Hubungi Administrator</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            &copy; {{ date('Y') }} PT Primatex Indonesia.<br>All rights reserved.
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('toggleIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash-fill';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>