<?php

namespace App\Livewire\Atelier\Quotes;

use App\Enums\QuoteStatus;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Services\Quotes\QuoteCalculator;
use Livewire\Attributes\On;
use Livewire\Component;

class Form extends Component
{
    public ?int $quoteId = null;

    public QuoteStatus $status = QuoteStatus::Draft;

    public ?int $selectedClientId = null;

    public string $clientPrenom = '';

    public string $clientNom = '';

    public string $clientEmail = '';

    public string $clientTelephone = '';

    public string $clientAdresse = '';

    public string $validUntil = '';

    public string $discountType = 'percent';

    public string $discountValue = '0';

    public array $lines = [];

    public array $totals = [
        'total_ht' => '0.00',
        'total_tva' => '0.00',
        'total_ttc' => '0.00',
        'margin_total_ht' => '0.00',
    ];

    public function mount(?int $quoteId = null): void
    {
        $this->quoteId = $quoteId;
        $this->validUntil = now()->addDays(15)->format('Y-m-d');

        if ($quoteId) {
            $this->loadQuote($quoteId);
        } else {
            $this->addLine();
        }
    }

    #[On('clientSelected')]
    public function handleClientSelected(array $clientData): void
    {
        $this->selectedClientId = $clientData['id'];
        $this->clientPrenom = $clientData['prenom'] ?? '';
        $this->clientNom = $clientData['nom'] ?? '';
        $this->clientEmail = $clientData['email'] ?? '';
        $this->clientTelephone = $clientData['telephone'] ?? '';
        $this->clientAdresse = $clientData['adresse'] ?? '';
    }

    public function addLine(): void
    {
        // Bloquer si le devis n'est pas éditable
        if (! $this->status->canEdit()) {
            return;
        }

        // En mode modifiable, les nouvelles lignes ont purchase_price_ht = null
        $purchasePriceHt = $this->status->canShowPurchasePrice() ? '0.00' : null;

        $this->lines[] = [
            'id' => null,
            'title' => '',
            'reference' => '',
            'purchase_price_ht' => $purchasePriceHt,
            'sale_price_ht' => '0.00',
            'sale_price_ttc' => '0.00',
            'margin_amount_ht' => $purchasePriceHt === null ? null : '0.00',
            'margin_rate' => $purchasePriceHt === null ? null : '0.0000',
            'tva_rate' => '20.0000',
            'position' => count($this->lines),
        ];
    }

    public function removeLine(int $index): void
    {
        // Bloquer si le devis n'est pas éditable
        if (! $this->status->canEdit()) {
            return;
        }

        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        $this->recalculateTotals();
    }

    public function updateLineSalePriceHt(int $index): void
    {
        $line = &$this->lines[$index];
        $calculator = new QuoteCalculator;

        $calculated = $calculator->fromSalePriceHt(
            $line['purchase_price_ht'],
            $line['sale_price_ht'],
            $line['tva_rate']
        );

        $line['sale_price_ht'] = number_format((float) $calculated['sale_price_ht'], 2, '.', '');
        $line['sale_price_ttc'] = number_format((float) $calculated['sale_price_ttc'], 2, '.', '');
        $line['margin_amount_ht'] = $calculated['margin_amount_ht'] !== null ? number_format((float) $calculated['margin_amount_ht'], 2, '.', '') : null;
        $line['margin_rate'] = $calculated['margin_rate'] !== null ? number_format((float) $calculated['margin_rate'], 4, '.', '') : null;

        $this->recalculateTotals();
    }

