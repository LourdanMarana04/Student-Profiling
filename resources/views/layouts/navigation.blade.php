<nav x-data="{ open: false }" class="app-nav">
    <style>
        .app-nav {
            background: rgba(255, 250, 245, 0.96);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(243, 200, 164, 0.8);
            box-shadow: 0 10px 24px rgba(124, 57, 10, 0.08);
        }

        .nav-link {
            color: #a24808;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            padding: 0.6rem 1rem;
            border-radius: 999px;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(243, 106, 16, 0.12);
            color: #111111;
        }

        .nav-button {
            background: linear-gradient(135deg, #f36a10 0%, #bf4300 100%);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 0.5rem 1.25rem;
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .dropdown-menu {
            background: rgba(255, 250, 245, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(243, 200, 164, 0.8);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            border-radius: 14px;
        }

        .dropdown-item {
            color: #2a1d16;
            text-decoration: none;
            padding: 0.75rem 1rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: rgba(243, 106, 16, 0.1);
            color: #a24808;
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
        }

        .brand-copy {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-kicker {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #a56a44;
        }

        .brand-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1b1b1b;
        }
    </style>

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="brand-link">
                        <x-application-logo class="block h-11 w-auto" />
                        <span class="brand-copy">
                            <span class="brand-kicker">Pnc Student Profiling</span>
                            <span class="brand-title">College of Computing Studies</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="nav-button">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1" style="display: inline-block;">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="dropdown-item">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" class="dropdown-item"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md" style="color: #a24808;">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="dropdown-item" style="display: block;">
                {{ __('Dashboard') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1" style="border-top: 1px solid rgba(0,0,0,0.1);">
            <div class="px-4">
                <div class="font-medium text-base" style="color: #333;">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm" style="color: #999;">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="dropdown-item" style="display: block;">
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a href="{{ route('logout') }}" class="dropdown-item" style="display: block;"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
