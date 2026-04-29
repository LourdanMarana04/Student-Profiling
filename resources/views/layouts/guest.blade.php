<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ccs-orange: #f36a10;
            --ccs-orange-deep: #bf4300;
        }

        /* 🔥 LOCK EVERYTHING */
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden; /* NO SCROLL ANYWHERE */
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Instrument Sans', sans-serif;

            background:
                radial-gradient(circle at top left, rgba(255, 220, 188, 0.5), transparent 28%),
                radial-gradient(circle at bottom right, rgba(243, 106, 16, 0.2), transparent 30%),
                linear-gradient(145deg, #fffaf5 0%, #ffe8d3 45%, #ffd6ae 100%);
        }

        /* SCALE */
        .zoom-wrapper {
            transform: scale(0.85); /* slightly adjusted to fit */
            transform-origin: center;
        }

        /* MAIN */
        .main-wrapper {
            display: flex;
            width: 1150px;
            height: 680px; /* FIXED HEIGHT = no overflow */
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 48px rgba(0,0,0,0.15);
            background: #fffaf5;
        }

        .content-fit {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transform: scale(0.9);
            transform-origin: top;
        }

        /* LEFT */
        .left-panel {
            flex: 0 0 30%;
            background: linear-gradient(135deg, var(--ccs-orange), var(--ccs-orange-deep));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem;
        }

        /* RIGHT */
        .right-panel {
            flex: 1;
            padding: 1.5rem;
            overflow: hidden; /* 🚫 NO SCROLL */
        }

        /* REMOVE LARAVEL LIMIT */
        .sm\:max-w-md {
            max-width: 100% !important;
        }

        /* GRID */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem; /* reduced spacing */
        }

        .form-grid .full {
            grid-column: span 2;
        }

        /* INPUT */
        .modern-input {
            width: 100%;
            padding: 0.7rem;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 0.9rem;
        }

        .modern-button {
            width: 100%;
            padding: 0.9rem;
            border-radius: 999px;
            border: none;
            color: white;
            font-weight: bold;
            background: linear-gradient(135deg, var(--ccs-orange), var(--ccs-orange-deep));
        }

        /* TEXT SIZE TUNING */
        h2, h3, p {
            margin: 0.3rem 0;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            body {
                overflow: auto;
            }

            .zoom-wrapper {
                transform: scale(1);
            }

            .main-wrapper {
                flex-direction: column;
                height: auto;
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="zoom-wrapper">
    <div class="main-wrapper">

        <!-- LEFT -->
        <div class="left-panel">
            <div>
                <x-application-logo style="width:70px;height:70px;margin:auto;" />
                <h3>COLLEGE OF COMPUTING STUDIES</h3>
                <p>Pamantasan ng Cabuyao</p>
                <h2>Register</h2>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="right-panel">
            <div class="content-fit">
                <div class="form-grid">
                    <div class="full">
                         {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
