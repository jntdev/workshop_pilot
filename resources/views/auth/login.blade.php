<x-layouts.auth>
    <x-slot:title>Connexion</x-slot:title>

    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Connexion</h1>
            <p class="auth-card__subtitle">Accédez à votre espace de travail</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="auth-form">
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
                    autocomplete="current-password"
                    class="auth-form__input @error('password') auth-form__input--error @enderror"
                />
                @error('password')
                    <p class="auth-form__error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="auth-form__group auth-form__group--checkbox">
                <label for="remember_me" class="auth-form__checkbox-label">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="auth-form__checkbox"
                    />
                    <span>Se souvenir de moi</span>
                </label>
            </div>

            <div class="auth-form__actions">
                <button type="submit" class="auth-form__button auth-form__button--primary">
                    Se connecter
                </button>
            </div>

            <div class="auth-form__footer">
                <a href="{{ route('password.request') }}" class="auth-form__link">
                    Mot de passe oublié ?
                </a>
                @if (config('fortify.features') && in_array(Laravel\Fortify\Features::registration(), config('fortify.features')))
                    <a href="{{ route('register') }}" class="auth-form__link">
                        Créer un compte
                    </a>
                @endif
            </div>
        </form>
    </div>
</x-layouts.auth>
