<div class="client-form">
    <h2 class="client-form__title">Formulaire Client</h2>

    <form wire:submit="save" class="client-form__form">
        <div class="client-form__section">
            <h3 class="client-form__section-title">Informations personnelles</h3>

            <div class="client-form__grid">
                <div class="client-form__field">
                    <label for="prenom" class="client-form__label">Prénom *</label>
                    <input type="text" id="prenom" wire:model.blur="prenom" class="client-form__input">
                    @error('prenom')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="client-form__field">
                    <label for="nom" class="client-form__label">Nom *</label>
                    <input type="text" id="nom" wire:model.blur="nom" class="client-form__input">
                    @error('nom')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="client-form__field">
                    <label for="telephone" class="client-form__label">Téléphone *</label>
                    <input type="text" id="telephone" wire:model.blur="telephone" class="client-form__input">
                    @error('telephone')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="client-form__field">
                    <label for="email" class="client-form__label">Email</label>
                    <input type="email" id="email" wire:model.blur="email" class="client-form__input">
                    @error('email')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="client-form__field">
                <label for="adresse" class="client-form__label">Adresse</label>
                <textarea id="adresse" wire:model.blur="adresse" class="client-form__textarea" rows="3"></textarea>
                @error('adresse')
                    <span class="client-form__error">{{ $message }}</span>
                @enderror
            </div>

            <div class="client-form__grid">
                <div class="client-form__field">
                    <label for="origine_contact" class="client-form__label">Origine du contact</label>
                    <input type="text" id="origine_contact" wire:model.blur="origine_contact" class="client-form__input">
                    @error('origine_contact')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="client-form__field">
                <label for="commentaires" class="client-form__label">Commentaires</label>
                <textarea id="commentaires" wire:model.blur="commentaires" class="client-form__textarea" rows="3"></textarea>
                @error('commentaires')
                    <span class="client-form__error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="client-form__section">
            <h3 class="client-form__section-title">Avantages client</h3>

            <div class="client-form__grid">
                <div class="client-form__field">
                    <label for="avantage_type" class="client-form__label">Type d'avantage *</label>
                    <select id="avantage_type" wire:model.live="avantage_type" class="client-form__select">
                        <option value="aucun">Aucun</option>
                        <option value="pourcentage">Pourcentage</option>
                        <option value="montant">Montant (€)</option>
                    </select>
                    @error('avantage_type')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="client-form__field">
                    <label for="avantage_valeur" class="client-form__label">
                        Valeur *
                        @if($avantage_type === 'pourcentage')
                            (%)
                        @elseif($avantage_type === 'montant')
                            (€)
                        @endif
                    </label>
                    <input type="number" step="0.01" id="avantage_valeur" wire:model.blur="avantage_valeur" class="client-form__input">
                    @error('avantage_valeur')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="client-form__field">
                    <label for="avantage_expiration" class="client-form__label">Date d'expiration</label>
                    <input type="date" id="avantage_expiration" wire:model.blur="avantage_expiration" class="client-form__input">
                    @error('avantage_expiration')
                        <span class="client-form__error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="client-form__actions">
            <button type="submit" class="btn btn-primary">Enregistrer le client</button>
        </div>
    </form>
</div>
