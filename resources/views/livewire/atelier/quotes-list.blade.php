<div class="quotes-list-container">
    <style>
    .quotes-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }

    .quotes-tab {
        padding: 12px 24px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #6c757d;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }

    .quotes-tab:hover {
        color: #2196F3;
    }

    .quotes-tab--active {
        color: #2196F3;
        border-bottom-color: #2196F3;
    }

    .quotes-tab-content {
        display: none;
    }

    .quotes-tab-content--active {
        display: block;
    }
    </style>

    <div class="quotes-tabs">
        <button
            type="button"
            wire:click="setActiveTab('quotes')"
            class="quotes-tab {{ $activeTab === 'quotes' ? 'quotes-tab--active' : '' }}"
        >
            Devis
        </button>
        <button
            type="button"
            wire:click="setActiveTab('invoices')"
            class="quotes-tab {{ $activeTab === 'invoices' ? 'quotes-tab--active' : '' }}"
        >
            Factures
        </button>
        <button
            type="button"
            wire:click="setActiveTab('clients')"
            class="quotes-tab {{ $activeTab === 'clients' ? 'quotes-tab--active' : '' }}"
        >
            Clients
        </button>
    </div>

    {{-- Onglet Devis --}}
    <div class="quotes-tab-content {{ $activeTab === 'quotes' ? 'quotes-tab-content--active' : '' }}">
        @if($this->quotes->count() > 0)
            <table class="quotes-list__table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Vélo</th>
                        <th>Total TTC</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->quotes as $quote)
                        <tr>
                            <td>{{ $quote->reference }}</td>
                            <td>{{ $quote->client->prenom }} {{ $quote->client->nom }}</td>
                            <td>{{ $quote->bike_description ?: '-' }}</td>
                            <td>{{ number_format((float)$quote->total_ttc, 2, ',', ' ') }} €</td>
                            <td>{{ $quote->created_at->format('d/m/Y') }}</td>
                            <td class="quotes-list__actions">
                                <a href="{{ route('atelier.quotes.edit', $quote) }}" class="quotes-list__link">
                                    Consulter
                                </a>
                                @if($quote->canDelete())
                                    <form action="{{ route('atelier.quotes.destroy', $quote) }}" method="POST" class="quotes-list__delete-form" onsubmit="return confirm('Voulez-vous vraiment supprimer ce devis ?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="quotes-list__link quotes-list__link--danger">
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="quotes-list__empty">
                <p>Aucun devis pour le moment.</p>
            </div>
        @endif
    </div>

    {{-- Onglet Factures --}}
    <div class="quotes-tab-content {{ $activeTab === 'invoices' ? 'quotes-tab-content--active' : '' }}">
        @if($this->invoices->count() > 0)
            <table class="quotes-list__table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Vélo</th>
                        <th>Total TTC</th>
                        <th>Date de facturation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->reference }}</td>
                            <td>{{ $invoice->client->prenom }} {{ $invoice->client->nom }}</td>
                            <td>{{ $invoice->bike_description ?: '-' }}</td>
                            <td>{{ number_format((float)$invoice->total_ttc, 2, ',', ' ') }} €</td>
                            <td>{{ $invoice->invoiced_at->format('d/m/Y') }}</td>
                            <td class="quotes-list__actions">
                                <a href="{{ route('atelier.quotes.show', $invoice) }}" class="quotes-list__link">
                                    Consulter
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="quotes-list__empty">
                <p>Aucune facture pour cette période.</p>
            </div>
        @endif
    </div>

    {{-- Onglet Clients --}}
    <div class="quotes-tab-content {{ $activeTab === 'clients' ? 'quotes-tab-content--active' : '' }}">
        <div style="margin-bottom: 20px;">
            <input
                type="text"
                wire:model.live.debounce.300ms="clientSearch"
                placeholder="Rechercher un client (prénom, nom, email)..."
                style="width: 100%; max-width: 400px; padding: 10px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px;"
            >
            @if(strlen($clientSearch) > 0 && strlen($clientSearch) < 2)
                <p style="margin-top: 8px; font-size: 13px; color: #6c757d;">
                    Saisissez au moins 2 caractères pour lancer la recherche.
                </p>
            @endif
        </div>

        @if($this->clientQuotes->count() > 0)
            @foreach($this->clientQuotes as $clientId => $quotes)
                @php
                    $client = $quotes->first()->client;
                    $totalQuotes = $quotes->where('invoiced_at', null)->count();
                    $totalInvoices = $quotes->where('invoiced_at', '!=', null)->count();
                @endphp
                <div style="margin-bottom: 30px; border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; background: white;">
                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #212529;">
                        {{ $client->prenom }} {{ $client->nom }}
                        <span style="font-size: 14px; color: #6c757d; font-weight: normal;">
                            ({{ $totalQuotes }} devis, {{ $totalInvoices }} facture{{ $totalInvoices > 1 ? 's' : '' }})
                        </span>
                    </h3>

                    @if($client->email || $client->telephone)
                        <p style="margin: 0 0 15px 0; font-size: 14px; color: #6c757d;">
                            @if($client->email)
                                <span>{{ $client->email }}</span>
                            @endif
                            @if($client->email && $client->telephone)
                                <span> • </span>
                            @endif
                            @if($client->telephone)
                                <span>{{ $client->telephone }}</span>
                            @endif
                        </p>
                    @endif

                    <table class="quotes-list__table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Vélo</th>
                                <th>Type</th>
                                <th>Total TTC</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes->sortByDesc('created_at') as $quote)
                                <tr>
                                    <td>{{ $quote->reference }}</td>
                                    <td>{{ $quote->bike_description ?: '-' }}</td>
                                    <td>
                                        @if($quote->isInvoice())
                                            <span class="quotes-list__status quotes-list__status--invoice">
                                                Facture
                                            </span>
                                        @else
                                            <span class="quotes-list__status quotes-list__status--quote">
                                                Devis
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ number_format((float)$quote->total_ttc, 2, ',', ' ') }} €</td>
                                    <td>
                                        @if($quote->isInvoice())
                                            {{ $quote->invoiced_at->format('d/m/Y') }}
                                        @else
                                            {{ $quote->created_at->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="quotes-list__actions">
                                        @if($quote->isInvoice())
                                            <a href="{{ route('atelier.quotes.show', $quote) }}" class="quotes-list__link">
                                                Consulter
                                            </a>
                                        @else
                                            <a href="{{ route('atelier.quotes.edit', $quote) }}" class="quotes-list__link">
                                                Consulter
                                            </a>
                                        @endif
                                        @if($quote->canDelete())
                                            <form action="{{ route('atelier.quotes.destroy', $quote) }}" method="POST" class="quotes-list__delete-form" onsubmit="return confirm('Voulez-vous vraiment supprimer ce devis ?')" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="quotes-list__link quotes-list__link--danger">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @elseif(strlen($clientSearch) >= 2)
            <div class="quotes-list__empty">
                <p>Aucun client trouvé pour "{{ $clientSearch }}".</p>
            </div>
        @else
            <div class="quotes-list__empty">
                <p>Utilisez la recherche pour trouver un client.</p>
            </div>
        @endif
    </div>
</div>
