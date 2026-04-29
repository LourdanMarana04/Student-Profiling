<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ccs-orange: #f36a10;
            --ccs-orange-deep: #c54a00;
            --ccs-bg: #ffe2c4;
            --ccs-card: rgba(255, 251, 246, 0.96);
            --ccs-border: #efcfb3;
            --ccs-text: #181818;
            --ccs-muted: #76695d;
            --ccs-error: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 18px;
            font-family: 'Instrument Sans', 'Segoe UI', sans-serif;
            color: var(--ccs-text);
            background:
                radial-gradient(circle at 18% 20%, rgba(255, 255, 255, 0.6), transparent 22%),
                radial-gradient(circle at 82% 78%, rgba(255, 177, 103, 0.35), transparent 24%),
                linear-gradient(180deg, #ffe7cf 0%, var(--ccs-bg) 100%);
        }

        .zoom-wrapper {
            transform: scale(0.85); /* slightly adjusted to fit */
            transform-origin: center;
        }

        .content-fit {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transform: scale(0.9);
            transform-origin: center;
        }

        .login-shell {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .login-card {
            width: min(100%, 402px);
            border-radius: 28px;
            padding: 40px 38px 34px;
            background: var(--ccs-card);
            border: 1px solid rgba(239, 207, 179, 0.9);
            box-shadow:
                0 18px 40px rgba(220, 121, 32, 0.18),
                0 0 60px rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(8px);
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo-wrap {
            width: 98px;
            height: 98px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #fff;
            box-shadow: 0 10px 24px rgba(211, 120, 39, 0.2);
        }

        .brand-logo {
            width: 78px;
            height: 78px;
            object-fit: contain;
        }

        .brand-title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            line-height: 1.08;
        }

        .brand-subtitle {
            margin: 8px 0 0;
            color: var(--ccs-muted);
            font-size: 0.98rem;
        }

        .page-title {
            margin: 20px 0 0;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .status-message {
            margin: 0 0 18px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #fff3e7;
            color: #a14404;
            font-size: 0.92rem;
            text-align: center;
        }

        .field {
            margin-bottom: 18px;
        }

        .field-label {
            display: inline-block;
            margin-bottom: 9px;
            font-size: 0.98rem;
            font-weight: 600;
            color: #49392f;
        }

        .field-input {
            width: 100%;
            height: 54px;
            border: 1px solid var(--ccs-border);
            border-radius: 16px;
            padding: 0 17px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--ccs-text);
            font-size: 0.98rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .field-input::placeholder {
            color: #a79b91;
        }

        .field-input:focus {
            border-color: var(--ccs-orange);
            box-shadow: 0 0 0 4px rgba(243, 106, 16, 0.12);
            transform: translateY(-1px);
        }

        .error-text {
            margin-top: 7px;
            font-size: 0.82rem;
            color: var(--ccs-error);
        }

        .login-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 4px 0 18px;
            color: #5a4d43;
            font-size: 0.94rem;
        }

        .login-meta input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--ccs-orange);
        }

        .login-button {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, #ff6a00 0%, #c94d00 100%);
            color: #fff;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 14px 26px rgba(224, 108, 22, 0.26);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 28px rgba(224, 108, 22, 0.3);
        }

        .login-links {
            margin-top: 26px;
            text-align: center;
        }

        .forgot-link,
        .signup-link {
            color: var(--ccs-orange-deep);
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover,
        .signup-link:hover {
            text-decoration: underline;
        }

        .signup-copy {
            margin: 14px 0 0;
            font-size: 0.92rem;
            color: #44372e;
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 12px;
            }

            .login-card {
                padding: 30px 22px 26px;
                border-radius: 24px;
            }

            .brand-title {
                font-size: 1.45rem;
            }

            .page-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <div class="zoom-wrapper">
        <div class="content-fit">
            <main class="login-shell">
        <section class="login-card" aria-labelledby="login-heading">
            <div class="brand">
                <div class="brand-logo-wrap">
                    <x-application-logo class="brand-logo" />
                </div>
                <h1 class="brand-title">College of Computing Studies</h1>
                <p class="brand-subtitle">Pamantasan ng Cabuyao</p>
                <h2 class="page-title" id="login-heading">Login</h2>
            </div>

            @if (session('status'))
                <div class="status-message">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email" class="field-label">Email</label>
                    <input
                        id="email"
                        class="field-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="your@email.com"
                    >
                    <x-input-error :messages="$errors->get('email')" class="error-text" />
                </div>

                <div class="field">
                    <label for="password" class="field-label">Password</label>
                    <input
                        id="password"
                        class="field-input"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="........"
                    >
                    <x-input-error :messages="$errors->get('password')" class="error-text" />
                </div>

                <label class="login-meta" for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="login-button">
                    Sign In
                </button>
            </form>

            <div class="login-links">
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                @endif

                <p class="signup-copy">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="signup-link">Sign up</a>
                </p>
            </div>
        </section>
    </main>


        </div>
    </div>


</body>
</html>
