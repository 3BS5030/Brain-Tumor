<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.brand') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --font-en: 'Manrope', sans-serif;
            --font-ar: 'Cairo', sans-serif;

            --hospital-primary: #087f8c;
            --hospital-primary-strong: #04606b;
            --hospital-secondary: #66b5ff;
            --hospital-bg: #f2f8ff;
            --hospital-card: #ffffff;
            --hospital-text: #122338;
            --hospital-muted: #62758c;
            --hospital-border: #d7e5f8;
            --hospital-input-bg: #ffffff;
            --hospital-table-head: #ecf4ff;
            --hospital-nav-bg: rgba(255, 255, 255, 0.9);
            --button-soft-bg: #edf6ff;
            --button-soft-text: #14538a;
        }

        body.dark-mode {
            --hospital-primary: #36c8d4;
            --hospital-primary-strong: #25a8b3;
            --hospital-secondary: #8ac8ff;
            --hospital-bg: #120d07;
            --hospital-card: #111c30;
            --hospital-text: #eaf3ff;
            --hospital-muted: #afc3dd;
            --hospital-border: #253a5b;
            --hospital-input-bg: #0d1729;
            --hospital-table-head: #172641;
            --hospital-nav-bg: rgba(10, 18, 34, 0.88);
            --button-soft-bg: #1a2b47;
            --button-soft-text: #badaff;
        }

        html[dir='rtl'] body {
            font-family: var(--font-ar);
        }

        html[dir='ltr'] body {
            font-family: var(--font-en);
        }

        body {
            background:
                radial-gradient(circle at 10% 8%, rgba(102, 181, 255, 0.2), transparent 34%),
                radial-gradient(circle at 90% 12%, rgba(8, 127, 140, 0.15), transparent 34%),
                linear-gradient(180deg, #f8fbff 0%, var(--hospital-bg) 100%);
            color: var(--hospital-text);
            min-height: 100vh;
            transition: background .3s ease, color .3s ease;
        }

        body.dark-mode {
            background:
radial-gradient(circle at 12% 8%, rgba(255, 206, 112, 0.22), transparent 34%), radial-gradient(circle at 86% 16%, rgba(212, 145, 55, 0.24), transparent 36%), linear-gradient(160deg, #1a49a8 0%, #00164b 45%, #000000 100%)        }

        .hospital-navbar {
            background-color: var(--hospital-nav-bg);
            border-bottom: 1px solid var(--hospital-border);
            backdrop-filter: blur(8px);
            animation: slideDown .45s ease;
        }

        .navbar-brand {
            color: var(--hospital-text) !important;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .top-nav-link {
            color: var(--hospital-muted);
            text-decoration: none;
            font-weight: 700;
            padding: .45rem .75rem;
            border-radius: .65rem;
            transition: all .2s ease;
        }

        .top-nav-link:hover,
        .top-nav-link.active {
            color: var(--hospital-text);
            background: color-mix(in srgb, var(--hospital-secondary) 20%, transparent);
        }

        .hospital-card {
            background: var(--hospital-card);
            border: 1px solid var(--hospital-border);
            border-radius: 1.15rem;
            box-shadow: 0 10px 30px rgba(9, 34, 62, 0.08);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .hospital-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 34px rgba(9, 34, 62, 0.12);
        }

        .text-secondary { color: var(--hospital-muted) !important; }
        .table, .form-label, .form-check-label, p, h1, h2, h3, h4, h5, h6 { color: var(--hospital-text); }
        .table thead th { background: var(--hospital-table-head); color: var(--hospital-text); border-bottom-color: var(--hospital-border); }
        .table td, .table th { border-color: var(--hospital-border); }

        .form-control {
            background-color: var(--hospital-input-bg);
            color: var(--hospital-text);
            border-color: var(--hospital-border);
            border-radius: .8rem;
        }

        .form-control:focus {
            background-color: var(--hospital-input-bg);
            color: var(--hospital-text);
            border-color: var(--hospital-primary);
            box-shadow: 0 0 0 .22rem rgba(54, 200, 212, .22);
        }

        .form-control::file-selector-button {
            background: color-mix(in srgb, var(--hospital-primary) 18%, var(--hospital-input-bg));
            color: var(--hospital-text);
            border: 0;
            border-inline-end: 1px solid var(--hospital-border);
        }

        .btn {
            border-radius: .8rem;
            font-weight: 700;
            letter-spacing: .2px;
        }

        .btn-hospital {
            background: linear-gradient(135deg, var(--hospital-primary), color-mix(in srgb, var(--hospital-primary) 72%, #35aac5));
            border: none;
            color: #fff;
            box-shadow: 0 8px 18px rgba(8, 127, 140, 0.25);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .btn-hospital:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 12px 22px rgba(8, 127, 140, 0.3);
            background: linear-gradient(135deg, var(--hospital-primary-strong), var(--hospital-primary));
        }

        .btn-soft {
            background: var(--button-soft-bg);
            border: 1px solid color-mix(in srgb, var(--hospital-primary) 25%, var(--hospital-border));
            color: var(--button-soft-text);
        }

        .btn-soft:hover {
            background: color-mix(in srgb, var(--button-soft-bg) 65%, var(--hospital-secondary));
            color: var(--hospital-text);
        }

        .btn-outline-secondary {
            color: var(--hospital-text);
            border-color: var(--hospital-border);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: color-mix(in srgb, var(--hospital-border) 42%, transparent);
            color: var(--hospital-text);
            border-color: var(--hospital-border);
        }

        .btn-outline-info {
            color: var(--hospital-primary);
            border-color: color-mix(in srgb, var(--hospital-primary) 55%, var(--hospital-border));
        }

        .btn-outline-info:hover {
            color: #fff;
            background-color: var(--hospital-primary);
            border-color: var(--hospital-primary);
        }

        .soft-badge {
            background-color: color-mix(in srgb, var(--hospital-secondary) 22%, transparent);
            color: var(--hospital-primary);
            border: 1px solid color-mix(in srgb, var(--hospital-secondary) 40%, transparent);
        }

        .reveal-up {
            opacity: 0;
            transform: translateY(20px);
            animation: revealUp .7s ease forwards;
        }

        .hero-wrap {
            background: linear-gradient(122deg, color-mix(in srgb, var(--hospital-card) 94%, #fff), color-mix(in srgb, var(--hospital-secondary) 12%, var(--hospital-card)));
            border: 1px solid var(--hospital-border);
            box-shadow: 0 16px 36px rgba(9, 34, 62, .12);
        }

        body.dark-mode .hero-wrap {
            background: linear-gradient(122deg, #101c30, #142544);
        }

        .hero-glow {
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            filter: blur(44px);
            opacity: .25;
            pointer-events: none;
        }

        .hero-glow-a { background: #66b5ff; top: -120px; right: -80px; }
        .hero-glow-b { background: #1ecaa1; bottom: -140px; left: -60px; }

        .scan-visual {
            width: min(360px, 100%);
            aspect-ratio: 1/1;
            border-radius: 28px;
            border: 1px solid var(--hospital-border);
            background: linear-gradient(180deg, #fafdff, #e8f6ff);
            position: relative;
            display: grid;
            place-items: center;
            overflow: hidden;
            box-shadow: inset 0 0 40px rgba(11,114,133,.08);
        }

        body.dark-mode .scan-visual {
            background: linear-gradient(180deg, #102036, #0d1b2e);
            box-shadow: inset 0 0 40px rgba(102,198,255,.12);
        }

        .scan-core { width: 80%; height: 80%; border-radius: 50%; border: 2px solid rgba(11,114,133,.2); position: relative; overflow: hidden; }
        .scan-grid { position:absolute; inset:0; background-image: linear-gradient(to right, rgba(11,114,133,.08) 1px, transparent 1px), linear-gradient(to bottom, rgba(11,114,133,.08) 1px, transparent 1px); background-size: 24px 24px; }
        .scan-line { position:absolute; left:0; right:0; height:4px; background: linear-gradient(to right, transparent, #0b7285, transparent); box-shadow: 0 0 16px rgba(11,114,133,.5); animation: scanMove 2.1s ease-in-out infinite; }
        .scan-ring { position:absolute; width:82%; height:82%; border:2px dashed rgba(11,114,133,.25); border-radius:50%; animation: spin 10s linear infinite; }
        .scan-ring-2 { width:68%; height:68%; animation-direction: reverse; animation-duration: 8s; }

        a { color: var(--hospital-primary); }
        a:hover { color: var(--hospital-primary-strong); }

        @keyframes revealUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown { from { opacity: .2; transform: translateY(-10px);} to { opacity: 1; transform: translateY(0);} }
        @keyframes spin { to { transform: rotate(360deg);} }
        @keyframes scanMove { 0%,100% { top: 8%; } 50% { top: 88%; } }

        @media (max-width: 992px) {
            .top-nav-links {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .scan-visual { width: 280px; }
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg hospital-navbar">
    <div class="container py-2">
        <a class="navbar-brand" href="{{ route('get-started') }}">{{ __('messages.brand') }}</a>

        <div class="d-flex align-items-center gap-2 top-nav-links">
            <a class="top-nav-link {{ request()->routeIs('get-started') ? 'active' : '' }}" href="{{ route('get-started') }}">{{ __('messages.home_nav') }}</a>
            <a class="top-nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('messages.about_nav') }}</a>
            <a class="top-nav-link {{ request()->routeIs('scan.index') ? 'active' : '' }}" href="{{ route('scan.index') }}">{{ __('messages.scan_nav') }}</a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button id="themeToggle" class="btn btn-sm btn-outline-secondary" type="button">{{ __('messages.night_mode') }}</button>

            <form method="post" action="{{ route('locale.update') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}">
                <button class="btn btn-sm btn-outline-info" type="submit">{{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}</button>
            </form>

            @auth
                <span class="badge rounded-pill soft-badge">{{ auth()->user()->name }}</span>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('messages.logout') }}</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<main class="container py-4 py-md-5">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        const key = 'neuroscan-theme';
        const button = document.getElementById('themeToggle');

        function setTheme(theme) {
            document.body.classList.toggle('dark-mode', theme === 'dark');
            if (button) {
                button.textContent = theme === 'dark' ? @json(__('messages.light_mode')) : @json(__('messages.night_mode'));
            }
        }

        const stored = localStorage.getItem(key);
        setTheme(stored === 'dark' ? 'dark' : 'light');

        if (button) {
            button.addEventListener('click', function () {
                const next = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
                localStorage.setItem(key, next);
                setTheme(next);
            });
        }
    })();
</script>
@stack('scripts')
</body>
</html>
