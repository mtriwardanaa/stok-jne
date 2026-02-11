<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok GA - Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* ─── LEFT PANEL ─── */
        .login-left {
            flex: 1;
            background: linear-gradient(160deg, #020617 0%, #0f172a 25%, #1e3a5f 55%, #2563eb 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated aurora blobs */
        .aurora {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: float 12s ease-in-out infinite;
        }
        .aurora-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #6366f1, transparent 70%);
            top: -15%; right: -10%;
            animation-delay: 0s;
        }
        .aurora-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #3b82f6, transparent 70%);
            bottom: -10%; left: -8%;
            animation-delay: -4s;
        }
        .aurora-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #8b5cf6, transparent 70%);
            top: 40%; left: 30%;
            animation-delay: -8s;
            opacity: 0.15;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(25px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Grid pattern overlay */
        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Floating geometric shapes */
        .geo {
            position: absolute;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            animation: spin-slow 20s linear infinite;
        }
        .geo-1 { width: 120px; height: 120px; top: 12%; right: 15%; animation-duration: 25s; }
        .geo-2 { width: 80px; height: 80px; bottom: 20%; left: 12%; animation-duration: 18s; animation-direction: reverse; }
        .geo-3 { width: 60px; height: 60px; top: 55%; right: 25%; border-radius: 50%; }
        .geo-4 { width: 180px; height: 180px; bottom: 8%; right: 5%; border-radius: 50%; border-color: rgba(255,255,255,0.03); animation-duration: 30s; }

        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Brand content */
        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        .brand-icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 28px;
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px -12px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.06);
            transition: transform 0.4s, box-shadow 0.4s;
        }
        .brand-icon-wrap:hover {
            transform: translateY(-4px) rotate(3deg);
            box-shadow: 0 28px 70px -12px rgba(99,102,241,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .brand-icon-wrap svg {
            width: 40px; height: 40px;
            color: #93c5fd;
            filter: drop-shadow(0 0 8px rgba(147,197,253,0.4));
        }

        .brand-name {
            font-size: 3rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.06em;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .brand-name span {
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-tagline {
            color: rgba(255,255,255,0.4);
            font-size: 0.95rem;
            font-weight: 400;
            max-width: 320px;
            line-height: 1.8;
            margin: 0 auto;
        }

        .brand-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .brand-stats div {
            text-align: center;
        }
        .brand-stats .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
        }
        .brand-stats .stat-label {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-top: 0.2rem;
        }

        /* ─── RIGHT PANEL ─── */
        .login-right {
            width: 520px;
            min-width: 440px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3.5rem;
            background: #fff;
            position: relative;
        }

        .login-card {
            max-width: 380px;
            width: 100%;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out 0.15s both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h2 {
            font-weight: 800;
            font-size: 1.85rem;
            color: #0f172a;
            letter-spacing: -0.04em;
            margin-bottom: 0.3rem;
        }
        .login-card .subtitle {
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 2.2rem;
        }

        /* Form elements */
        .form-group { margin-bottom: 1.4rem; }
        .form-label {
            display: block;
            font-weight: 700;
            font-size: 0.78rem;
            color: #475569;
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        .input-wrap {
            position: relative;
        }
        .input-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px; height: 18px;
            color: #94a3b8;
            transition: color 0.2s;
        }
        .input-wrap input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            font-size: 0.88rem;
            font-family: inherit;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-wrap input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.08);
            background: #fff;
        }
        .input-wrap input:focus + .input-icon,
        .input-wrap input:focus ~ .input-icon { color: #6366f1; }
        .input-wrap input::placeholder { color: #cbd5e1; }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.8rem;
        }
        .remember-row label {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.82rem; color: #64748b; cursor: pointer;
        }
        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px; accent-color: #6366f1; cursor: pointer;
        }

        .btn-signin {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            font-weight: 700;
            font-size: 0.92rem;
            font-family: inherit;
            color: #fff;
            width: 100%;
            cursor: pointer;
            letter-spacing: -0.01em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px -4px rgba(79,70,229,0.4);
        }
        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px -4px rgba(79,70,229,0.5);
        }
        .btn-signin:active {
            transform: translateY(0);
            box-shadow: 0 4px 16px -4px rgba(79,70,229,0.3);
        }
        .btn-signin svg { width: 18px; height: 18px; }

        .alert-error {
            background: linear-gradient(135deg, #fef2f2, #fff1f2);
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.82rem;
            margin-bottom: 1.4rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-footer {
            text-align: center;
            margin-top: 3rem;
            font-size: 0.75rem;
            color: #cbd5e1;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .login-left { display: none !important; }
            .login-right {
                width: 100%; min-width: unset; min-height: 100vh;
                background: linear-gradient(180deg, #f0f4ff 0%, #fff 40%);
            }
            .login-card { max-width: 400px; }
            .mobile-brand { display: flex !important; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- LEFT PANEL -->
        <div class="login-left">
            <div class="grid-pattern"></div>
            <div class="aurora aurora-1"></div>
            <div class="aurora aurora-2"></div>
            <div class="aurora aurora-3"></div>
            <div class="geo geo-1"></div>
            <div class="geo geo-2"></div>
            <div class="geo geo-3"></div>
            <div class="geo geo-4"></div>

            <div class="brand-content">
                <div class="brand-icon-wrap">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="brand-name">Stok<span>GA</span></div>
                <p class="brand-tagline">Sistem Manajemen Inventaris & Stok Barang<br>General Affairs — JNE Express</p>
                <div class="brand-stats">
                    <div>
                        <div class="stat-value">24/7</div>
                        <div class="stat-label">Monitoring</div>
                    </div>
                    <div>
                        <div class="stat-value">Real-time</div>
                        <div class="stat-label">Tracking</div>
                    </div>
                    <div>
                        <div class="stat-value">100%</div>
                        <div class="stat-label">Akurat</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="login-right">
            <div class="login-card">
                <!-- Mobile brand -->
                <div class="mobile-brand" style="display:none;flex-direction:column;align-items:center;margin-bottom:2.5rem">
                    <div style="font-size:2rem;font-weight:800;color:#0f172a;letter-spacing:-0.04em">Stok<span style="background:linear-gradient(135deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">GA</span></div>
                    <div style="font-size:0.75rem;color:#94a3b8;margin-top:0.25rem">JNE Express</div>
                </div>

                <h2>Welcome back! 👋</h2>
                <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>

                @if ($errors->any())
                    <div class="alert-error">
                        <svg style="width:16px;height:16px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-wrap">
                            <input id="username" name="username" type="text"
                                placeholder="Masukkan username" required
                                value="{{ old('username') }}" autofocus>
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrap">
                            <input id="password" name="password" type="password"
                                placeholder="Masukkan password" required>
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>

                    <div class="remember-row">
                        <label>
                            <input type="checkbox" id="remember" name="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn-signin">
                        Sign In
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </form>

                <div class="login-footer">
                    &copy; {{ date('Y') }} StokGA &mdash; JNE Express Pontianak
                </div>
            </div>
        </div>
    </div>
</body>
</html>
