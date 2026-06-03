import { useState, useCallback } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import DashboardMetrics from '@/Components/Atelier/DashboardMetrics';
import QuotesTabs from '@/Components/Atelier/QuotesTabs';
import { AtelierPageProps, Quote } from '@/types';

interface Props extends AtelierPageProps {}

export default function AtelierIndex({
    stats,
    comparisonStats,
    selectedYear,
    selectedMonth,
    availableYears,
    quotes,
    archivedQuotes,
    invoices: initialInvoices,
}: Props) {
    const [currentYear, setCurrentYear] = useState(selectedYear);
    const [currentMonth, setCurrentMonth] = useState(selectedMonth);
    const [currentStats, setCurrentStats] = useState(stats);
    const [currentComparisonStats, setCurrentComparisonStats] = useState(comparisonStats);
    const [invoices, setInvoices] = useState<Quote[]>(initialInvoices);
    const [invoicesLoaded, setInvoicesLoaded] = useState(false);
    const [isRebuilding, setIsRebuilding] = useState(false);
    const [activeTab, setActiveTab] = useState<'quotes' | 'invoices' | 'clients' | 'archives'>('quotes');

    const loadInvoices = useCallback(async (year: number, month: number) => {
        try {
            const response = await fetch(`/api/atelier/invoices?year=${year}&month=${month}`);
            const data = await response.json();
            setInvoices(data);
            setInvoicesLoaded(true);
        } catch (error) {
            console.error('Failed to load invoices:', error);
        }
    }, []);

    const handleYearChange = useCallback(async (year: number) => {
        setCurrentYear(year);
        try {
            const response = await fetch(`/api/atelier/stats?year=${year}&month=${currentMonth}`);
            const data = await response.json();
            setCurrentStats(data.stats);
            setCurrentComparisonStats(data.comparisonStats);
            setInvoicesLoaded(false);
            setInvoices([]);
            if (activeTab === 'invoices') {
                await loadInvoices(year, currentMonth);
            }
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    }, [currentMonth, activeTab, loadInvoices]);

    const handleMonthChange = useCallback(async (month: number) => {
        setCurrentMonth(month);
        try {
            const response = await fetch(`/api/atelier/stats?year=${currentYear}&month=${month}`);
            const data = await response.json();
            setCurrentStats(data.stats);
            setCurrentComparisonStats(data.comparisonStats);
            setInvoicesLoaded(false);
            setInvoices([]);
            if (activeTab === 'invoices') {
                await loadInvoices(currentYear, month);
            }
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    }, [currentYear, activeTab, loadInvoices]);

    const handleLoadInvoices = useCallback(async () => {
        if (invoicesLoaded) return;
        await loadInvoices(currentYear, currentMonth);
    }, [currentYear, currentMonth, invoicesLoaded, loadInvoices]);

    const handleRebuildStats = useCallback(async () => {
        setIsRebuilding(true);
        try {
            const response = await fetch('/api/atelier/stats/rebuild', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            if (response.ok) {
                // Reload stats after rebuild
                const statsResponse = await fetch(`/api/atelier/stats?year=${currentYear}&month=${currentMonth}`);
                const data = await statsResponse.json();
                setCurrentStats(data.stats);
                setCurrentComparisonStats(data.comparisonStats);
                // Reset invoices to force reload
                setInvoicesLoaded(false);
                setInvoices([]);
            }
        } catch (error) {
            console.error('Failed to rebuild stats:', error);
        } finally {
            setIsRebuilding(false);
        }
    }, [currentYear, currentMonth]);

    return (
        <MainLayout>
            <Head title="Atelier" />

            <div className="page-header">
                <h1>Atelier</h1>
            </div>

            <div className="atelier-index">
                <div className="atelier-index__dashboard">
                    <DashboardMetrics
                        stats={currentStats}
                        comparisonStats={currentComparisonStats}
                        selectedYear={currentYear}
                        selectedMonth={currentMonth}
                        availableYears={availableYears}
                        onYearChange={handleYearChange}
                        onMonthChange={handleMonthChange}
                        onRebuildStats={handleRebuildStats}
                        isRebuilding={isRebuilding}
                    />
                </div>

                <div className="atelier-index__actions">
                    <Link
                        href="/atelier/devis/nouveau"
                        className="atelier-index__btn atelier-index__btn--primary"
                    >
                        Nouveau devis
                    </Link>
                    <Link
                        href="/atelier/pieces-a-commander"
                        className="atelier-index__btn atelier-index__btn--secondary"
                    >
                        Pièces à commander
                    </Link>
                </div>

                <div className="atelier-index__quotes">
                    <QuotesTabs
                        quotes={quotes}
                        archivedQuotes={archivedQuotes}
                        invoices={invoices}
                        onLoadInvoices={handleLoadInvoices}
                        invoicesLoaded={invoicesLoaded}
                        activeTab={activeTab}
                        onTabChange={setActiveTab}
                    />
                </div>
            </div>
        </MainLayout>
    );
}
