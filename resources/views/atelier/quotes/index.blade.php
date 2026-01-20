<x-layouts.main>
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('atelier.index') }}">Atelier</a>
            <span>&gt;</span>
            <span>Devis</span>
        </div>
        <h1>Devis</h1>
    </div>

    @if(session('message'))
        <div class="alert alert--success">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert--error">
            {{ session('error') }}
        </div>
    @endif

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
                        <th>Type</th>
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
                            <td>{{ $quote->created_at->format('d/m/Y') }}</td>
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
                                    <form action="{{ route('atelier.quotes.destroy', $quote) }}" method="POST" class="quotes-list__delete-form" onsubmit="return confirm('Voulez-vous vraiment supprimer ce devis ?')">
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
                <a href="{{ route('atelier.quotes.create') }}" class="quotes-list__btn-create">
                    Créer le premier devis
                </a>
            </div>
        @endif
    </div>
</x-layouts.main>
