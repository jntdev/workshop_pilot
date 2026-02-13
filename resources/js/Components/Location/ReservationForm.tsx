import { useState, useCallback, useMemo } from 'react';
import ClientSearch from '@/Components/Atelier/QuoteForm/ClientSearch';
import type { Client, BikeType, ReservationFormData, ReservationItem, ReservationStatut } from '@/types';

interface ReservationFormProps {
    bikeTypes: BikeType[];
    onSuccess?: () => void;
}

const STATUT_OPTIONS: { value: ReservationStatut; label: string }[] = [
    { value: 'reserve', label: 'Réservé' },
    { value: 'en_attente_acompte', label: 'En attente d\'acompte' },
    { value: 'en_cours', label: 'En cours' },
    { value: 'paye', label: 'Payé' },
    { value: 'annule', label: 'Annulé' },
];

const initialFormData: ReservationFormData = {
    client_id: null,
    date_contact: new Date().toISOString().slice(0, 16),
    date_reservation: '',
    date_retour: '',
    livraison_necessaire: false,
    adresse_livraison: '',
    contact_livraison: '',
    creneau_livraison: '',
    recuperation_necessaire: false,
    adresse_recuperation: '',
    contact_recuperation: '',
    creneau_recuperation: '',
    prix_total_ttc: '',
    acompte_demande: false,
    acompte_montant: '',
    acompte_paye_le: '',
    paiement_final_le: '',
    statut: 'reserve',
    raison_annulation: '',
    commentaires: '',
    items: [],
};

