<x-layouts.auth>
    <x-slot:title>Mot de passe oublié</x-slot:title>

    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Mot de passe oublié</h1>
            <p class="auth-card__subtitle">
                Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf

            <!-- Session Status -->
            @if (session('status'))
                <div class="auth-alert auth-alert--success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Email -->
            <div class="auth-form__group">
                <label for="email" class="auth-form__label">Adresse e-mail</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="auth-form__input @error('email') auth-form__input--error @enderror"
                />
                @error('email')
                    <p class="auth-form__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-form__actions">
                <button type="submit" class="auth-form__button auth-form__button--primary">
                    Envoyer le lien
                </button>
            </div>

            <div class="auth-form__footer">
                <a href="{{ route('login') }}" class="auth-form__link">
                    Retour à la connexion
                </a>
            </div>
        </form>
    </div>
</x-layouts.auth>
