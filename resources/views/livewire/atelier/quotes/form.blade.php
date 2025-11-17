<div class="quote-form">
    @if (session()->has('message'))
        <div class="quote-form__alert quote-form__alert--success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save">
        {{-- Section Client --}}
        <section class="quote-form__section">
            <h2 class="quote-form__section-title">Informations client</h2>

            <div class="quote-form__tabs">
                <button type="button" class="quote-form__tab" onclick="showTab('search')">
                    Rechercher un client
                </button>
                <button type="button" class="quote-form__tab" onclick="showTab('new')">
                    Nouveau client
                </button>
            </div>

            <div id="tab-search" class="quote-form__tab-content">
                <livewire:clients.search />
            </div>

            <div id="tab-new" class="quote-form__tab-content" style="display: none;">
                <p class="quote-form__help-text">
                    Remplissez les informations ci-dessous pour créer un nouveau client.
                </p>
            </div>

            <div class="quote-form__client-fields">
                @if($selectedClientId)
                    <div class="quote-form__client-badge">
                        Client sélectionné : {{ $clientPrenom }} {{ $clientNom }}
                    </div>
                @endif

                <div class="quote-form__grid">
                    <div class="quote-form__field">
                        <label for="clientPrenom" class="quote-form__label">Prénom *</label>
                        <input
                            type="text"
                            id="clientPrenom"
                            wire:model="clientPrenom"
                            class="quote-form__input"
                            required
                        >
                        @error('clientPrenom') <span class="quote-form__error">{{ $message }}</span> @enderror
                    </div>

                    <div class="quote-form__field">
                        <label for="clientNom" class="quote-form__label">Nom *</label>
                        <input
                            type="text"
                            id="clientNom"
                            wire:model="clientNom"
                            class="quote-form__input"
                            required
                        >
                        @error('clientNom') <span class="quote-form__error">{{ $message }}</span> @enderror
                    </div>

                    <div class="quote-form__field">
                        <label for="clientEmail" class="quote-form__label">Email</label>
                        <input
                            type="email"
                            id="clientEmail"
                            wire:model="clientEmail"
                            class="quote-form__input"
                        >
                        @error('clientEmail') <span class="quote-form__error">{{ $message }}</span> @enderror
                    </div>

                    <div class="quote-form__field">
                        <label for="clientTelephone" class="quote-form__label">Téléphone</label>
                        <input
                            type="tel"
                            id="clientTelephone"
                            wire:model="clientTelephone"
                            class="quote-form__input"
                        >
                        @error('clientTelephone') <span class="quote-form__error">{{ $message }}</span> @enderror
                    </div>

                    <div class="quote-form__field quote-form__field--full">
                        <label for="clientAdresse" class="quote-form__label">Adresse</label>
                        <input
                            type="text"
                            id="clientAdresse"
                            wire:model="clientAdresse"
                            class="quote-form__input"
                        >
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Prestations --}}
        <section class="quote-form__section">
            <h2 class="quote-form__section-title">Prestations</h2>

            <div class="quote-lines-table">
                <div class="quote-lines-table__header">
                    <div class="quote-lines-table__cell">Intitulé</div>
                    <div class="quote-lines-table__cell">Réf.</div>
                    <div class="quote-lines-table__cell">PA HT</div>
                    <div class="quote-lines-table__cell">PV HT</div>
                    <div class="quote-lines-table__cell">Marge €</div>
                    <div class="quote-lines-table__cell">Marge %</div>
                    <div class="quote-lines-table__cell">TVA %</div>
                    <div class="quote-lines-table__cell">PV TTC</div>
                    <div class="quote-lines-table__cell"></div>
                </div>

                @foreach($lines as $index => $line)
                    <div class="quote-lines-table__row" wire:key="line-{{ $index }}">
                        <div class="quote-lines-table__cell">
                            <input
                                type="text"
                                wire:model="lines.{{ $index }}.title"
                                class="quote-lines-table__input"
                                placeholder="Intitulé"
                                required
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="text"
                                wire:model="lines.{{ $index }}.reference"
                                class="quote-lines-table__input quote-lines-table__input--narrow"
                                placeholder="Réf"
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="lines.{{ $index }}.purchase_price_ht"
                                wire:change="updateLinePurchasePrice({{ $index }})"
                                class="quote-lines-table__input quote-lines-table__input--number"
                                required
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="lines.{{ $index }}.sale_price_ht"
                                wire:change="updateLineSalePriceHt({{ $index }})"
                                class="quote-lines-table__input quote-lines-table__input--number"
                                required
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="lines.{{ $index }}.margin_amount_ht"
                                wire:change="updateLineMarginAmount({{ $index }})"
                                class="quote-lines-table__input quote-lines-table__input--number"
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.0001"
                                wire:model="lines.{{ $index }}.margin_rate"
                                wire:change="updateLineMarginRate({{ $index }})"
                                class="quote-lines-table__input quote-lines-table__input--number"
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.0001"
                                wire:model="lines.{{ $index }}.tva_rate"
                                class="quote-lines-table__input quote-lines-table__input--number"
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="lines.{{ $index }}.sale_price_ttc"
                                wire:change="updateLineSalePriceTtc({{ $index }})"
                                class="quote-lines-table__input quote-lines-table__input--number"
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <button
                                type="button"
                                wire:click="removeLine({{ $index }})"
                                class="quote-lines-table__btn-remove"
                                title="Supprimer la prestation"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button
                type="button"
                wire:click="addLine"
                class="quote-form__btn-add-line"
            >
                + Ajouter une ligne
            </button>
        </section>

        {{-- Section Totaux --}}
        <section class="quote-form__section">
            <h2 class="quote-form__section-title">Résumé</h2>

            <div class="quote-totals">
                <div class="quote-totals__row">
                    <span class="quote-totals__label">Total HT</span>
                    <span class="quote-totals__value">{{ number_format((float)$totals['total_ht'], 2, ',', ' ') }} €</span>
                </div>
                <div class="quote-totals__row">
                    <span class="quote-totals__label">TVA</span>
                    <span class="quote-totals__value">{{ number_format((float)$totals['total_tva'], 2, ',', ' ') }} €</span>
                </div>
                <div class="quote-totals__row quote-totals__row--total">
                    <span class="quote-totals__label">Total TTC</span>
                    <span class="quote-totals__value">{{ number_format((float)$totals['total_ttc'], 2, ',', ' ') }} €</span>
                </div>
                <div class="quote-totals__row">
                    <span class="quote-totals__label">Marge totale</span>
                    <span class="quote-totals__value">{{ number_format((float)$totals['margin_total_ht'], 2, ',', ' ') }} €</span>
                </div>
            </div>

            <div class="quote-form__discount">
                <div class="quote-form__field">
                    <label for="discountType" class="quote-form__label">Type remise</label>
                    <select
                        id="discountType"
                        wire:model.live="discountType"
                        class="quote-form__input"
                    >
                        <option value="amount">Montant (€)</option>
                        <option value="percent">Pourcentage (%)</option>
                    </select>
                </div>

                <div class="quote-form__field">
                    <label for="discountValue" class="quote-form__label">Remise</label>
                    <input
                        type="number"
                        step="0.01"
                        id="discountValue"
                        wire:model.live="discountValue"
                        class="quote-form__input"
                    >
                </div>

                <div class="quote-form__field">
                    <label for="validUntil" class="quote-form__label">Date de validité</label>
                    <input
                        type="date"
                        id="validUntil"
                        wire:model="validUntil"
                        class="quote-form__input"
                        required
                    >
                    @error('validUntil') <span class="quote-form__error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        {{-- Actions --}}
        <div class="quote-form__actions">
            <a href="{{ route('atelier.index') }}" class="quote-form__btn quote-form__btn--secondary">
                Annuler
            </a>
            <button
                type="button"
                wire:click="save(true)"
                class="quote-form__btn quote-form__btn--secondary"
            >
                Enregistrer et continuer
            </button>
            <button
                type="submit"
                class="quote-form__btn quote-form__btn--primary"
            >
                Enregistrer le devis
            </button>
        </div>
    </form>
</div>

<script>
    function showTab(tab) {
        document.querySelectorAll('.quote-form__tab-content').forEach(el => el.style.display = 'none');
        document.getElementById('tab-' + tab).style.display = 'block';
    }
</script>
