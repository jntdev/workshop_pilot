import { useState, useCallback } from 'react';
import { router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { Head } from '@inertiajs/react';
import KpiRow from '@/Components/Dashboard/KpiCard';

interface MetierKpi {
    metier: string;
    revenue: number;
    margin: number | null;
    average_basket: number | null;
    invoice_count: number;
    trend: number | null;
    has_data: boolean;
}

interface Period {
    year: number;
    month: number;
    label: string;
}

interface DashboardProps {
    kpis: {
        vente: MetierKpi;
        atelier: MetierKpi;
        location: MetierKpi;
    };
    period: Period;
}

const METIER_CONFIG = {
    vente: {
        title: 'Vente',
        href: '/atelier',
    },
    atelier: {
        title: 'Atelier',
        href: '/atelier',
    },
    location: {
        title: 'Location',
        href: '/location',
        marginUnavailable: true,
    },
};

export default function Dashboard({ kpis, period }: DashboardProps) {
    const metiers = ['vente', 'atelier', 'location'] as const;
    const [isRebuilding, setIsRebuilding] = useState(false);

    const handleRebuildKpis = useCallback(async () => {
        setIsRebuilding(true);
        try {
            const response = await fetch('/api/dashboard/kpis/rebuild', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            if (response.ok) {
                router.reload();
            }
        } catch (error) {
            console.error('Failed to rebuild KPIs:', error);
        } finally {
            setIsRebuilding(false);
        }
    }, []);

    return (
        <MainLayout>
            <Head title="Tableau de bord" />

            <div className="dashboard">
                <div className="dashboard__header">
                    <div className="dashboard__header-left">
                        <h1 className="dashboard__title">Tableau de bord</h1>
                        <span className="dashboard__period">{period.label}</span>
                    </div>
                    <button
                        type="button"
                        onClick={handleRebuildKpis}
                        disabled={isRebuilding}
                        className="dashboard__btn-rebuild"
                    >
                        {isRebuilding ? 'Recalcul...' : 'Recalculer les KPIs'}
                    </button>
                </div>

                <div className="dashboard__table">
                    <div className="dashboard__table-header">
                        <div className="dashboard__th dashboard__th--title">Métier</div>
                        <div className="dashboard__th">CA</div>
                        <div className="dashboard__th">Marge</div>
                        <div className="dashboard__th">Panier</div>
                        <div className="dashboard__th dashboard__th--link"></div>
                    </div>

                    {metiers.map((metier) => {
                        const kpi = kpis[metier];
                        const config = METIER_CONFIG[metier];

                        return (
                            <KpiRow
                                key={metier}
                                title={config.title}
                                href={config.href}
                                revenue={kpi.revenue}
                                margin={kpi.margin}
                                averageBasket={kpi.average_basket}
                                trend={kpi.trend}
                                hasData={kpi.has_data}
                                marginUnavailable={'marginUnavailable' in config ? config.marginUnavailable : false}
                            />
                        );
                    })}
                </div>
            </div>
        </MainLayout>
    );
}
