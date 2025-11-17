<x-layouts.auth>
    <x-slot:title>Réinitialiser le mot de passe</x-slot:title>

    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Réinitialiser le mot de passe</h1>
            <p class="auth-card__subtitle">Choisissez un nouveau mot de passe pour votre compte</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div class="auth-form__group">
                <label for="email" class="auth-form__label">Adresse e-mail</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
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
                <label for="password" class="auth-form__label">Nouveau mot de passe</label>
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
                    Réinitialiser le mot de passe
                </button>
            </div>
        </form>
    </div>
</x-layouts.auth>
