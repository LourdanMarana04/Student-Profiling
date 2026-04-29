@php
    $logoPath = public_path('images/ccs.logo.png');
@endphp

@if (file_exists($logoPath))
    <img src="{{ asset('images/ccs.logo.png') }}" alt="College of Computing Studies logo" {{ $attributes }}>
@else
    <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
        <defs>
            <linearGradient id="ccsRing" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ff9b3d" />
                <stop offset="100%" stop-color="#f36a10" />
            </linearGradient>
            <linearGradient id="ccsShield" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ff8f2f" />
                <stop offset="100%" stop-color="#e95b0c" />
            </linearGradient>
        </defs>

        <circle cx="60" cy="60" r="57" fill="url(#ccsRing)" stroke="#111111" stroke-width="4" />
        <circle cx="60" cy="60" r="43" fill="#fff7ed" stroke="#111111" stroke-width="3" />

        <path
            d="M60 29L88 44V63C88 81 75.5 94.5 60 98C44.5 94.5 32 81 32 63V44L60 29Z"
            fill="url(#ccsShield)"
            stroke="#111111"
            stroke-width="3.5"
            stroke-linejoin="round"
        />

        <path d="M47 53H73" stroke="#111111" stroke-width="4" stroke-linecap="round" />
        <path d="M47 63H73" stroke="#111111" stroke-width="4" stroke-linecap="round" />
        <path d="M47 73H64" stroke="#111111" stroke-width="4" stroke-linecap="round" />

        <circle cx="60" cy="18" r="3.5" fill="#111111" />
        <circle cx="60" cy="102" r="3.5" fill="#111111" />
        <circle cx="18" cy="60" r="3.5" fill="#111111" />
        <circle cx="102" cy="60" r="3.5" fill="#111111" />

        <text
            x="60"
            y="16"
            text-anchor="middle"
            font-size="8"
            font-weight="800"
            fill="#ffffff"
            transform="rotate(-27 60 60)"
        >CCS</text>
    </svg>
@endif
