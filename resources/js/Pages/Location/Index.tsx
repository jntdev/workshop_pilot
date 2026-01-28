import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function LocationIndex() {
    return (
        <MainLayout>
            <Head title="Location" />

            <div className="page-header">
                <h1>Location</h1>
            </div>

            <div className="location-index">
                <p className="location-index__placeholder">
                    Page location - Contenu à venir
                </p>
            </div>
        </MainLayout>
    );
}
