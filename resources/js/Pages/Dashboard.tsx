import MainLayout from '@/Layouts/MainLayout';
import { Head, Link } from '@inertiajs/react';

interface DashboardCard {
    title: string;
    description: string;
    href: string;
}

const cards: DashboardCard[] = [
    {
        title: 'Clients',
        description: 'Gérer vos clients et leurs informations',
        href: '/clients',
    },
    {
        title: 'Atelier',
        description: 'Réparations, vente et conversion de vélos',
        href: '/atelier',
    },
    {
        title: 'Location',
        description: 'Courte et longue durée',
        href: '/location',
    },
];

export default function Dashboard() {
    return (
        <MainLayout>
            <Head title="Tableau de bord" />

            <div className="dashboard">
                <h1 className="dashboard__title">Tableau de bord</h1>

                <div className="dashboard__cards">
                    {cards.map((card) => (
                        <Link
                            key={card.href}
                            href={card.href}
                            className="dashboard-card"
                        >
                            <div className="dashboard-card__content">
                                <h2 className="dashboard-card__title">{card.title}</h2>
                                <p className="dashboard-card__description">
                                    {card.description}
                                </p>
                            </div>
                        </Link>
                    ))}
                </div>
            </div>
        </MainLayout>
    );
}
