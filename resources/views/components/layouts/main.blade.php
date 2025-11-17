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
                        <a href="{{ route('home') }}" class="layout-nav__link">Accueil</a>
                        <a href="{{ route('clients.index') }}" class="layout-nav__link">Clients</a>
                        <a href="{{ route('atelier.index') }}" class="layout-nav__link">Atelier</a>
                        <a href="{{ route('location.index') }}" class="layout-nav__link">Location</a>

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

        <!-- Breadcrumb -->
        @if (isset($breadcrumb))
            <div class="layout-breadcrumb">
                <div class="layout-breadcrumb__inner">
                    {{ $breadcrumb }}
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main class="layout-main">
            {{ $slot }}
        </main>
    </body>
</html>
