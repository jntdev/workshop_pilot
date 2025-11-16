<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Livewire Counter Demo</title>

        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="counter-demo">
        <h1 class="counter-demo__title">Livewire Demo</h1>

        @livewire('counter')

        <a href="/" class="counter-demo__cta">
            Retour à l'accueil
        </a>
    </body>
</html>
