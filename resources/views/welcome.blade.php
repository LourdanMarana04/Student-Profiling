<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Welcome</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
        :root {
            --ccs-orange: #f36a10;
            --ccs-orange-deep: #bf4300;
            --ccs-cream: #fff7ee;
            --ccs-ink: #1f1f1f;
            --ccs-muted: #6f4f39;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(255, 220, 188, 0.58), transparent 24%),
                radial-gradient(circle at bottom right, rgba(243, 106, 16, 0.22), transparent 30%),
                linear-gradient(145deg, #fff9f3 0%, #ffe9d5 48%, #ffd8b3 100%);
            font-family: 'Instrument Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .welcome-container {
            text-align: center;
            color: var(--ccs-ink);
            max-width: 780px;
            padding: 2.25rem;
            background: rgba(255, 249, 242, 0.92);
            border: 1px solid rgba(241, 199, 166, 0.92);
            border-radius: 30px;
            box-shadow: 0 24px 48px rgba(155, 77, 17, 0.14);
            backdrop-filter: blur(10px);
        }

        .welcome-logo {
            width: 140px;
            height: 140px;
            margin: 0 auto 1.5rem;
            padding: 0.5rem;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 16px 30px rgba(0,0,0,0.08);
        }

        .welcome-logo img,
        .welcome-logo svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .welcome-kicker {
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #9f531f;
            margin-bottom: 0.65rem;
        }

        .welcome-title {
            font-size: 3.1rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.05;
        }

        .welcome-subtitle {
            font-size: 1.08rem;
            font-weight: 500;
            margin-bottom: 2rem;
            color: var(--ccs-muted);
            max-width: 620px;
            margin-left: auto;
            margin-right: auto;
        }

        .welcome-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .welcome-button {
            background: linear-gradient(135deg, var(--ccs-orange) 0%, var(--ccs-orange-deep) 100%);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 1rem 2rem;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 14px 28px rgba(191, 67, 0, 0.22);
        }

        .welcome-button.secondary {
            background: transparent;
            color: var(--ccs-orange-deep);
            border: 2px solid rgba(191, 67, 0, 0.18);
            box-shadow: none;
        }

        .welcome-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }

        .brand-text {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.875rem;
            font-weight: 700;
            color: #7a573f;
        }

        @media (max-width: 640px) {
            .welcome-title {
                font-size: 2.2rem;
            }
            .welcome-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-logo">
            <x-application-logo class="block h-full w-full" />
        </div>
        <div class="welcome-kicker">Pamantasan ng Cabuyao</div>
        <h1 class="welcome-title">College of Computing Studies</h1>
        <p class="welcome-subtitle">A CCS-themed student profiling portal for managing academic, activity, affiliation, and personal student records in one place.</p>
        <div class="welcome-actions">
            <a href="{{ route('login') }}" class="welcome-button">Login</a>
            <a href="{{ route('register') }}" class="welcome-button secondary">Student Sign Up</a>
        </div>
    </div>
    <div class="brand-text">Pnc Student Profiling • College of Computing Studies</div>
</body>
</html>
