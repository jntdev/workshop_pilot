<div class="quote-form">
    @php
        $isEditable = $status->canEdit();
        $isReadOnly = !$isEditable;
    @endphp

    @if (session()->has('message'))
        <div class="quote-form__alert quote-form__alert--success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="quote-form__alert quote-form__alert--error">
            {{ session('error') }}
        </div>
    @endif

    {{-- En-tête avec statut --}}
    @if($quoteId)
        <div class="quote-form__header">
            <div class="quote-form__status-section">
                <span class="quote-form__status-badge quote-form__status-badge--{{ $status->value }}">
                    {{ $status->label() }}
                </span>

                <div class="quote-form__status-selector">
                    <label for="status-select" class="quote-form__label">Changer le statut :</label>
                    <select
                        id="status-select"
                        wire:change="changeStatus($event.target.value)"
                        class="quote-form__select"
                        {{ $status === \App\Enums\QuoteStatus::Invoiced ? 'disabled' : '' }}
                    >
                        <option value="{{ \App\Enums\QuoteStatus::Draft->value }}" {{ $status === \App\Enums\QuoteStatus::Draft ? 'selected' : '' }}>
                            Brouillon
                        </option>
                        <option value="{{ \App\Enums\QuoteStatus::Ready->value }}" {{ $status === \App\Enums\QuoteStatus::Ready ? 'selected' : '' }}>
                            Prêt
                        </option>
                        <option value="{{ \App\Enums\QuoteStatus::Editable->value }}" {{ $status === \App\Enums\QuoteStatus::Editable ? 'selected' : '' }}>
                            Modifiable
                        </option>
                        <option value="{{ \App\Enums\QuoteStatus::Invoiced->value }}" {{ $status === \App\Enums\QuoteStatus::Invoiced ? 'selected' : '' }}>
                            Facturé
                        </option>
                    </select>
                </div>
            </div>

            @if($status === \App\Enums\QuoteStatus::Editable)
                <div class="quote-form__info-banner quote-form__info-banner--warning">
                    ⚠️ Mode modifiable : Les prix d'achat et marges sont masqués. Nouvelles lignes créées sans prix d'achat.
                </div>
            @endif
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
                            {{ $isReadOnly ? 'readonly' : '' }}
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
                            {{ $isReadOnly ? 'readonly' : '' }}
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
                            {{ $isReadOnly ? 'readonly' : '' }}
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
                            {{ $isReadOnly ? 'readonly' : '' }}
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
                            {{ $isReadOnly ? 'readonly' : '' }}
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
                    @if($status->canShowPurchasePrice())
                        <div class="quote-lines-table__cell">PA HT</div>
                    @endif
                    <div class="quote-lines-table__cell">PV HT</div>
                    @if($status->showMargins())
                        <div class="quote-lines-table__cell">Marge €</div>
                        <div class="quote-lines-table__cell">Marge %</div>
                    @endif
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
                                {{ $isReadOnly ? 'readonly' : '' }}
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="text"
                                wire:model="lines.{{ $index }}.reference"
                                class="quote-lines-table__input quote-lines-table__input--narrow"
                                placeholder="Réf"
                                {{ $isReadOnly ? 'readonly' : '' }}
                            >
                        </div>

                        @if($status->canShowPurchasePrice())
                            <div class="quote-lines-table__cell">
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="lines.{{ $index }}.purchase_price_ht"
                                    wire:change="updateLinePurchasePrice({{ $index }})"
                                    class="quote-lines-table__input quote-lines-table__input--number"
                                    {{ $line['purchase_price_ht'] === null ? 'placeholder=À définir' : '' }}
                                >
                                @if($line['purchase_price_ht'] === null)
                                    <span class="quote-lines-table__badge quote-lines-table__badge--warning">
                                        ⚠️ À compléter
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="lines.{{ $index }}.sale_price_ht"
                                wire:change="updateLineSalePriceHt({{ $index }})"
                                class="quote-lines-table__input quote-lines-table__input--number"
                                required
                                {{ $isReadOnly ? 'readonly' : '' }}
                            >
                        </div>

                        @if($status->showMargins())
                            <div class="quote-lines-table__cell">
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="lines.{{ $index }}.margin_amount_ht"
                                    wire:change="updateLineMarginAmount({{ $index }})"
                                    class="quote-lines-table__input quote-lines-table__input--number"
                                    {{ $line['margin_amount_ht'] === null ? 'placeholder=-' : '' }}
                                    {{ $line['margin_amount_ht'] === null || $isReadOnly ? 'disabled' : '' }}
                                >
                            </div>
                            <div class="quote-lines-table__cell">
                                <input
                                    type="number"
                                    step="0.0001"
                                    wire:model="lines.{{ $index }}.margin_rate"
                                    wire:change="updateLineMarginRate({{ $index }})"
                                    class="quote-lines-table__input quote-lines-table__input--number"
                                    {{ $line['margin_rate'] === null ? 'placeholder=-' : '' }}
                                    {{ $line['margin_rate'] === null || $isReadOnly ? 'disabled' : '' }}
                                >
                            </div>
                        @endif
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.0001"
                                wire:model="lines.{{ $index }}.tva_rate"
                                class="quote-lines-table__input quote-lines-table__input--number"
                                {{ $isReadOnly ? 'readonly' : '' }}
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="lines.{{ $index }}.sale_price_ttc"
                                wire:change="updateLineSalePriceTtc({{ $index }})"
                                class="quote-lines-table__input quote-lines-table__input--number"
                                {{ $isReadOnly ? 'readonly' : '' }}
                            >
                        </div>
                        <div class="quote-lines-table__cell">
                            <button
                                type="button"
                                wire:click="removeLine({{ $index }})"
                                class="quote-lines-table__btn-remove"
                                title="Supprimer la prestation"
                                {{ $isReadOnly ? 'disabled' : '' }}
                            >
                                ×
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($isEditable)
                <button
                    type="button"
                    wire:click="addLine"
                    class="quote-form__btn-add-line"
                >
                    + Ajouter une ligne
                </button>
            @endif
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
                        {{ $isReadOnly ? 'disabled' : '' }}
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
                        {{ $isReadOnly ? 'readonly' : '' }}
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
                        {{ $isReadOnly ? 'readonly' : '' }}
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
            @if($isEditable)
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
            @endif
        </div>
    </form>
</div>

<script>
    function showTab(tab) {
        document.querySelectorAll('.quote-form__tab-content').forEach(el => el.style.display = 'none');
        document.getElementById('tab-' + tab).style.display = 'block';
    }
</script>