export default function ReservationForm({ bikeTypes, onSuccess }: ReservationFormProps) {
    const [formData, setFormData] = useState<ReservationFormData>(initialFormData);
    const [selectedClient, setSelectedClient] = useState<Client | null>(null);
    const [isSaving, setIsSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

    // Données du nouveau client
    const [newClientData, setNewClientData] = useState({
        prenom: '',
        nom: '',
        telephone: '',
        email: '',
        adresse: '',
        origine_contact: '',
        commentaires: '',
        avantage_type: 'aucun' as const,
        avantage_valeur: 0,
        avantage_expiration: '',
    });

    // Grouper les bikeTypes par catégorie
    const bikeTypesByCategory = useMemo(() => {
        const grouped: Record<string, BikeType[]> = {};
        bikeTypes.forEach((bt) => {
            if (!grouped[bt.category]) {
                grouped[bt.category] = [];
            }
            grouped[bt.category].push(bt);
        });
        return grouped;
    }, [bikeTypes]);

    // Récap des vélos sélectionnés
    const selectedBikesRecap = useMemo(() => {
        return formData.items
            .filter((item) => item.quantite > 0)
            .map((item) => {
                const bikeType = bikeTypes.find((bt) => bt.id === item.bike_type_id);
                return bikeType ? `${item.quantite}x ${bikeType.label}` : '';
            })
            .filter(Boolean);
    }, [formData.items, bikeTypes]);

    // Calcul acompte suggéré (30%)
    const suggestedAcompte = useMemo(() => {
        const total = parseFloat(formData.prix_total_ttc) || 0;
        return (total * 0.3).toFixed(2);
    }, [formData.prix_total_ttc]);

    // Reste dû
    const resteDu = useMemo(() => {
        const total = parseFloat(formData.prix_total_ttc) || 0;
        const acompte = parseFloat(formData.acompte_montant) || 0;
        return (total - acompte).toFixed(2);
    }, [formData.prix_total_ttc, formData.acompte_montant]);

    const handleClientSelect = useCallback((client: Client) => {
        setSelectedClient(client);
        setFormData((prev) => ({ ...prev, client_id: client.id }));
        // Remplir les champs avec les données du client existant
        setNewClientData({
            prenom: client.prenom || '',
            nom: client.nom || '',
            telephone: client.telephone || '',
            email: client.email || '',
            adresse: client.adresse || '',
            origine_contact: client.origine_contact || '',
            commentaires: client.commentaires || '',
            avantage_type: client.avantage_type || 'aucun',
            avantage_valeur: client.avantage_valeur || 0,
            avantage_expiration: client.avantage_expiration || '',
        });
        setErrors((prev) => ({ ...prev, client_id: '', client_prenom: '', client_nom: '', client_telephone: '', client_email: '' }));
    }, []);

    // Vérifie si le nouveau client a les champs obligatoires
    const isNewClientValid = useMemo(() => {
        return (
            newClientData.prenom.trim() !== '' &&
            newClientData.nom.trim() !== '' &&
            newClientData.telephone.trim() !== ''
        );
    }, [newClientData.prenom, newClientData.nom, newClientData.telephone]);

    const handleBikeQuantityChange = useCallback((bikeTypeId: string, quantite: number) => {
        setFormData((prev) => {
            const existingIndex = prev.items.findIndex((item) => item.bike_type_id === bikeTypeId);
            const newItems = [...prev.items];

            if (existingIndex >= 0) {
                if (quantite <= 0) {
                    newItems.splice(existingIndex, 1);
                } else {
                    newItems[existingIndex] = { ...newItems[existingIndex], quantite };
                }
            } else if (quantite > 0) {
                newItems.push({ bike_type_id: bikeTypeId, quantite });
            }

            return { ...prev, items: newItems };
        });
    }, []);

    const getBikeQuantity = useCallback((bikeTypeId: string): number => {
        const item = formData.items.find((i) => i.bike_type_id === bikeTypeId);
        return item?.quantite || 0;
    }, [formData.items]);

    const handleSubmit = useCallback(async () => {
        setIsSaving(true);
        setErrors({});
        setMessage(null);

        try {
            const csrfToken = document.cookie
                .split('; ')
                .find((row) => row.startsWith('XSRF-TOKEN='))
                ?.split('=')[1];

            // Construire le payload
            const payload: Record<string, unknown> = {
                ...formData,
                prix_total_ttc: parseFloat(formData.prix_total_ttc) || 0,
                acompte_montant: formData.acompte_montant ? parseFloat(formData.acompte_montant) : null,
                adresse_livraison: formData.livraison_necessaire ? formData.adresse_livraison : null,
                contact_livraison: formData.livraison_necessaire ? formData.contact_livraison : null,
                creneau_livraison: formData.livraison_necessaire ? formData.creneau_livraison : null,
                adresse_recuperation: formData.recuperation_necessaire ? formData.adresse_recuperation : null,
                contact_recuperation: formData.recuperation_necessaire ? formData.contact_recuperation : null,
                creneau_recuperation: formData.recuperation_necessaire ? formData.creneau_recuperation : null,
                acompte_paye_le: formData.acompte_paye_le || null,
                paiement_final_le: formData.paiement_final_le || null,
                raison_annulation: formData.statut === 'annule' ? formData.raison_annulation : null,
            };

            // Si nouveau client (pas de client existant sélectionné mais formulaire valide), envoyer new_client
            if (!formData.client_id && isNewClientValid) {
                payload.client_id = null;
                payload.new_client = {
                    prenom: newClientData.prenom,
                    nom: newClientData.nom,
                    telephone: newClientData.telephone,
                    email: newClientData.email || null,
                    adresse: newClientData.adresse || null,
                    origine_contact: newClientData.origine_contact || null,
                    commentaires: newClientData.commentaires || null,
                    avantage_type: newClientData.avantage_type,
                    avantage_valeur: newClientData.avantage_valeur,
                    avantage_expiration: newClientData.avantage_expiration || null,
                };
            } else if (formData.client_id && isNewClientValid) {
                // Client existant sélectionné - envoyer les données pour mise à jour
                payload.update_client = {
                    prenom: newClientData.prenom,
                    nom: newClientData.nom,
                    telephone: newClientData.telephone,
                    email: newClientData.email || null,
                    adresse: newClientData.adresse || null,
                    origine_contact: newClientData.origine_contact || null,
                    commentaires: newClientData.commentaires || null,
                };
            }

            const response = await fetch('/api/reservations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': decodeURIComponent(csrfToken || ''),
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    const newErrors: Record<string, string> = {};
                    Object.entries(data.errors).forEach(([key, messages]) => {
                        // Mapper les erreurs new_client.* et update_client.* vers client_*
                        let mappedKey = key;
                        if (key.startsWith('new_client.')) {
                            mappedKey = key.replace('new_client.', 'client_');
                        } else if (key.startsWith('update_client.')) {
                            mappedKey = key.replace('update_client.', 'client_');
                        }
                        newErrors[mappedKey] = (messages as string[])[0];
                    });
                    setErrors(newErrors);
                }
                setMessage({ type: 'error', text: data.message || 'Erreur de validation' });
                return;
            }

            setMessage({ type: 'success', text: 'Réservation créée avec succès' });
            setFormData(initialFormData);
            setSelectedClient(null);
            setNewClientData({
                prenom: '',
                nom: '',
                telephone: '',
                email: '',
                adresse: '',
                origine_contact: '',
                commentaires: '',
                avantage_type: 'aucun',
                avantage_valeur: 0,
                avantage_expiration: '',
            });
            onSuccess?.();
        } catch (error) {
            console.error('Error creating reservation:', error);
            setMessage({ type: 'error', text: 'Erreur lors de la création de la réservation' });
        } finally {
            setIsSaving(false);
        }
    }, [formData, isNewClientValid, newClientData, onSuccess]);

    const isReadyToSubmit = useMemo(() => {
        const hasClient = formData.client_id || isNewClientValid;
        return (
            hasClient &&
            formData.date_reservation &&
            formData.date_retour &&
            formData.prix_total_ttc &&
            formData.items.length > 0
        );
    }, [formData, isNewClientValid]);

    return (
        <div className="reservation-form">
            {message && (
                <div className={`reservation-form__message reservation-form__message--${message.type}`}>
                    {message.text}
                </div>
            )}

            {/* Récap vélos sélectionnés */}
            {selectedBikesRecap.length > 0 && (
                <div className="reservation-form__recap">
                    {selectedBikesRecap.map((recap, index) => (
                        <span key={index} className="reservation-form__recap-chip">{recap}</span>
                    ))}
                </div>
            )}

            {/* Section 1: Client */}
            <section className="reservation-form__section">
                <h3 className="reservation-form__section-title">Client</h3>

                <ClientSearch onClientSelect={handleClientSelect} />

                {selectedClient && (
                    <div className="reservation-form__tag reservation-form__tag--success">
                        Client existant sélectionné
                    </div>
                )}

                <div className="reservation-form__new-client">
                    <div className="reservation-form__row">
                        <div className="reservation-form__field">
                            <label>Prénom *</label>
                            <input
                                type="text"
                                value={newClientData.prenom}
                                onChange={(e) => setNewClientData((prev) => ({ ...prev, prenom: e.target.value }))}
                                className={errors.client_prenom ? 'reservation-form__input--error' : ''}
                            />
                            {errors.client_prenom && <span className="reservation-form__error">{errors.client_prenom}</span>}
                        </div>
                        <div className="reservation-form__field">
                            <label>Nom *</label>
                            <input
                                type="text"
                                value={newClientData.nom}
                                onChange={(e) => setNewClientData((prev) => ({ ...prev, nom: e.target.value }))}
                                className={errors.client_nom ? 'reservation-form__input--error' : ''}
                            />
                            {errors.client_nom && <span className="reservation-form__error">{errors.client_nom}</span>}
                        </div>
                    </div>
                    <div className="reservation-form__row">
                        <div className="reservation-form__field">
                            <label>Téléphone *</label>
                            <input
                                type="tel"
                                value={newClientData.telephone}
                                onChange={(e) => setNewClientData((prev) => ({ ...prev, telephone: e.target.value }))}
                                className={errors.client_telephone ? 'reservation-form__input--error' : ''}
                            />
                            {errors.client_telephone && <span className="reservation-form__error">{errors.client_telephone}</span>}
                        </div>
                        <div className="reservation-form__field">
                            <label>Email</label>
                            <input
                                type="email"
                                value={newClientData.email}
                                onChange={(e) => setNewClientData((prev) => ({ ...prev, email: e.target.value }))}
                                className={errors.client_email ? 'reservation-form__input--error' : ''}
                            />
                            {errors.client_email && <span className="reservation-form__error">{errors.client_email}</span>}
                        </div>
                    </div>

                    <div className="reservation-form__field">
                        <label>Adresse</label>
                        <textarea
                            value={newClientData.adresse}
                            onChange={(e) => setNewClientData((prev) => ({ ...prev, adresse: e.target.value }))}
                            rows={2}
                        />
                    </div>
                    <div className="reservation-form__field">
                        <label>Origine du contact</label>
                        <input
                            type="text"
                            value={newClientData.origine_contact}
                            onChange={(e) => setNewClientData((prev) => ({ ...prev, origine_contact: e.target.value }))}
                        />
                    </div>
                    <div className="reservation-form__field">
                        <label>Commentaires</label>
                        <textarea
                            value={newClientData.commentaires}
                            onChange={(e) => setNewClientData((prev) => ({ ...prev, commentaires: e.target.value }))}
                            rows={2}
                        />
                    </div>

                    {!selectedClient && isNewClientValid && (
                        <div className="reservation-form__tag reservation-form__tag--success">
                            Nouveau client - sera créé avec la réservation
                        </div>
                    )}
                </div>
                {errors.client_id && <span className="reservation-form__error">{errors.client_id}</span>}
            </section>

            {/* Section 2: Dates & Logistique */}
            <section className="reservation-form__section">
                <h3 className="reservation-form__section-title">Dates & Logistique</h3>

                <div className="reservation-form__field">
                    <label>Date de contact</label>
                    <input
                        type="datetime-local"
                        value={formData.date_contact}
                        onChange={(e) => setFormData((prev) => ({ ...prev, date_contact: e.target.value }))}
                    />
                </div>

                <div className="reservation-form__row">
                    <div className="reservation-form__field">
                        <label>Début de location *</label>
                        <input
                            type="date"
                            value={formData.date_reservation}
                            onChange={(e) => setFormData((prev) => ({ ...prev, date_reservation: e.target.value }))}
                            className={errors.date_reservation ? 'reservation-form__input--error' : ''}
                        />
                        {errors.date_reservation && <span className="reservation-form__error">{errors.date_reservation}</span>}
                    </div>
                    <div className="reservation-form__field">
                        <label>Fin de location *</label>
                        <input
                            type="date"
                            value={formData.date_retour}
                            onChange={(e) => setFormData((prev) => ({ ...prev, date_retour: e.target.value }))}
                            min={formData.date_reservation}
                            className={errors.date_retour ? 'reservation-form__input--error' : ''}
                        />
                        {errors.date_retour && <span className="reservation-form__error">{errors.date_retour}</span>}
                    </div>
                </div>

                <div className="reservation-form__checkbox-group">
                    <label className="reservation-form__checkbox">
                        <input
                            type="checkbox"
                            checked={formData.livraison_necessaire}
                            onChange={(e) => setFormData((prev) => ({ ...prev, livraison_necessaire: e.target.checked }))}
                        />
                        <span>Livraison nécessaire</span>
                    </label>
                </div>

                {formData.livraison_necessaire && (
                    <div className="reservation-form__nested">
                        <div className="reservation-form__field">
                            <label>Adresse de livraison *</label>
                            <textarea
                                value={formData.adresse_livraison}
                                onChange={(e) => setFormData((prev) => ({ ...prev, adresse_livraison: e.target.value }))}
                                rows={2}
                                className={errors.adresse_livraison ? 'reservation-form__input--error' : ''}
                            />
                            {errors.adresse_livraison && <span className="reservation-form__error">{errors.adresse_livraison}</span>}
                        </div>
                        <div className="reservation-form__row">
                            <div className="reservation-form__field">
                                <label>Contact sur place</label>
                                <input
                                    type="text"
                                    value={formData.contact_livraison}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, contact_livraison: e.target.value }))}
                                />
                            </div>
                            <div className="reservation-form__field">
                                <label>Créneau souhaité</label>
                                <input
                                    type="text"
                                    value={formData.creneau_livraison}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, creneau_livraison: e.target.value }))}
                                    placeholder="ex: Matin (9h-12h)"
                                />
                            </div>
                        </div>
                    </div>
                )}

                <div className="reservation-form__checkbox-group">
                    <label className="reservation-form__checkbox">
                        <input
                            type="checkbox"
                            checked={formData.recuperation_necessaire}
                            onChange={(e) => {
                                const checked = e.target.checked;
                                setFormData((prev) => ({
                                    ...prev,
                                    recuperation_necessaire: checked,
                                    // Préremplir avec l'adresse de livraison
                                    adresse_recuperation: checked && prev.livraison_necessaire ? prev.adresse_livraison : prev.adresse_recuperation,
                                    contact_recuperation: checked && prev.livraison_necessaire ? prev.contact_livraison : prev.contact_recuperation,
                                }));
                            }}
                        />
                        <span>Récupération nécessaire</span>
                    </label>
                </div>

                {formData.recuperation_necessaire && (
                    <div className="reservation-form__nested">
                        <div className="reservation-form__field">
                            <label>Adresse de récupération *</label>
                            <textarea
                                value={formData.adresse_recuperation}
                                onChange={(e) => setFormData((prev) => ({ ...prev, adresse_recuperation: e.target.value }))}
                                rows={2}
                                className={errors.adresse_recuperation ? 'reservation-form__input--error' : ''}
                            />
                            {errors.adresse_recuperation && <span className="reservation-form__error">{errors.adresse_recuperation}</span>}
                        </div>
                        <div className="reservation-form__row">
                            <div className="reservation-form__field">
                                <label>Contact sur place</label>
                                <input
                                    type="text"
                                    value={formData.contact_recuperation}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, contact_recuperation: e.target.value }))}
                                />
                            </div>
                            <div className="reservation-form__field">
                                <label>Créneau souhaité</label>
                                <input
                                    type="text"
                                    value={formData.creneau_recuperation}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, creneau_recuperation: e.target.value }))}
                                    placeholder="ex: Après-midi (14h-18h)"
                                />
                            </div>
                        </div>
                    </div>
                )}

                {!formData.livraison_necessaire && !formData.recuperation_necessaire && (
                    <div className="reservation-form__tag reservation-form__tag--neutral">
                        Remise sur place
                    </div>
                )}
            </section>

            {/* Section 3: Vélos */}
            <section className="reservation-form__section">
                <h3 className="reservation-form__section-title">Vélos</h3>

                {formData.items.length === 0 && (
                    <div className="reservation-form__help">
                        Sélectionnez au moins un vélo pour enregistrer la réservation
                    </div>
                )}
                {errors.items && <span className="reservation-form__error">{errors.items}</span>}

                {Object.entries(bikeTypesByCategory).map(([category, types]) => (
                    <div key={category} className="reservation-form__bike-category">
                        <h4 className="reservation-form__bike-category-title">{category}</h4>
                        <div className="reservation-form__bike-grid">
                            {types.map((bikeType) => {
                                const quantity = getBikeQuantity(bikeType.id);
                                return (
                                    <div
                                        key={bikeType.id}
                                        className={`reservation-form__bike-card ${quantity > 0 ? 'reservation-form__bike-card--selected' : ''}`}
                                    >
                                        <div className="reservation-form__bike-label">
                                            {bikeType.size} {bikeType.frame_type === 'b' ? 'bas' : 'haut'}
                                        </div>
                                        <div className="reservation-form__bike-quantity">
                                            <button
                                                type="button"
                                                onClick={() => handleBikeQuantityChange(bikeType.id, Math.max(0, quantity - 1))}
                                                disabled={quantity <= 0}
                                            >
                                                -
                                            </button>
                                            <span>{quantity}</span>
                                            <button
                                                type="button"
                                                onClick={() => handleBikeQuantityChange(bikeType.id, quantity + 1)}
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                ))}
            </section>

            {/* Section 4: Finances & Statut */}
            <section className="reservation-form__section">
                <h3 className="reservation-form__section-title">Finances & Statut</h3>

                <div className="reservation-form__field">
                    <label>Prix total TTC *</label>
                    <div className="reservation-form__input-with-suffix">
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            value={formData.prix_total_ttc}
                            onChange={(e) => setFormData((prev) => ({ ...prev, prix_total_ttc: e.target.value }))}
                            className={errors.prix_total_ttc ? 'reservation-form__input--error' : ''}
                        />
                        <span className="reservation-form__suffix">€</span>
                    </div>
                    {errors.prix_total_ttc && <span className="reservation-form__error">{errors.prix_total_ttc}</span>}
                </div>

                <div className="reservation-form__checkbox-group">
                    <label className="reservation-form__checkbox">
                        <input
                            type="checkbox"
                            checked={formData.acompte_demande}
                            onChange={(e) => setFormData((prev) => ({ ...prev, acompte_demande: e.target.checked }))}
                        />
                        <span>Acompte demandé</span>
                    </label>
                </div>
                {errors.acompte_demande && <span className="reservation-form__error">{errors.acompte_demande}</span>}

                {formData.acompte_demande && (
                    <div className="reservation-form__nested">
                        <div className="reservation-form__field">
                            <label>Montant acompte</label>
                            <div className="reservation-form__input-with-suffix">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={formData.acompte_montant}
                                    onChange={(e) => setFormData((prev) => ({ ...prev, acompte_montant: e.target.value }))}
                                    placeholder={`Suggéré: ${suggestedAcompte}€`}
                                />
                                <span className="reservation-form__suffix">€</span>
                            </div>
                            {formData.prix_total_ttc && (
                                <span className="reservation-form__hint">30% = {suggestedAcompte}€</span>
                            )}
                        </div>
                        <div className="reservation-form__field">
                            <label>Acompte payé le</label>
                            <input
                                type="date"
                                value={formData.acompte_paye_le}
                                onChange={(e) => setFormData((prev) => ({ ...prev, acompte_paye_le: e.target.value }))}
                            />
                        </div>
                        {formData.acompte_paye_le && (
                            <div className="reservation-form__tag reservation-form__tag--success">
                                Acompte reçu
                            </div>
                        )}
                    </div>
                )}

                <div className="reservation-form__field">
                    <label>Statut *</label>
                    <select
                        value={formData.statut}
                        onChange={(e) => setFormData((prev) => ({ ...prev, statut: e.target.value as ReservationStatut }))}
                    >
                        {STATUT_OPTIONS.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                </div>

                {formData.statut === 'paye' && (
                    <div className="reservation-form__field">
                        <label>Paiement final reçu le *</label>
                        <input
                            type="date"
                            value={formData.paiement_final_le}
                            onChange={(e) => setFormData((prev) => ({ ...prev, paiement_final_le: e.target.value }))}
                            className={errors.paiement_final_le ? 'reservation-form__input--error' : ''}
                        />
                        {errors.paiement_final_le && <span className="reservation-form__error">{errors.paiement_final_le}</span>}
                    </div>
                )}

                {formData.statut === 'annule' && (
                    <div className="reservation-form__field">
                        <label>Raison d'annulation *</label>
                        <textarea
                            value={formData.raison_annulation}
                            onChange={(e) => setFormData((prev) => ({ ...prev, raison_annulation: e.target.value }))}
                            rows={2}
                            className={errors.raison_annulation ? 'reservation-form__input--error' : ''}
                        />
                        {errors.raison_annulation && <span className="reservation-form__error">{errors.raison_annulation}</span>}
                    </div>
                )}

                {/* Récapitulatif */}
                {formData.prix_total_ttc && (
                    <div className="reservation-form__summary">
                        <div className="reservation-form__summary-row">
                            <span>Total TTC</span>
                            <strong>{parseFloat(formData.prix_total_ttc).toFixed(2)} €</strong>
                        </div>
                        {formData.acompte_demande && formData.acompte_montant && (
                            <>
                                <div className="reservation-form__summary-row">
                                    <span>Acompte</span>
                                    <span>- {parseFloat(formData.acompte_montant).toFixed(2)} €</span>
                                </div>
                                <div className="reservation-form__summary-row reservation-form__summary-row--total">
                                    <span>Reste dû</span>
                                    <strong>{resteDu} €</strong>
                                </div>
                            </>
                        )}
                    </div>
                )}
            </section>

            {/* Commentaires */}
            <section className="reservation-form__section">
                <div className="reservation-form__field">
                    <label>Commentaires</label>
                    <textarea
                        value={formData.commentaires}
                        onChange={(e) => setFormData((prev) => ({ ...prev, commentaires: e.target.value }))}
                        rows={3}
                        placeholder="Notes internes..."
                    />
                </div>
            </section>

            {/* Actions */}
            <div className="reservation-form__footer">
                {isReadyToSubmit && (
                    <div className="reservation-form__tag reservation-form__tag--success">
                        Prêt à confirmer
                    </div>
                )}
                <button
                    type="button"
                    className="reservation-form__btn reservation-form__btn--primary reservation-form__btn--large"
                    onClick={handleSubmit}
                    disabled={isSaving || !isReadyToSubmit}
                >
                    {isSaving ? 'Enregistrement...' : 'Enregistrer la réservation'}
                </button>
            </div>
        </div>
    );
}
