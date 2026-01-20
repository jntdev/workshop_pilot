<?php

namespace App\Livewire\Atelier;

use App\Models\Quote;
use Livewire\Attributes\On;
use Livewire\Component;

class QuotesList extends Component
{
    public string $activeTab = 'quotes';

    public int $filterYear;

    public int $filterMonth;

    public string $clientSearch = '';

    public function mount(): void
    {
        $this->filterYear = now()->year;
        $this->filterMonth = now()->month;
    }

    #[On('dashboard-filter-changed')]
    public function updateFilters(int $year, int $month): void
    {
        $this->filterYear = $year;
        $this->filterMonth = $month;
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getQuotesProperty()
    {
        return Quote::with('client')
            ->whereNull('invoiced_at')
            ->latest()
            ->get();
    }

    public function getInvoicesProperty()
    {
        $startDate = now()->setYear($this->filterYear)->setMonth($this->filterMonth)->startOfMonth();
        $endDate = now()->setYear($this->filterYear)->setMonth($this->filterMonth)->endOfMonth();

        return Quote::with('client')
            ->whereNotNull('invoiced_at')
            ->whereBetween('invoiced_at', [$startDate, $endDate])
            ->latest('invoiced_at')
            ->get();
    }

    public function getClientQuotesProperty()
    {
        $query = Quote::with('client')->latest();

        if ($this->clientSearch) {
            $query->whereHas('client', function ($q) {
                $q->where('prenom', 'like', '%' . $this->clientSearch . '%')
                    ->orWhere('nom', 'like', '%' . $this->clientSearch . '%')
                    ->orWhere('email', 'like', '%' . $this->clientSearch . '%');
            });
        }

        return $query->get()->groupBy('client_id');
    }

    public function render()
    {
        return view('livewire.atelier.quotes-list');
    }
}