    public function updateLineSalePriceTtc(int $index): void
    {
        $line = &$this->lines[$index];
        $calculator = new QuoteCalculator;

        $calculated = $calculator->fromSalePriceTtc(
            $line['purchase_price_ht'],
            $line['sale_price_ttc'],
            $line['tva_rate']
        );

        $line['sale_price_ht'] = number_format((float) $calculated['sale_price_ht'], 2, '.', '');
        $line['sale_price_ttc'] = number_format((float) $calculated['sale_price_ttc'], 2, '.', '');
        $line['margin_amount_ht'] = $calculated['margin_amount_ht'] !== null ? number_format((float) $calculated['margin_amount_ht'], 2, '.', '') : null;
        $line['margin_rate'] = $calculated['margin_rate'] !== null ? number_format((float) $calculated['margin_rate'], 4, '.', '') : null;

        $this->recalculateTotals();
    }

    public function updateLineMarginAmount(int $index): void
    {
        $line = &$this->lines[$index];
        $calculator = new QuoteCalculator;

        $calculated = $calculator->fromMarginAmount(
            $line['purchase_price_ht'],
            $line['margin_amount_ht'],
            $line['tva_rate']
        );

        $line['sale_price_ht'] = number_format((float) $calculated['sale_price_ht'], 2, '.', '');
        $line['sale_price_ttc'] = number_format((float) $calculated['sale_price_ttc'], 2, '.', '');
        $line['margin_amount_ht'] = $calculated['margin_amount_ht'] !== null ? number_format((float) $calculated['margin_amount_ht'], 2, '.', '') : null;
        $line['margin_rate'] = $calculated['margin_rate'] !== null ? number_format((float) $calculated['margin_rate'], 4, '.', '') : null;

        $this->recalculateTotals();
    }

    public function updateLineMarginRate(int $index): void
    {
        $line = &$this->lines[$index];
        $calculator = new QuoteCalculator;

        $calculated = $calculator->fromMarginRate(
            $line['purchase_price_ht'],
            $line['margin_rate'],
            $line['tva_rate']
        );

        $line['sale_price_ht'] = number_format((float) $calculated['sale_price_ht'], 2, '.', '');
        $line['sale_price_ttc'] = number_format((float) $calculated['sale_price_ttc'], 2, '.', '');
        $line['margin_amount_ht'] = $calculated['margin_amount_ht'] !== null ? number_format((float) $calculated['margin_amount_ht'], 2, '.', '') : null;
        $line['margin_rate'] = $calculated['margin_rate'] !== null ? number_format((float) $calculated['margin_rate'], 4, '.', '') : null;

        $this->recalculateTotals();
    }

    public function updateLinePurchasePrice(int $index): void
    {
        $this->updateLineSalePriceHt($index);
    }

    public function recalculateTotals(): void
    {
        $calculator = new QuoteCalculator;
        $totals = $calculator->aggregateTotals($this->lines);

        $this->totals['total_ht'] = number_format((float) $totals['total_ht'], 2, '.', '');
        $this->totals['total_tva'] = number_format((float) $totals['total_tva'], 2, '.', '');
        $this->totals['total_ttc'] = number_format((float) $totals['total_ttc'], 2, '.', '');
        $this->totals['margin_total_ht'] = number_format((float) $totals['margin_total_ht'], 2, '.', '');

        if ($this->discountValue && $this->discountValue !== '0') {
            $discounted = $calculator->applyDiscount(
                $this->totals['total_ht'],
                $this->totals['total_tva'],
                $this->discountType,
                $this->discountValue
            );

            $this->totals['total_ht'] = number_format((float) $discounted['total_ht'], 2, '.', '');
            $this->totals['total_tva'] = number_format((float) $discounted['total_tva'], 2, '.', '');
            $this->totals['total_ttc'] = number_format((float) $discounted['total_ttc'], 2, '.', '');
        }
    }

    public function updatedDiscountValue(): void
    {
        $this->recalculateTotals();
    }

    public function updatedDiscountType(): void
    {
        $this->recalculateTotals();
    }

