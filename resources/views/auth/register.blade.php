<x-layouts.auth>
    <x-slot:title>Inscription</x-slot:title>

    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Créer un compte</h1>
            <p class="auth-card__subtitle">Accédez à l'outil réservé aux collaborateurs</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <!-- Name -->
            <div class="auth-form__group">
                <label for="name" class="auth-form__label">Nom complet</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="auth-form__input @error('name') auth-form__input--error @enderror"
                />
                @error('name')
                    <p class="auth-form__error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="auth-form__group">
                <label for="email" class="auth-form__label">Adresse e-mail</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    class="auth-form__input @error('email') auth-form__input--error @enderror"
                />
                @error('email')
                    <p class="auth-form__error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="auth-form__group">
                <label for="password" class="auth-form__label">Mot de passe</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="auth-form__input @error('password') auth-form__input--error @enderror"
                />
                @error('password')
                    <p class="auth-form__error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="auth-form__group">
                <label for="password_confirmation" class="auth-form__label">Confirmer le mot de passe</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="auth-form__input @error('password_confirmation') auth-form__input--error @enderror"
                />
                @error('password_confirmation')
                    <p class="auth-form__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-form__actions">
                <button type="submit" class="auth-form__button auth-form__button--primary">
                    Créer mon compte
                </button>
            </div>

            <div class="auth-form__footer">
                <a href="{{ route('login') }}" class="auth-form__link">
                    Déjà un compte ? Se connecter
                </a>
            </div>
        </form>
    </div>
</x-layouts.auth>
