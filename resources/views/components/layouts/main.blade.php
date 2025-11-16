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
        @vite(['resources/css/app.css', 'resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <!-- Header -->
        <header class="border-b border-neutral-200">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold">
                        <a href="{{ route('home') }}">{{ config('app.name', 'Workshop') }}</a>
                    </h1>

                    <!-- Navigation -->
                    <nav class="flex gap-6">
                        <a href="{{ route('home') }}" class="hover:text-primary transition">Accueil</a>
                        <a href="{{ route('clients.index') }}" class="hover:text-primary transition">Clients</a>
                        <a href="{{ route('atelier.index') }}" class="hover:text-primary transition">Atelier</a>
                        <a href="{{ route('location.index') }}" class="hover:text-primary transition">Location</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Breadcrumb -->
        @if (isset($breadcrumb))
            <div class="bg-neutral-50 border-b border-neutral-200">
                <div class="container mx-auto px-4 py-2">
                    {{ $breadcrumb }}
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            {{ $slot }}
        </main>
    </body>
</html>
