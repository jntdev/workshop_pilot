import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import CaisseForm from '@/Components/Vente/CaisseForm';

export default function Caisse() {
    return (
        <MainLayout>
            <Head title="Caisse" />
            <div className="caisse-page">
                <h1 className="caisse-page__title">Caisse</h1>
                <CaisseForm />
            </div>
        </MainLayout>
    );
}
