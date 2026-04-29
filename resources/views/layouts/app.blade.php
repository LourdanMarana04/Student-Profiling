<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --ccs-orange: #f36a10;
                --ccs-orange-deep: #c94d00;
                --ccs-orange-soft: #fff1e3;
                --ccs-ink: #151515;
                --ccs-cream: #fffaf5;
                --ccs-border: #f3c8a4;
            }

            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                background:
                    radial-gradient(circle at top right, rgba(243, 106, 16, 0.14), transparent 30%),
                    linear-gradient(180deg, #fffaf5 0%, #fff3e7 100%) !important;
                font-family: 'Instrument Sans', 'Segoe UI', sans-serif !important;
                min-height: 100vh;
                color: var(--ccs-ink);
            }

            .app-wrapper { display: flex; min-height: 100vh; }
            .app-sidebar {
                width: 260px;
                background:
                    linear-gradient(180deg, rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.35)),
                    linear-gradient(160deg, #ff8d36 0%, #f36a10 52%, #bc4000 100%);
                padding: 1.25rem 1rem;
                color: white;
                position: fixed;
                height: 100vh;
                overflow: hidden;
                z-index: 1000;
                box-shadow: 14px 0 38px rgba(118, 43, 1, 0.16);
                display: flex;
                flex-direction: column;
            }

            .sidebar-brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
                padding: 0.85rem 0.8rem;
                border-radius: 20px;
                background: rgba(255, 247, 237, 0.14);
                border: 1px solid rgba(255, 255, 255, 0.18);
                color: #fffaf5;
                text-decoration: none;
                text-align: center;
                backdrop-filter: blur(10px);
            }

            .sidebar-brand-logo {
                width: 88px;
                height: 88px;
                flex-shrink: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.16);
                border-radius: 50%;
                padding: 0.5rem;
                box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18);
            }

            .sidebar-brand-logo img,
            .sidebar-brand-logo svg {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .sidebar-brand-copy {
                width: 100%;
                min-width: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.18rem;
            }

            .sidebar-brand-kicker {
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.06em;
                color: rgba(255, 250, 245, 0.9);
                line-height: 1.3;
            }

            .sidebar-brand-title {
                font-size: 0.9rem;
                font-weight: 800;
                line-height: 1.2;
                text-transform: uppercase;
            }

            .sidebar-brand-subtitle {
                font-size: 0.72rem;
                color: rgba(255, 250, 245, 0.88);
                line-height: 1.25;
            }

            .sidebar-user-panel {
                background: rgba(255, 255, 255, 0.12);
                border: 1px solid rgba(255, 255, 255, 0.18);
                border-radius: 16px;
                padding: 0.85rem 0.9rem 0.9rem;
                margin-bottom: 1rem;
                color: #fff;
            }

            .sidebar-user-name {
                font-size: 0.9rem;
                font-weight: 700;
                margin-bottom: 0.15rem;
            }

            .sidebar-user-role {
                font-size: 0.72rem;
                color: rgba(255, 255, 255, 0.78);
                text-transform: capitalize;
            }

            .sidebar-menu {
                list-style: none;
                display: flex;
                flex-direction: column;
                gap: 0.4rem;
            }
            .sidebar-menu-item {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                padding: 0.75rem 0.85rem;
                border-radius: 12px;
                color: rgba(255, 247, 237, 0.86);
                text-decoration: none;
                font-size: 0.82rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.25s ease;
                border: 1px solid transparent;
                line-height: 1.2;
            }
            .sidebar-menu-item:hover, .sidebar-menu-item.active {
                background: rgba(255, 248, 242, 0.18);
                color: white;
                border-color: rgba(255, 255, 255, 0.14);
                transform: translateX(3px);
            }
            .sidebar-menu-item span { font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; }
            .sidebar-menu-item svg { width: 17px; height: 17px; }
            .app-content { flex-grow: 1; margin-left: 260px; padding: 2rem; }
            .page-header { background: white; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }
            .page-header h1 { font-size: 2rem; font-weight: 700; color: var(--ccs-ink); }
            .page-header p { font-size: 0.875rem; color: #8d6e63; margin-top: 0.25rem; }
            @media (max-width: 768px) {
                .app-sidebar { width: 60px; padding: 1rem 0.5rem; }
                .app-content { margin-left: 60px; padding: 1rem; }
                .sidebar-brand-copy, .sidebar-menu-item:not(.icon-only) { display: none; }
                .sidebar-brand { justify-content: center; padding: 0.5rem; }
                .sidebar-brand-logo { width: 42px; height: 42px; }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="app-wrapper">
            <div class="app-sidebar">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <span class="sidebar-brand-logo">
                        <x-application-logo class="block h-full w-full" />
                    </span>
                    <span class="sidebar-brand-copy">
                        <span class="sidebar-brand-kicker">Pnc Student Profiling</span>
                        <span class="sidebar-brand-title">College of Computing Studies</span>
                        <span class="sidebar-brand-subtitle">Pamantasan ng Cabuyao</span>
                    </span>
                </a>

                <div class="sidebar-user-panel">
                    <div class="sidebar-user-name">{{ $authUser?->name ?? Auth::user()->name }}</div>
                    <div class="sidebar-user-role">{{ $authUserRole ? ucfirst($authUserRole) : 'Guest' }}</div>
                </div>

                <ul class="sidebar-menu">
                    @if(auth()->user()->canManageStudents())
                        <li><a href="{{ route('dashboard') }}" data-spa-link class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 10.5L12 4L20 10.5V20H14.5V14H9.5V20H4V10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span><span>Dashboard</span></a></li>
                        <li><a href="{{ route('users') }}" class="sidebar-menu-item {{ request()->routeIs('users') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 12C14.4853 12 16.5 9.98528 16.5 7.5C16.5 5.01472 14.4853 3 12 3C9.51472 3 7.5 5.01472 7.5 7.5C7.5 9.98528 9.51472 12 12 12Z" stroke="currentColor" stroke-width="1.8"/><path d="M4.5 20.25C4.5 16.7982 7.29822 14 10.75 14H13.25C16.7018 14 19.5 16.7982 19.5 20.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Users</span></a></li>
                        <li><a href="{{ route('students.index') }}" class="sidebar-menu-item {{ request()->routeIs('students.*') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M4 19C4.8 16.6 6.7 15 9 15C11.3 15 13.2 16.6 14 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="17" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M15.5 19C16 17.3 17.3 16.1 19 15.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Students</span></a></li>
                        <li><a href="{{ route('faculty.index') }}" class="sidebar-menu-item {{ request()->routeIs('faculty.*') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 12C12.4853 12 14.5 9.98528 14.5 7.5C14.5 5.01472 12.4853 3 10 3C7.51472 3 5.5 5.01472 5.5 7.5C5.5 9.98528 7.51472 12 10 12Z" stroke="currentColor" stroke-width="1.8"/><path d="M2.5 20.25C2.5 16.7982 5.29822 14 8.75 14H11.25C14.7018 14 17.5 16.7982 17.5 20.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M19 11V18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M22.5 14.5H15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Faculty</span></a></li>
                        @if(auth()->user()->isAdmin())
                            <li><a href="{{ route('reports') }}" class="sidebar-menu-item {{ request()->routeIs('reports') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 5H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 9H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 13H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 17H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Reports</span></a></li>
                        @endif
                        <li><a href="{{ route('queries.index') }}" class="sidebar-menu-item {{ request()->routeIs('queries.*') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/><path d="M20 20L16.65 16.65" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Search</span></a></li>
                    @else
                        @if(auth()->user()->isFaculty())
                            <li><a href="{{ route('faculty.dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 10.5L12 4L20 10.5V20H14.5V14H9.5V20H4V10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span><span>Faculty Dashboard</span></a></li>
                        @else
                            <li><a href="{{ route('students.me') }}" class="sidebar-menu-item {{ request()->routeIs('students.show') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/><path d="M5 19C5.9 16.3 8.31 14.7 12 14.7C15.69 14.7 18.1 16.3 19 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>My Profile</span></a></li>
                        @endif
                    @endif
                    <li><a href="{{ route('profile.edit') }}" class="sidebar-menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8.5A3.5 3.5 0 1 0 12 15.5A3.5 3.5 0 1 0 12 8.5Z" stroke="currentColor" stroke-width="1.8"/><path d="M19 12C19 12.4 18.96 12.79 18.88 13.16L21 14.8L18.8 18.61L16.28 17.59C15.7 18.03 15.05 18.37 14.34 18.57L14 21H10L9.66 18.57C8.95 18.37 8.3 18.03 7.72 17.59L5.2 18.61L3 14.8L5.12 13.16C5.04 12.79 5 12.4 5 12C5 11.6 5.04 11.21 5.12 10.84L3 9.2L5.2 5.39L7.72 6.41C8.3 5.97 8.95 5.63 9.66 5.43L10 3H14L14.34 5.43C15.05 5.63 15.7 5.97 16.28 6.41L18.8 5.39L21 9.2L18.88 10.84C18.96 11.21 19 11.6 19 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg></span><span>Settings</span></a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="sidebar-menu-item text-left w-full"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 5H8C6.89543 5 6 5.89543 6 7V17C6 18.1046 6.89543 19 8 19H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span>Log Out</span></button>
                        </form>
                    </li>
                </ul>
            </div>

            <div class="app-content">
                @isset($header)
                    <div class="page-header">
                        {{ $header }}
                    </div>
                @endisset

                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>
        </div>
    </body>
</html>
