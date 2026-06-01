import { useEffect, useState } from 'react';
import PartenaireLayout from '../components/PartenaireLayout';
import type { PartenaireData } from '../api/auth';
import { getDisponibilites, type Disponibilite } from '../api/disponibilites';
import { getDemandes, storeDemande, type Demande, type Creneau } from '../api/demandes';
import { AxiosError } from 'axios';

interface Props {
    partenaire: PartenaireData | null;
    onLogout: () => void;
}

const GUIDE_TAILLES = [
    { label: '< 155 cm', cadre: 'XS' },
    { label: '155 – 165 cm', cadre: 'S' },
    { label: '165 – 175 cm', cadre: 'M' },
    { label: '175 – 185 cm', cadre: 'L' },
    { label: '> 185 cm', cadre: 'XL' },
];

const STATUT_LABELS: Record<string, string> = {
    en_attente: 'En attente de confirmation',
    confirmee: 'Confirmée',
    annulee: 'Annulée',
};

const CRENEAU_LABELS: Record<Creneau, string> = {
    journee: 'Journée entière',
    matin: 'Matin',
    'apres-midi': 'Après-midi',
};

const inputStyle: React.CSSProperties = {
    width: '100%',
    padding: '0.5rem 0.75rem',
    border: '1px solid #ced4da',
    borderRadius: '4px',
    fontSize: '0.95rem',
    boxSizing: 'border-box',
};

const labelStyle: React.CSSProperties = {
    display: 'block',
    marginBottom: '0.3rem',
    fontWeight: 500,
    fontSize: '0.875rem',
};

