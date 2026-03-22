import { useState, FormEvent } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { ClientFormPageProps, ClientFormData } from '@/types';

interface Props extends ClientFormPageProps {}

interface FormErrors {
    [key: string]: string;
}

export default function ClientForm({ client }: Props) {
    const isEditing = !!client;

    const [formData, setFormData] = useState<ClientFormData>({
        prenom: client?.prenom ?? '',
        nom: client?.nom ?? '',
        telephone: client?.telephone ?? '',
        email: client?.email ?? '',
        adresse: client?.adresse ?? '',
        origine_contact: client?.origine_contact ?? '',
        commentaires: client?.commentaires ?? '',
        avantage_type: client?.avantage_type ?? 'aucun',
        avantage_valeur: client?.avantage_valeur ?? 0,
        avantage_expiration: client?.avantage_expiration ?? '',
    });

    const [errors, setErrors] = useState<FormErrors>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleChange = (field: keyof ClientFormData, value: string | number) => {
        setFormData(prev => ({ ...prev, [field]: value }));
        // Clear error when field is modified
        if (errors[field]) {
            setErrors(prev => {
                const newErrors = { ...prev };
                delete newErrors[field];
                return newErrors;
            });
        }
    };

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        setErrors({});

        try {
            const url = isEditing ? `/api/clients/${client.id}` : '/api/clients';
            const method = isEditing ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie
                            .split('; ')
                            .find(row => row.startsWith('XSRF-TOKEN='))
                            ?.split('=')[1] ?? ''
                    ),
                },
                credentials: 'same-origin',
                body: JSON.stringify(formData),
            });

            if (response.ok) {
                router.visit('/clients', {
                    preserveState: false,
                });
            } else if (response.status === 422) {
                const data = await response.json();
                if (data.errors) {
                    const formattedErrors: FormErrors = {};
                    Object.keys(data.errors).forEach(key => {
                        formattedErrors[key] = data.errors[key][0];
                    });
                    setErrors(formattedErrors);
                }
            } else {
                setErrors({ general: 'Une erreur est survenue. Veuillez réessayer.' });
            }
        } catch (error) {
            console.error('Failed to save client:', error);
            setErrors({ general: 'Une erreur est survenue. Veuillez réessayer.' });
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleDelete = async () => {
        if (!client || !confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
            return;
        }

        setIsDeleting(true);

        try {
            const response = await fetch(`/api/clients/${client.id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie
                            .split('; ')
                            .find(row => row.startsWith('XSRF-TOKEN='))
                            ?.split('=')[1] ?? ''
                    ),
                },
                credentials: 'same-origin',
            });

            if (response.ok || response.status === 204) {
                router.visit('/clients', {
                    preserveState: false,
                });
            } else {
                setErrors({ general: 'Impossible de supprimer ce client.' });
            }
        } catch (error) {
            console.error('Failed to delete client:', error);
            setErrors({ general: 'Une erreur est survenue lors de la suppression.' });
        } finally {
            setIsDeleting(false);
        }
    };

    const getAvantageLabel = () => {
        if (formData.avantage_type === 'pourcentage') return '(%)';
        if (formData.avantage_type === 'montant') return '(€)';
        return '';
    };

    return (
        <MainLayout>
            <Head title={isEditing ? `${formData.prenom} ${formData.nom}` : 'Nouveau client'} />

            <div className="client-form">
                <div className="client-form__header">
                    {isEditing && (
                        <Link href="/clients" className="client-form__back">
                            ← Retour à la liste
                        </Link>
                    )}
                    <h2 className="client-form__title">
                        {isEditing ? `Fiche client : ${formData.prenom} ${formData.nom}` : 'Nouveau client'}
                    </h2>
                </div>

                {errors.general && (
                    <div className="client-form__error-banner">
                        {errors.general}
                    </div>
                )}

                <form onSubmit={handleSubmit} className="client-form__form">
                    <div className="client-form__section">
                        <h3 className="client-form__section-title">Informations personnelles</h3>

                        <div className="client-form__grid">
                            <div className="client-form__field">
                                <label htmlFor="prenom" className="client-form__label">Prénom *</label>
                                <input
                                    type="text"
                                    id="prenom"
                                    value={formData.prenom}
                                    onChange={(e) => handleChange('prenom', e.target.value)}
                                    className="client-form__input"
                                />
                                {errors.prenom && (
                                    <span className="client-form__error">{errors.prenom}</span>
                                )}
                            </div>

                            <div className="client-form__field">
                                <label htmlFor="nom" className="client-form__label">Nom *</label>
                                <input
                                    type="text"
                                    id="nom"
                                    value={formData.nom}
                                    onChange={(e) => handleChange('nom', e.target.value)}
                                    className="client-form__input"
                                />
                                {errors.nom && (
                                    <span className="client-form__error">{errors.nom}</span>
                                )}
                            </div>

                            <div className="client-form__field">
                                <label htmlFor="telephone" className="client-form__label">Téléphone *</label>
                                <input
                                    type="text"
                                    id="telephone"
                                    value={formData.telephone}
                                    onChange={(e) => handleChange('telephone', e.target.value)}
                                    className="client-form__input"
                                />
                                {errors.telephone && (
                                    <span className="client-form__error">{errors.telephone}</span>
                                )}
                            </div>

                            <div className="client-form__field">
                                <label htmlFor="email" className="client-form__label">Email</label>
                                <input
                                    type="email"
                                    id="email"
                                    value={formData.email}
                                    onChange={(e) => handleChange('email', e.target.value)}
                                    className="client-form__input"
                                />
                                {errors.email && (
                                    <span className="client-form__error">{errors.email}</span>
                                )}
                            </div>
                        </div>

                        <div className="client-form__field">
                            <label htmlFor="adresse" className="client-form__label">Adresse</label>
                            <textarea
                                id="adresse"
                                value={formData.adresse}
                                onChange={(e) => handleChange('adresse', e.target.value)}
                                className="client-form__textarea"
                                rows={3}
                            />
                            {errors.adresse && (
                                <span className="client-form__error">{errors.adresse}</span>
                            )}
                        </div>

                        <div className="client-form__grid">
                            <div className="client-form__field">
                                <label htmlFor="origine_contact" className="client-form__label">Origine du contact</label>
                                <input
                                    type="text"
                                    id="origine_contact"
                                    value={formData.origine_contact}
                                    onChange={(e) => handleChange('origine_contact', e.target.value)}
                                    className="client-form__input"
                                />
                                {errors.origine_contact && (
                                    <span className="client-form__error">{errors.origine_contact}</span>
                                )}
                            </div>
                        </div>

                        <div className="client-form__field">
                            <label htmlFor="commentaires" className="client-form__label">Commentaires</label>
                            <textarea
                                id="commentaires"
                                value={formData.commentaires}
                                onChange={(e) => handleChange('commentaires', e.target.value)}
                                className="client-form__textarea"
                                rows={3}
                            />
                            {errors.commentaires && (
                                <span className="client-form__error">{errors.commentaires}</span>
                            )}
                        </div>
                    </div>

                    <div className="client-form__section">
                        <h3 className="client-form__section-title">Avantages client</h3>

                        <div className="client-form__grid">
                            <div className="client-form__field">
                                <label htmlFor="avantage_type" className="client-form__label">Type d'avantage *</label>
                                <select
                                    id="avantage_type"
                                    value={formData.avantage_type}
                                    onChange={(e) => handleChange('avantage_type', e.target.value as ClientFormData['avantage_type'])}
                                    className="client-form__select"
                                >
                                    <option value="aucun">Aucun</option>
                                    <option value="pourcentage">Pourcentage</option>
                                    <option value="montant">Montant (€)</option>
                                </select>
                                {errors.avantage_type && (
                                    <span className="client-form__error">{errors.avantage_type}</span>
                                )}
                            </div>

                            <div className="client-form__field">
                                <label htmlFor="avantage_valeur" className="client-form__label">
                                    Valeur * {getAvantageLabel()}
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    id="avantage_valeur"
                                    value={formData.avantage_valeur}
                                    onChange={(e) => handleChange('avantage_valeur', parseFloat(e.target.value) || 0)}
                                    className="client-form__input"
                                />
                                {errors.avantage_valeur && (
                                    <span className="client-form__error">{errors.avantage_valeur}</span>
                                )}
                            </div>

                            <div className="client-form__field">
                                <label htmlFor="avantage_expiration" className="client-form__label">Date d'expiration</label>
                                <input
                                    type="date"
                                    id="avantage_expiration"
                                    value={formData.avantage_expiration}
                                    onChange={(e) => handleChange('avantage_expiration', e.target.value)}
                                    className="client-form__input"
                                />
                                {errors.avantage_expiration && (
                                    <span className="client-form__error">{errors.avantage_expiration}</span>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="client-form__actions">
                        {isEditing ? (
                            <>
                                <button
                                    type="button"
                                    onClick={handleDelete}
                                    className="btn btn-danger"
                                    disabled={isDeleting}
                                >
                                    {isDeleting ? 'Suppression...' : 'Supprimer'}
                                </button>
                                <button
                                    type="submit"
                                    className="btn btn-primary"
                                    disabled={isSubmitting}
                                >
                                    {isSubmitting ? 'Modification...' : 'Modifier'}
                                </button>
                            </>
                        ) : (
                            <button
                                type="submit"
                                className="btn btn-primary"
                                disabled={isSubmitting}
                            >
                                {isSubmitting ? 'Enregistrement...' : 'Enregistrer le client'}
                            </button>
                        )}
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