    public function save(bool $stayOnPage = false): void
    {
        // Bloquer la sauvegarde si le devis est facturé
        if ($this->quoteId && $this->status === QuoteStatus::Invoiced) {
            session()->flash('error', 'Impossible de modifier un devis facturé.');

            return;
        }

        // Bloquer la sauvegarde si le devis est en mode "prêt" (lecture seule)
        if ($this->quoteId && $this->status === QuoteStatus::Ready) {
            session()->flash('error', 'Le devis est en mode lecture seule. Changez le statut pour le modifier.');

            return;
        }

        // Adapter les règles de validation selon le statut
        $purchasePriceRule = $this->status->canShowPurchasePrice()
            ? 'required|numeric|min:0'
            : 'nullable|numeric|min:0';

        $this->validate([
            'clientPrenom' => 'required|string|max:255',
            'clientNom' => 'required|string|max:255',
            'clientEmail' => 'nullable|email|max:255',
            'clientTelephone' => 'nullable|string|max:20',
            'validUntil' => 'required|date',
            'lines.*.title' => 'required|string|max:255',
            'lines.*.purchase_price_ht' => $purchasePriceRule,
            'lines.*.sale_price_ht' => 'required|numeric|min:0',
        ]);

        // Update or create client
        if ($this->selectedClientId) {
            $client = Client::findOrFail($this->selectedClientId);

            // Vérifier si les données client ont été modifiées
            $currentClientData = [
                'prenom' => $this->clientPrenom,
                'nom' => $this->clientNom,
                'email' => $this->clientEmail,
                'telephone' => $this->clientTelephone,
                'adresse' => $this->clientAdresse,
            ];

            if ($this->hasClientDataChanged($currentClientData)) {
                $client->update($currentClientData);
            }
        } else {
            $client = Client::create([
                'prenom' => $this->clientPrenom,
                'nom' => $this->clientNom,
                'email' => $this->clientEmail,
                'telephone' => $this->clientTelephone,
                'adresse' => $this->clientAdresse,
            ]);
            $this->selectedClientId = $client->id;
        }

        // Create or update quote
        if ($this->quoteId) {
            // Mode édition : mise à jour sans changer la référence ni le statut
            $quote = Quote::findOrFail($this->quoteId);
            $quote->update([
                'client_id' => $client->id,
                'valid_until' => $this->validUntil,
                'discount_type' => $this->discountValue ? $this->discountType : null,
                'discount_value' => $this->discountValue ?: null,
                'total_ht' => $this->totals['total_ht'],
                'total_tva' => $this->totals['total_tva'],
                'total_ttc' => $this->totals['total_ttc'],
                'margin_total_ht' => $this->totals['margin_total_ht'],
            ]);
        } else {
            // Mode création : génération de la référence, status par défaut brouillon
            $quote = Quote::create([
                'client_id' => $client->id,
                'reference' => $this->generateReference(),
                'status' => QuoteStatus::Draft,
                'valid_until' => $this->validUntil,
                'discount_type' => $this->discountValue ? $this->discountType : null,
                'discount_value' => $this->discountValue ?: null,
                'total_ht' => $this->totals['total_ht'],
                'total_tva' => $this->totals['total_tva'],
                'total_ttc' => $this->totals['total_ttc'],
                'margin_total_ht' => $this->totals['margin_total_ht'],
            ]);
            $this->quoteId = $quote->id;
        }

        // Sync lines
        $quote->lines()->delete();
        foreach ($this->lines as $index => $lineData) {
            QuoteLine::create([
                'quote_id' => $quote->id,
                'title' => $lineData['title'],
                'reference' => $lineData['reference'],
                'purchase_price_ht' => $lineData['purchase_price_ht'],
                'sale_price_ht' => $lineData['sale_price_ht'],
                'sale_price_ttc' => $lineData['sale_price_ttc'],
                'margin_amount_ht' => $lineData['margin_amount_ht'],
                'margin_rate' => $lineData['margin_rate'],
                'tva_rate' => $lineData['tva_rate'],
                'position' => $index,
            ]);
        }

        session()->flash('message', 'Devis enregistré avec succès.');

        if (! $stayOnPage) {
            $this->redirect(route('atelier.quotes.show', $quote), navigate: true);
        }
    }

