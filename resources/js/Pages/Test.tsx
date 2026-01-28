import MainLayout from '@/Layouts/MainLayout';
import { Head, usePage } from '@inertiajs/react';
import { PageProps } from '@/types';

interface TestPageProps extends PageProps {
    message: string;
    timestamp: string;
}

export default function Test() {
    const { message, timestamp, auth } = usePage<TestPageProps>().props;

    return (
        <MainLayout>
            <Head title="Test React" />

            <div className="page-header">
                <h1>Test React + Inertia</h1>
            </div>

            <div style={{ padding: '20px', background: '#f8f9fa', borderRadius: '8px', marginTop: '20px' }}>
                <h2 style={{ color: '#28a745', marginBottom: '16px' }}>
                    La migration React fonctionne !
                </h2>

                <div style={{ marginBottom: '12px' }}>
                    <strong>Message du serveur :</strong> {message}
                </div>

                <div style={{ marginBottom: '12px' }}>
                    <strong>Timestamp :</strong> {timestamp}
                </div>

                <div style={{ marginBottom: '12px' }}>
                    <strong>Utilisateur connecté :</strong>{' '}
                    {auth.user ? auth.user.email : 'Non connecté'}
                </div>

                <div style={{ marginTop: '24px', padding: '16px', background: '#e7f5ff', borderRadius: '4px' }}>
                    <h3 style={{ marginBottom: '8px' }}>Stack technique :</h3>
                    <ul style={{ margin: 0, paddingLeft: '20px' }}>
                        <li>Laravel + Inertia.js (server-side)</li>
                        <li>React 18 + TypeScript (client-side)</li>
                        <li>Vite (bundler)</li>
                    </ul>
                </div>
            </div>
        </MainLayout>
    );
}
