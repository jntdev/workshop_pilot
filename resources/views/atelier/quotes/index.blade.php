<x-layouts.main>
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('atelier.index') }}">Atelier</a>
            <span>&gt;</span>
            <span>Devis</span>
        </div>
        <h1>Devis</h1>
    </div>

    <div class="quotes-list">
        <div class="quotes-list__header">
            <a href="{{ route('atelier.quotes.create') }}" class="quotes-list__btn-create">
                + Nouveau devis
            </a>
        </div>

        @if($quotes->count() > 0)
            <table class="quotes-list__table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Total TTC</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotes as $quote)
                        <tr>
                            <td>{{ $quote->reference }}</td>
                            <td>{{ $quote->client->prenom }} {{ $quote->client->nom }}</td>
                            <td>{{ number_format((float)$quote->total_ttc, 2, ',', ' ') }} €</td>
                            <td>
                                <span class="quotes-list__status quotes-list__status--{{ $quote->status }}">
                                    {{ $quote->status === 'draft' ? 'Brouillon' : 'Validé' }}
                                </span>
                            </td>
                            <td>{{ $quote->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('atelier.quotes.show', $quote) }}" class="quotes-list__link">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="quotes-list__empty">
                <p>Aucun devis pour le moment.</p>
                <a href="{{ route('atelier.quotes.create') }}" class="quotes-list__btn-create">
                    Créer le premier devis
                </a>
            </div>
        @endif
    </div>
</x-layouts.main>