    protected function loadQuote(int $quoteId): void
    {
        $quote = Quote::with('lines', 'client')->findOrFail($quoteId);

        $this->status = $quote->status;
        $this->selectedClientId = $quote->client_id;
        $this->clientPrenom = $quote->client->prenom;
        $this->clientNom = $quote->client->nom;
        $this->clientEmail = $quote->client->email ?? '';
        $this->clientTelephone = $quote->client->telephone ?? '';
        $this->clientAdresse = $quote->client->adresse ?? '';
        $this->validUntil = $quote->valid_until->format('Y-m-d');
        $this->discountType = $quote->discount_type ?? 'percent';
        $this->discountValue = $quote->discount_value ?? '0';

        $this->lines = $quote->lines->map(fn (QuoteLine $line) => [
            'id' => $line->id,
            'title' => $line->title,
            'reference' => $line->reference ?? '',
            'purchase_price_ht' => $line->purchase_price_ht !== null ? number_format((float) $line->purchase_price_ht, 2, '.', '') : null,
            'sale_price_ht' => number_format((float) $line->sale_price_ht, 2, '.', ''),
            'sale_price_ttc' => number_format((float) $line->sale_price_ttc, 2, '.', ''),
            'margin_amount_ht' => $line->margin_amount_ht !== null ? number_format((float) $line->margin_amount_ht, 2, '.', '') : null,
            'margin_rate' => $line->margin_rate !== null ? number_format((float) $line->margin_rate, 4, '.', '') : null,
            'tva_rate' => number_format((float) $line->tva_rate, 4, '.', ''),
            'position' => $line->position,
        ])->toArray();

        $this->recalculateTotals();
    }

    protected function hasClientDataChanged(array $currentData): bool
    {
        // Si pas de client sélectionné, pas de comparaison possible
        if (! $this->selectedClientId) {
            return false;
        }

        // Charger le client actuel depuis la base de données
        $client = Client::find($this->selectedClientId);

        if (! $client) {
            return false;
        }

        // Comparer avec les données actuelles du client en base
        $databaseData = [
            'prenom' => $client->prenom,
            'nom' => $client->nom,
            'email' => $client->email ?? '',
            'telephone' => $client->telephone ?? '',
            'adresse' => $client->adresse ?? '',
        ];

        // Comparer chaque champ
        foreach ($currentData as $key => $value) {
            $dbValue = $databaseData[$key] ?? '';
            $currentValue = $value ?? '';

            if ($currentValue !== $dbValue) {
                return true;
            }
        }

        return false;
    }

    protected function generateReference(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastQuote = Quote::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest('id')
            ->first();

        $number = $lastQuote ? ((int) substr($lastQuote->reference, -4)) + 1 : 1;

        return sprintf('DEV-%s%s-%04d', $year, $month, $number);
    }

    public function changeStatus(string $newStatus): void
    {
        if (! $this->quoteId) {
            session()->flash('error', 'Vous devez d\'abord enregistrer le devis.');

            return;
        }

        $quote = Quote::findOrFail($this->quoteId);
        $newStatusEnum = QuoteStatus::from($newStatus);

        try {
            // Vérifier si la transition est autorisée
            if (! $quote->status->canTransitionTo($newStatusEnum)) {
                session()->flash('error', "Impossible de passer au statut '{$newStatusEnum->label()}' depuis '{$quote->status->label()}'");

                return;
            }

            // Validation spécifique pour le statut facturé
            if ($newStatusEnum === QuoteStatus::Invoiced && ! $quote->canBeInvoiced()) {
                session()->flash('error', "Impossible de facturer : {$quote->getIncompleteLinesCount()} ligne(s) sans prix d'achat. Passez en brouillon pour les compléter.");

                return;
            }

            $quote->update(['status' => $newStatusEnum]);
            $this->status = $newStatusEnum;

            session()->flash('message', "Devis marqué comme '{$newStatusEnum->label()}'");
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.atelier.quotes.form');
    }
}
