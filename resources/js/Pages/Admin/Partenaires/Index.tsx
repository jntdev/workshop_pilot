import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { PageProps } from '@/types';

interface WhitelistEntry {
    id: number;
    email: string;
    created_at: string;
}

interface ComptePartenaire {
    id: number;
    nom: string;
    email: string;
    actif: boolean;
    created_at: string;
}

interface Props extends PageProps {
    enAttente: WhitelistEntry[];
    comptes: ComptePartenaire[];
}

export default function PartenairesIndex({ enAttente, comptes }: Props) {
    const [email, setEmail] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState('');

    const handleAddWhitelist = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        setError('');

        try {
            const response = await fetch('/admin/partenaires/whitelist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? ''
                    ),
                },
                credentials: 'same-origin',
                body: JSON.stringify({ email }),
            });

            if (response.ok) {
                setEmail('');
                router.reload();
            } else {
                const data = await response.json();
                setError(data.errors?.email?.[0] ?? data.message ?? 'Une erreur est survenue.');
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleRemoveWhitelist = async (emailToRemove: string) => {
        if (!confirm(`Retirer ${emailToRemove} de la whitelist ?`)) return;

        const response = await fetch(`/admin/partenaires/whitelist/${encodeURIComponent(emailToRemove)}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(
                    document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? ''
                ),
            },
            credentials: 'same-origin',
        });

        if (response.ok) {
            router.reload();
        } else {
            const data = await response.json();
            alert(data.message ?? 'Une erreur est survenue.');
        }
    };

    const handleToggle = async (partenaire: ComptePartenaire) => {
        const action = partenaire.actif ? 'désactiver' : 'activer';
        if (!confirm(`Voulez-vous ${action} le compte de ${partenaire.nom} ?`)) return;

        const response = await fetch(`/admin/partenaires/${partenaire.id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(
                    document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? ''
                ),
            },
            credentials: 'same-origin',
        });

        if (response.ok) {
            router.reload();
        }
    };

    return (
        <MainLayout>
            <Head title="Gestion des partenaires" />

            <div className="page-header">
                <div className="breadcrumb">
                    <Link href="/">Accueil</Link>
                    <span>&gt;</span>
                    <span>Partenaires</span>
                </div>
                <h1>Gestion des partenaires</h1>
            </div>

            <div style={{ maxWidth: '900px', margin: '0 auto', padding: '2rem' }}>

                {/* Section whitelist */}
                <section style={{ background: 'white', borderRadius: '8px', padding: '2rem', marginBottom: '2rem', boxShadow: '0 1px 3px rgba(0,0,0,0.1)' }}>
                    <h2 style={{ fontSize: '1.25rem', marginBottom: '1.5rem' }}>Autoriser un nouveau partenaire</h2>

                    <form onSubmit={handleAddWhitelist} style={{ display: 'flex', gap: '1rem', alignItems: 'flex-start' }}>
                        <div style={{ flex: 1 }}>
                            <input
                                type="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="email@hotel-partenaire.fr"
                                required
                                style={{ width: '100%', padding: '0.6rem 1rem', border: '1px solid #ced4da', borderRadius: '4px', fontSize: '1rem' }}
                            />
                            {error && <p style={{ color: '#dc3545', fontSize: '0.875rem', marginTop: '0.4rem' }}>{error}</p>}
                        </div>
                        <button
                            type="submit"
                            disabled={isSubmitting}
                            style={{ padding: '0.6rem 1.5rem', background: '#0d6efd', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', fontSize: '1rem', whiteSpace: 'nowrap' }}
                        >
                            {isSubmitting ? 'Ajout...' : 'Autoriser'}
                        </button>
                    </form>

                    {enAttente.length > 0 && (
                        <div style={{ marginTop: '1.5rem' }}>
                            <h3 style={{ fontSize: '1rem', color: '#6c757d', marginBottom: '0.75rem' }}>
                                En attente d'inscription ({enAttente.length})
                            </h3>
                            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                                <tbody>
                                    {enAttente.map((entry) => (
                                        <tr key={entry.id} style={{ borderBottom: '1px solid #f0f0f0' }}>
                                            <td style={{ padding: '0.6rem 0' }}>{entry.email}</td>
                                            <td style={{ padding: '0.6rem 0', color: '#6c757d', fontSize: '0.875rem' }}>
                                                Ajouté le {new Date(entry.created_at).toLocaleDateString('fr-FR')}
                                            </td>
                                            <td style={{ padding: '0.6rem 0', textAlign: 'right' }}>
                                                <button
                                                    type="button"
                                                    onClick={() => handleRemoveWhitelist(entry.email)}
                                                    style={{ background: 'none', border: 'none', color: '#dc3545', cursor: 'pointer', fontSize: '0.875rem' }}
                                                >
                                                    Retirer
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </section>

                {/* Section comptes */}
                <section style={{ background: 'white', borderRadius: '8px', padding: '2rem', boxShadow: '0 1px 3px rgba(0,0,0,0.1)' }}>
                    <h2 style={{ fontSize: '1.25rem', marginBottom: '1.5rem' }}>
                        Comptes partenaires ({comptes.length})
                    </h2>

                    {comptes.length === 0 ? (
                        <p style={{ color: '#6c757d' }}>Aucun partenaire inscrit pour le moment.</p>
                    ) : (
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '2px solid #dee2e6', textAlign: 'left' }}>
                                    <th style={{ padding: '0.6rem 0.5rem', fontWeight: 600 }}>Établissement</th>
                                    <th style={{ padding: '0.6rem 0.5rem', fontWeight: 600 }}>Email</th>
                                    <th style={{ padding: '0.6rem 0.5rem', fontWeight: 600 }}>Inscrit le</th>
                                    <th style={{ padding: '0.6rem 0.5rem', fontWeight: 600 }}>Statut</th>
                                    <th style={{ padding: '0.6rem 0.5rem' }}></th>
                                </tr>
                            </thead>
                            <tbody>
                                {comptes.map((compte) => (
                                    <tr key={compte.id} style={{ borderBottom: '1px solid #f0f0f0' }}>
                                        <td style={{ padding: '0.75rem 0.5rem', fontWeight: 500 }}>{compte.nom}</td>
                                        <td style={{ padding: '0.75rem 0.5rem', color: '#6c757d' }}>{compte.email}</td>
                                        <td style={{ padding: '0.75rem 0.5rem', color: '#6c757d', fontSize: '0.875rem' }}>
                                            {new Date(compte.created_at).toLocaleDateString('fr-FR')}
                                        </td>
                                        <td style={{ padding: '0.75rem 0.5rem' }}>
                                            <span style={{
                                                display: 'inline-block',
                                                padding: '0.2rem 0.6rem',
                                                borderRadius: '12px',
                                                fontSize: '0.8rem',
                                                fontWeight: 600,
                                                background: compte.actif ? '#d1fae5' : '#fee2e2',
                                                color: compte.actif ? '#065f46' : '#991b1b',
                                            }}>
                                                {compte.actif ? 'Actif' : 'Inactif'}
                                            </span>
                                        </td>
                                        <td style={{ padding: '0.75rem 0.5rem', textAlign: 'right' }}>
                                            <button
                                                type="button"
                                                onClick={() => handleToggle(compte)}
                                                style={{
                                                    background: 'none',
                                                    border: '1px solid',
                                                    borderColor: compte.actif ? '#dc3545' : '#198754',
                                                    color: compte.actif ? '#dc3545' : '#198754',
                                                    borderRadius: '4px',
                                                    padding: '0.3rem 0.75rem',
                                                    cursor: 'pointer',
                                                    fontSize: '0.875rem',
                                                }}
                                            >
                                                {compte.actif ? 'Désactiver' : 'Activer'}
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </section>
            </div>
        </MainLayout>
    );
}
