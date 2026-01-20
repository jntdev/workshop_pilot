<x-layouts.auth>
    <x-slot:title>Connexion</x-slot:title>

    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Connexion</h1>
            <p class="auth-card__subtitle">Acces reserve aux comptes autorises</p>
        </div>

        @if (session('status'))
            <div class="auth-alert auth-alert--success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-alert auth-alert--error">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="auth-form">
            <div class="auth-form__actions">
                <a href="{{ route('auth.google.redirect') }}" class="auth-form__button auth-form__button--primary">
                    Se connecter avec Google
                </a>
            </div>
        </div>
    </div>
</x-layouts.auth>
