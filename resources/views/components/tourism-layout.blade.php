<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Explore Egypt') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/map.css'])
    @stack('styles')
</head>
<body class="tourism-body light">
<header id="top" class="site-header" x-data="tourismNavigation()" x-init="init()" @keydown.escape.window="closeMenus()" @resize.window="handleResize()">
    <div class="container site-header__inner">
        <a href="{{ route('home') }}" class="site-brand" aria-label="Explore Egypt home">
            <span class="site-brand__mark" aria-hidden="true">E</span>
            <span class="site-brand__text">
                <strong>Explore Egypt</strong>
                <small>Premium travel guide</small>
            </span>
        </a>

        <nav class="site-nav" aria-label="Primary navigation">
            <a href="{{ route('home') }}" @class(['site-nav__link', 'is-active' => request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
            <a href="{{ route('explore') }}" @class(['site-nav__link', 'is-active' => request()->routeIs('explore') || request()->routeIs('attractions.show')]) @if(request()->routeIs('explore') || request()->routeIs('attractions.show')) aria-current="page" @endif>Explore</a>
            <a href="{{ route('civilizations.index') }}" @class(['site-nav__link', 'is-active' => request()->routeIs('civilizations.*')]) @if(request()->routeIs('civilizations.*')) aria-current="page" @endif>Civilizations</a>
            <a href="{{ route('regions.index') }}" @class(['site-nav__link', 'is-active' => request()->routeIs('regions.*')]) @if(request()->routeIs('regions.*')) aria-current="page" @endif>Regions</a>
        </nav>

        <div class="site-actions">
            <form action="{{ route('explore') }}" method="GET" class="site-search" role="search" aria-label="Search attractions">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    class="site-search__input"
                    placeholder="Search attractions"
                    aria-label="Search attractions"
                >
                <button type="submit" class="site-search__button" aria-label="Submit search">Search</button>
            </form>

            <button
                type="button"
                class="theme-toggle"
                x-data="themeToggle()"
                x-init="init()"
                @click="toggle()"
                :aria-label="getLabel()"
                :title="getLabel()"
            >
                <span x-text="getIcon()"></span>
            </button>

            @auth
                <div class="site-user" x-ref="userMenu">
                    <button type="button" class="site-user__button" @click="toggleUserMenu()" :aria-expanded="userMenuOpen.toString()" aria-controls="user-menu-panel" aria-label="Open user menu">
                        <span class="site-user__avatar">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatarUrl('80x80') }}" alt="{{ auth()->user()->name }} avatar">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </span>
                    </button>

                    <div
                        id="user-menu-panel"
                        class="site-user__dropdown"
                        x-show="userMenuOpen"
                        x-transition:enter="transition ease-out duration-160"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-120"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        x-cloak
                    >
                        <a href="{{ route('profile.edit') }}">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="site-auth-actions">
                    <a href="{{ route('login') }}" class="btn btn-outline site-login-link">Log in</a>
                </div>
            @endauth

            <button
                type="button"
                class="mobile-toggle"
                :class="{ 'is-active': mobileOpen }"
                x-ref="mobileToggle"
                @click="toggleMobileMenu()"
                :aria-expanded="mobileOpen.toString()"
                aria-controls="mobile-nav"
                aria-label="Toggle navigation"
            >
                <span class="mobile-toggle-line"></span>
                <span class="mobile-toggle-line"></span>
                <span class="mobile-toggle-line"></span>
            </button>
        </div>
    </div>

    <div
        id="mobile-nav"
        class="mobile-nav"
        x-ref="mobilePanel"
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-220"
        x-transition:enter-start="opacity-0 -translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-160"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-3"
        x-cloak
    >
        <div class="container mobile-nav__panel">
            <div class="mobile-nav__group">
                <p class="mobile-nav__eyebrow">Navigate</p>
                <a href="{{ route('home') }}" @click="closeMobileMenu()" @class(['is-active' => request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a href="{{ route('explore') }}" @click="closeMobileMenu()" @class(['is-active' => request()->routeIs('explore') || request()->routeIs('attractions.show')]) @if(request()->routeIs('explore') || request()->routeIs('attractions.show')) aria-current="page" @endif>Explore</a>
                <a href="{{ route('civilizations.index') }}" @click="closeMobileMenu()" @class(['is-active' => request()->routeIs('civilizations.*')]) @if(request()->routeIs('civilizations.*')) aria-current="page" @endif>Civilizations</a>
                <a href="{{ route('regions.index') }}" @click="closeMobileMenu()" @class(['is-active' => request()->routeIs('regions.*')]) @if(request()->routeIs('regions.*')) aria-current="page" @endif>Regions</a>
            </div>

            <div class="mobile-nav__group">
                <p class="mobile-nav__eyebrow">Account</p>
                @auth
                    <a href="{{ route('profile.edit') }}" @click="closeMobileMenu()">Profile</a>
                    <a href="{{ route('favorites.index') }}" @click="closeMobileMenu()">Favorites</a>
                    <form method="POST" action="{{ route('logout') }}" class="mobile-logout-form">
                        @csrf
                        <button type="submit" @click="closeMobileMenu()">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" @click="closeMobileMenu()">Log in</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" @click="closeMobileMenu()">Create account</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</header>

<main class="site-main">
    {{ $slot }}
</main>

<div id="favorite-feedback" class="favorite-feedback" aria-live="polite"></div>

<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <a href="{{ route('home') }}" class="site-footer__logo">Explore Egypt</a>
            <p>Discover curated cities, ancient civilizations, and memorable attractions through a polished travel experience.</p>
        </div>

        <div class="site-footer__column">
            <h2>Quick links</h2>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('explore') }}">Explore</a>
            <a href="{{ route('civilizations.index') }}">Civilizations</a>
            <a href="{{ route('regions.index') }}">Regions</a>
            <a href="{{ route('favorites.index') }}">Favorites</a>
        </div>

        <div class="site-footer__column">
            <h2>Contact</h2>
            <a href="mailto:hello@exploreegypt.com">hello@exploreegypt.com</a>
            <span>Cairo, Egypt</span>
            <span>Open daily for your next journey</span>
        </div>

        <div class="site-footer__column">
            <h2>Follow</h2>
            <div class="site-footer__socials">
                <a href="https://facebook.com" target="_blank" rel="noreferrer" aria-label="Facebook">Facebook</a>
                <a href="https://instagram.com" target="_blank" rel="noreferrer" aria-label="Instagram">Instagram</a>
                <a href="https://x.com" target="_blank" rel="noreferrer" aria-label="X">X</a>
                <a href="https://youtube.com" target="_blank" rel="noreferrer" aria-label="YouTube">YouTube</a>
            </div>
        </div>
    </div>

    <div class="container site-footer__bottom">
        <p>© {{ now()->year }} Explore Egypt. All rights reserved.</p>
        <a href="#top" class="site-footer__top">Back to top</a>
    </div>
</footer>
@stack('scripts')
</body>
</html>
