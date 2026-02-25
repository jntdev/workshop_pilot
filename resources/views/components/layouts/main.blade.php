<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="layout">
        <!-- Header -->
        <header class="layout-header">
            <div class="layout-header__inner">
                <div class="layout-header__bar">
                    <h1 class="layout-header__title">
                        <a href="{{ route('home') }}">{{ config('app.name', 'Workshop') }}</a>
                    </h1>

                    <!-- Navigation -->
                    <nav class="layout-nav">
                        <a href="{{ route('location.index') }}" class="layout-nav__link">Location</a>
                        <a href="{{ route('clients.index') }}" class="layout-nav__link">Clients</a>
                        <a href="{{ route('atelier.index') }}" class="layout-nav__link">Atelier</a>
                        <a href="{{ route('dashboard') }}" class="layout-nav__link">Dashboard</a>

                        <!-- Privacy Toggle -->
                        <div class="privacy-toggle" id="privacy-toggle" title="Ctrl+Shift+P pour basculer">
                            <span class="privacy-toggle__label privacy-toggle__label--active" data-mode="atelier">Atelier</span>
                            <button type="button" class="privacy-toggle__switch" onclick="togglePrivacyMode()" aria-label="Basculer entre mode Atelier et Comptoir">
                                <span class="privacy-toggle__slider"></span>
                            </button>
                            <span class="privacy-toggle__label" data-mode="comptoir">Comptoir</span>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="layout-nav__link layout-nav__link--logout">
                                Déconnexion
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Feedback Banner -->
        <div class="feedback-host">
            <x-feedback.banner />
        </div>

        <!-- Breadcrumb -->
        @if (isset($breadcrumb))
            <div class="layout-breadcrumb">
                <div class="layout-breadcrumb__inner">
                    {{ $breadcrumb }}
                </div>
            </div>
        @endif

        <!-- Privacy Banner -->
        <div class="privacy-banner" id="privacy-banner" style="display: none;"></div>

        <!-- Main Content -->
        <main class="layout-main">
            {{ $slot }}
        </main>

        <!-- Privacy Mode Script -->
        <script>
            (function() {
                const STORAGE_KEY = 'workshop_privacy_mode';

                function getPrivacyMode() {
                    return localStorage.getItem(STORAGE_KEY) || 'atelier';
                }

                function setPrivacyMode(mode) {
                    localStorage.setItem(STORAGE_KEY, mode);
                    updateUI(mode);
                }

                function updateUI(mode) {
                    const isComptoir = mode === 'comptoir';
                    const toggle = document.getElementById('privacy-toggle');
                    const banner = document.getElementById('privacy-banner');
                    const switchBtn = toggle?.querySelector('.privacy-toggle__switch');
                    const labels = toggle?.querySelectorAll('.privacy-toggle__label');

                    document.body.setAttribute('data-privacy-mode', mode);

                    if (switchBtn) {
                        switchBtn.classList.toggle('privacy-toggle__switch--comptoir', isComptoir);
                    }

                    if (labels) {
                        labels.forEach(label => {
                            const labelMode = label.getAttribute('data-mode');
                            label.classList.toggle('privacy-toggle__label--active', labelMode === mode);
                        });
                    }

                    if (banner) {
                        banner.style.display = isComptoir ? 'block' : 'none';
                    }
                }

                window.togglePrivacyMode = function() {
                    const current = getPrivacyMode();
                    setPrivacyMode(current === 'atelier' ? 'comptoir' : 'atelier');
                };

                // Raccourci clavier Ctrl+Shift+P
                document.addEventListener('keydown', function(e) {
                    if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'p') {
                        e.preventDefault();
                        window.togglePrivacyMode();
                    }
                });

                // Initialiser au chargement
                document.addEventListener('DOMContentLoaded', function() {
                    updateUI(getPrivacyMode());
                });

                // Écouter les changements depuis d'autres onglets
                window.addEventListener('storage', function(e) {
                    if (e.key === STORAGE_KEY && e.newValue) {
                        updateUI(e.newValue);
                    }
                });
            })();
        </script>
    </body>
</html>