export default function DashboardPage({ partenaire, onLogout }: Props) {
    const [dateDebut, setDateDebut] = useState('');
    const [dateFin, setDateFin] = useState('');
    const [creneau, setCreneau] = useState<Creneau>('journee');
    const [clients, setClients] = useState<Array<{ taille_cm: string }>>([{ taille_cm: '' }]);
    const [commentaire, setCommentaire] = useState('');

    const [disponibilites, setDisponibilites] = useState<Disponibilite[]>([]);
    const [loadingDispo, setLoadingDispo] = useState(false);

    const [demandes, setDemandes] = useState<Demande[]>([]);
    const [loadingDemandes, setLoadingDemandes] = useState(true);

    const [submitting, setSubmitting] = useState(false);
    const [submitError, setSubmitError] = useState('');
    const [submitSuccess, setSubmitSuccess] = useState(false);
    const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});

    const today = new Date().toISOString().split('T')[0];

    useEffect(() => {
        getDemandes()
            .then(setDemandes)
            .finally(() => setLoadingDemandes(false));
    }, []);

    useEffect(() => {
        if (!dateDebut || !dateFin || dateFin < dateDebut) {
            setDisponibilites([]);
            return;
        }
        setLoadingDispo(true);
        getDisponibilites(dateDebut, dateFin)
            .then((r) => setDisponibilites(r.disponibilites))
            .catch(() => setDisponibilites([]))
            .finally(() => setLoadingDispo(false));
    }, [dateDebut, dateFin]);

    const addClient = () => setClients((prev) => [...prev, { taille_cm: '' }]);

    const removeClient = (index: number) =>
        setClients((prev) => prev.filter((_, i) => i !== index));

    const updateClientTaille = (index: number, value: string) =>
        setClients((prev) => prev.map((c, i) => (i === index ? { taille_cm: value } : c)));

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitError('');
        setFieldErrors({});
        setSubmitSuccess(false);

        const clientsPayload = clients
            .filter((c) => c.taille_cm !== '')
            .map((c) => ({ taille_cm: parseInt(c.taille_cm, 10) }));

        setSubmitting(true);
        try {
            const demande = await storeDemande({
                date_debut: dateDebut,
                date_fin: dateFin,
                creneau,
                clients: clientsPayload,
                commentaire: commentaire || undefined,
            });
            setDemandes((prev) => [demande, ...prev]);
            setDateDebut('');
            setDateFin('');
            setCreneau('journee');
            setClients([{ taille_cm: '' }]);
            setCommentaire('');
            setDisponibilites([]);
            setSubmitSuccess(true);
        } catch (err) {
            const axiosErr = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>;
            if (axiosErr.response?.status === 422 && axiosErr.response.data.errors) {
                setFieldErrors(axiosErr.response.data.errors);
            } else {
                setSubmitError('Une erreur est survenue. Veuillez réessayer.');
            }
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <PartenaireLayout nomEtablissement={partenaire?.nom} onLogout={onLogout}>
            {/* Formulaire de demande */}
            <div style={{ background: 'white', borderRadius: '8px', padding: '2rem', boxShadow: '0 1px 3px rgba(0,0,0,0.08)', marginBottom: '2rem' }}>
                <h2 style={{ fontSize: '1.1rem', marginBottom: '1.5rem', fontWeight: 600 }}>
                    Nouvelle demande de réservation
                </h2>

                <form onSubmit={handleSubmit}>
                    {/* Dates + créneau */}
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '1rem', marginBottom: '1.25rem' }}>
                        <div>
                            <label style={labelStyle}>Date de début</label>
                            <input
                                type="date"
                                value={dateDebut}
                                min={today}
                                onChange={(e) => setDateDebut(e.target.value)}
                                required
                                style={{ ...inputStyle, borderColor: fieldErrors['date_debut'] ? '#dc3545' : '#ced4da' }}
                            />
                            {fieldErrors['date_debut'] && (
                                <p style={{ color: '#dc3545', fontSize: '0.8rem', marginTop: '0.2rem' }}>{fieldErrors['date_debut'][0]}</p>
                            )}
                        </div>
                        <div>
                            <label style={labelStyle}>Date de fin</label>
                            <input
                                type="date"
                                value={dateFin}
                                min={dateDebut || today}
                                onChange={(e) => setDateFin(e.target.value)}
                                required
                                style={{ ...inputStyle, borderColor: fieldErrors['date_fin'] ? '#dc3545' : '#ced4da' }}
                            />
                            {fieldErrors['date_fin'] && (
                                <p style={{ color: '#dc3545', fontSize: '0.8rem', marginTop: '0.2rem' }}>{fieldErrors['date_fin'][0]}</p>
                            )}
                        </div>
                        <div>
                            <label style={labelStyle}>Créneau</label>
                            <select
                                value={creneau}
                                onChange={(e) => setCreneau(e.target.value as Creneau)}
                                style={inputStyle}
                            >
                                <option value="journee">Journée entière</option>
                                <option value="matin">Matin</option>
                                <option value="apres-midi">Après-midi</option>
                            </select>
                        </div>
                    </div>

                    {/* Stock disponible */}
                    {(loadingDispo || disponibilites.length > 0) && (
                        <div style={{ marginBottom: '1.25rem', padding: '1rem', background: '#f8f9fa', borderRadius: '6px' }}>
                            <p style={{ fontSize: '0.8rem', fontWeight: 600, color: '#6c757d', marginBottom: '0.5rem', textTransform: 'uppercase', letterSpacing: '0.04em' }}>
                                Stock indicatif sur la période
                            </p>
                            {loadingDispo ? (
                                <p style={{ fontSize: '0.875rem', color: '#6c757d' }}>Chargement…</p>
                            ) : (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                                    {Object.entries(
                                        disponibilites.reduce<Record<string, Disponibilite[]>>((acc, d) => {
                                            (acc[d.categorie] ??= []).push(d);
                                            return acc;
                                        }, {})
                                    ).map(([categorie, items]) => (
                                        <div key={categorie} style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', flexWrap: 'wrap' }}>
                                            <span style={{ fontSize: '0.8rem', fontWeight: 600, color: '#495057', minWidth: '3rem' }}>{categorie}</span>
                                            {items.map((d) => (
                                                <span
                                                    key={d.taille}
                                                    style={{
                                                        padding: '0.2rem 0.65rem',
                                                        borderRadius: '20px',
                                                        fontSize: '0.85rem',
                                                        background: d.disponible === 0 ? '#fff3cd' : '#d1e7dd',
                                                        color: d.disponible === 0 ? '#856404' : '#0f5132',
                                                        fontWeight: 500,
                                                    }}
                                                >
                                                    {d.taille} : {d.disponible}
                                                </span>
                                            ))}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}

                    {/* Guide tailles + clients */}
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem', marginBottom: '1.25rem' }}>
                        {/* Guide */}
                        <div>
                            <p style={{ ...labelStyle, marginBottom: '0.5rem' }}>Guide de tailles</p>
                            <table style={{ width: '100%', fontSize: '0.85rem', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr style={{ background: '#f8f9fa' }}>
                                        <th style={{ padding: '0.4rem 0.75rem', textAlign: 'left', fontWeight: 500, border: '1px solid #dee2e6' }}>Taille client</th>
                                        <th style={{ padding: '0.4rem 0.75rem', textAlign: 'left', fontWeight: 500, border: '1px solid #dee2e6' }}>Taille cadre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {GUIDE_TAILLES.map((g) => (
                                        <tr key={g.cadre}>
                                            <td style={{ padding: '0.35rem 0.75rem', border: '1px solid #dee2e6', color: '#495057' }}>{g.label}</td>
                                            <td style={{ padding: '0.35rem 0.75rem', border: '1px solid #dee2e6', fontWeight: 600 }}>{g.cadre}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Clients */}
                        <div>
                            <p style={labelStyle}>Clients ({clients.length})</p>
                            <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                                {clients.map((c, i) => (
                                    <div key={i} style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                                        <input
                                            type="number"
                                            value={c.taille_cm}
                                            min={100}
                                            max={220}
                                            placeholder={`Client ${i + 1} — taille en cm`}
                                            onChange={(e) => updateClientTaille(i, e.target.value)}
                                            required
                                            style={{ ...inputStyle, borderColor: fieldErrors[`clients.${i}.taille_cm`] ? '#dc3545' : '#ced4da' }}
                                        />
                                        {clients.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeClient(i)}
                                                style={{ background: 'none', border: 'none', color: '#dc3545', cursor: 'pointer', fontSize: '1.1rem', flexShrink: 0 }}
                                                aria-label="Supprimer ce client"
                                            >
                                                ×
                                            </button>
                                        )}
                                    </div>
                                ))}
                                <button
                                    type="button"
                                    onClick={addClient}
                                    style={{ alignSelf: 'flex-start', background: 'none', border: '1px dashed #ced4da', borderRadius: '4px', padding: '0.4rem 0.75rem', fontSize: '0.875rem', color: '#0d6efd', cursor: 'pointer', marginTop: '0.25rem' }}
                                >
                                    + Ajouter un client
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Commentaire */}
                    <div style={{ marginBottom: '1.25rem' }}>
                        <label style={labelStyle}>Commentaire <span style={{ fontWeight: 400, color: '#6c757d' }}>(optionnel)</span></label>
                        <textarea
                            value={commentaire}
                            onChange={(e) => setCommentaire(e.target.value)}
                            maxLength={500}
                            rows={3}
                            placeholder="Informations complémentaires pour Les Vélos d'Armor…"
                            style={{ ...inputStyle, resize: 'vertical' }}
                        />
                    </div>

                    {submitError && (
                        <p style={{ color: '#dc3545', fontSize: '0.875rem', marginBottom: '1rem', padding: '0.6rem', background: '#fff5f5', borderRadius: '4px' }}>
                            {submitError}
                        </p>
                    )}

                    {submitSuccess && (
                        <p style={{ color: '#0f5132', fontSize: '0.875rem', marginBottom: '1rem', padding: '0.6rem', background: '#d1e7dd', borderRadius: '4px' }}>
                            Votre demande a bien été envoyée. Nous vous contacterons pour confirmation.
                        </p>
                    )}

                    <button
                        type="submit"
                        disabled={submitting}
                        style={{ padding: '0.7rem 1.5rem', background: '#0d6efd', color: 'white', border: 'none', borderRadius: '4px', fontSize: '0.95rem', fontWeight: 500, cursor: 'pointer' }}
                    >
                        {submitting ? 'Envoi…' : 'Envoyer la demande'}
                    </button>
                </form>
            </div>

            {/* Liste des demandes */}
            <div style={{ background: 'white', borderRadius: '8px', padding: '2rem', boxShadow: '0 1px 3px rgba(0,0,0,0.08)' }}>
                <h2 style={{ fontSize: '1.1rem', marginBottom: '1.25rem', fontWeight: 600 }}>
                    Mes demandes
                </h2>

                {loadingDemandes ? (
                    <p style={{ color: '#6c757d', fontSize: '0.9rem' }}>Chargement…</p>
                ) : demandes.length === 0 ? (
                    <p style={{ color: '#6c757d', fontSize: '0.9rem' }}>Aucune demande pour l'instant.</p>
                ) : (
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.875rem' }}>
                        <thead>
                            <tr style={{ borderBottom: '2px solid #dee2e6' }}>
                                <th style={{ padding: '0.5rem 0.75rem', textAlign: 'left', fontWeight: 600, color: '#495057' }}>Dates</th>
                                <th style={{ padding: '0.5rem 0.75rem', textAlign: 'left', fontWeight: 600, color: '#495057' }}>Créneau</th>
                                <th style={{ padding: '0.5rem 0.75rem', textAlign: 'left', fontWeight: 600, color: '#495057' }}>Clients</th>
                                <th style={{ padding: '0.5rem 0.75rem', textAlign: 'left', fontWeight: 600, color: '#495057' }}>Statut</th>
                                <th style={{ padding: '0.5rem 0.75rem', textAlign: 'left', fontWeight: 600, color: '#495057' }}>Envoyée le</th>
                            </tr>
                        </thead>
                        <tbody>
                            {demandes.map((d) => (
                                <tr key={d.id} style={{ borderBottom: '1px solid #f1f3f5' }}>
                                    <td style={{ padding: '0.6rem 0.75rem' }}>
                                        {d.date_debut} → {d.date_fin}
                                    </td>
                                    <td style={{ padding: '0.6rem 0.75rem', color: '#495057' }}>
                                        {CRENEAU_LABELS[d.creneau]}
                                    </td>
                                    <td style={{ padding: '0.6rem 0.75rem' }}>{d.nb_clients}</td>
                                    <td style={{ padding: '0.6rem 0.75rem' }}>
                                        <span style={{
                                            padding: '0.2rem 0.6rem',
                                            borderRadius: '20px',
                                            fontSize: '0.8rem',
                                            fontWeight: 500,
                                            background: d.statut === 'confirmee' ? '#d1e7dd' : d.statut === 'annulee' ? '#f8d7da' : '#fff3cd',
                                            color: d.statut === 'confirmee' ? '#0f5132' : d.statut === 'annulee' ? '#842029' : '#856404',
                                        }}>
                                            {STATUT_LABELS[d.statut] ?? d.statut}
                                        </span>
                                    </td>
                                    <td style={{ padding: '0.6rem 0.75rem', color: '#6c757d' }}>{d.created_at}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </PartenaireLayout>
    );
}
