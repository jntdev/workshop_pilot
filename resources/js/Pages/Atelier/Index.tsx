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
    invoices: initialInvoices,
}: Props) {
    const [currentYear, setCurrentYear] = useState(selectedYear);
    const [currentMonth, setCurrentMonth] = useState(selectedMonth);
    const [currentStats, setCurrentStats] = useState(stats);
    const [currentComparisonStats, setCurrentComparisonStats] = useState(comparisonStats);
    const [invoices, setInvoices] = useState<Quote[]>(initialInvoices);
    const [invoicesLoaded, setInvoicesLoaded] = useState(false);

    const handleYearChange = useCallback(async (year: number) => {
        setCurrentYear(year);
        // Reload stats from server
        try {
            const response = await fetch(`/api/atelier/stats?year=${year}&month=${currentMonth}`);
            const data = await response.json();
            setCurrentStats(data.stats);
            setCurrentComparisonStats(data.comparisonStats);
            // Reset invoices when filter changes
            setInvoicesLoaded(false);
            setInvoices([]);
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    }, [currentMonth]);

    const handleMonthChange = useCallback(async (month: number) => {
        setCurrentMonth(month);
        // Reload stats from server
        try {
            const response = await fetch(`/api/atelier/stats?year=${currentYear}&month=${month}`);
            const data = await response.json();
            setCurrentStats(data.stats);
            setCurrentComparisonStats(data.comparisonStats);
            // Reset invoices when filter changes
            setInvoicesLoaded(false);
            setInvoices([]);
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    }, [currentYear]);

    const handleLoadInvoices = useCallback(async () => {
        if (invoicesLoaded) return;

        try {
            const response = await fetch(`/api/atelier/invoices?year=${currentYear}&month=${currentMonth}`);
            const data = await response.json();
            setInvoices(data);
            setInvoicesLoaded(true);
        } catch (error) {
            console.error('Failed to load invoices:', error);
        }
    }, [currentYear, currentMonth, invoicesLoaded]);

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
                    />
                </div>

                <div className="atelier-index__actions">
                    <Link
                        href="/atelier/devis/nouveau"
                        className="atelier-index__btn atelier-index__btn--primary"
                    >
                        Nouveau devis
                    </Link>
                </div>

                <div className="atelier-index__quotes">
                    <QuotesTabs
                        quotes={quotes}
                        invoices={invoices}
                        onLoadInvoices={handleLoadInvoices}
                        invoicesLoaded={invoicesLoaded}
                    />
                </div>
            </div>
        </MainLayout>
    );
}
