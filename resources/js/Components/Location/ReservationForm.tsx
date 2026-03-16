import { useState, useCallback, useMemo, useEffect } from 'react';
import { router } from '@inertiajs/react';
import ClientSearch from '@/Components/Atelier/QuoteForm/ClientSearch';
import ColorPicker from '@/Components/Location/ColorPicker';
import type {
    Client,
    ReservationFormData,
    ReservationStatut,
    ReservationDraft,
    ReservationDraftActions,
    ReservationDraftSelectors,
    LoadedReservation,
    PaymentLine,
    PaymentMethod,
} from '@/types';

interface ReservationFormProps {
    draft: ReservationDraft;
    selectors: ReservationDraftSelectors;
    actions: ReservationDraftActions;
    editingReservation?: LoadedReservation | null;
    viewingMode?: boolean;
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
    selection: [],
    payments: [],
};

export default function ReservationForm({ draft, selectors, actions, editingReservation, viewingMode = false, onSuccess }: ReservationFormProps) {
    const [formData, setFormData] = useState<ReservationFormData>(initialFormData);
    const [selectedClient, setSelectedClient] = useState<Client | null>(null);
    const [isSaving, setIsSaving] = useState(false);
    const [isSendingAcompteEmail, setIsSendingAcompteEmail] = useState(false);
    const [acompteEmailMessage, setAcompteEmailMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

    // Données du nouveau client
    const [newClientData, setNewClientData] = useState<{
        prenom: string;
        nom: string;
        telephone: string;
        email: string;
        adresse: string;
        origine_contact: string;
        commentaires: string;
        avantage_type: 'aucun' | 'pourcentage' | 'montant';
        avantage_valeur: number;
        avantage_expiration: string;
    }>({
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

    // Charger les données d'une réservation existante
    useEffect(() => {
        if (!editingReservation) return;

        // Remplir le formulaire avec les données de la réservation
        setFormData({
            client_id: editingReservation.client_id,
            date_contact: editingReservation.date_contact || new Date().toISOString().slice(0, 16),
            date_reservation: editingReservation.date_reservation,
            date_retour: editingReservation.date_retour,
            livraison_necessaire: editingReservation.livraison_necessaire,
            adresse_livraison: editingReservation.adresse_livraison || '',
            contact_livraison: editingReservation.contact_livraison || '',
            creneau_livraison: editingReservation.creneau_livraison || '',
            recuperation_necessaire: editingReservation.recuperation_necessaire,
            adresse_recuperation: editingReservation.adresse_recuperation || '',
            contact_recuperation: editingReservation.contact_recuperation || '',
            creneau_recuperation: editingReservation.creneau_recuperation || '',
            prix_total_ttc: editingReservation.prix_total_ttc?.toString() || '',
            acompte_demande: editingReservation.acompte_demande,
            acompte_montant: editingReservation.acompte_montant?.toString() || '',
            acompte_paye_le: editingReservation.acompte_paye_le || '',
            paiement_final_le: editingReservation.paiement_final_le || '',
            statut: editingReservation.statut,
            raison_annulation: editingReservation.raison_annulation || '',
            commentaires: editingReservation.commentaires || '',
            items: editingReservation.items || [],
            selection: editingReservation.selection || [],
            payments: (editingReservation.payments || []).map((p) => ({
                ...p,
                amount: typeof p.amount === 'number' ? p.amount : parseFloat(p.amount) || 0,
                note: p.note || '',
            })),
        });

        // Remplir les données du client
        if (editingReservation.client) {
            setSelectedClient(editingReservation.client);
            setNewClientData({
                prenom: editingReservation.client.prenom || '',
                nom: editingReservation.client.nom || '',
                telephone: editingReservation.client.telephone || '',
                email: editingReservation.client.email || '',
                adresse: editingReservation.client.adresse || '',
                origine_contact: editingReservation.client.origine_contact || '',
                commentaires: editingReservation.client.commentaires || '',
                avantage_type: editingReservation.client.avantage_type || 'aucun',
                avantage_valeur: editingReservation.client.avantage_valeur || 0,
                avantage_expiration: editingReservation.client.avantage_expiration || '',
            });
        }
    }, [editingReservation]);

    // Synchroniser le formulaire avec la sélection du calendrier
    // En mode édition OU création, la sélection du calendrier doit être prise en compte
    useEffect(() => {
        if (!draft.isActive) return;

        setFormData((prev) => ({
            ...prev,
            date_reservation: selectors.globalMinDate || prev.date_reservation,
            date_retour: selectors.globalMaxDate || prev.date_retour,
            items: selectors.items.length > 0 ? selectors.items : prev.items,
            selection: selectors.selectedBikes,
        }));
    }, [draft.isActive, selectors.globalMinDate, selectors.globalMaxDate, selectors.items, selectors.selectedBikes]);

    // Récap des vélos sélectionnés (basé sur selectors)
    const selectedBikesRecap = useMemo(() => {
        return selectors.selectedBikes.map((bike) => bike.label);
    }, [selectors.selectedBikes]);

    // Calcul acompte suggéré (30%)
    const suggestedAcompte = useMemo(() => {
        const total = parseFloat(formData.prix_total_ttc) || 0;
        return (total * 0.3).toFixed(2);
    }, [formData.prix_total_ttc]);

    // Acompte payé (seulement si date de paiement renseignée)
    const acomptePaye = useMemo(() => {
        if (!formData.acompte_paye_le) return 0;
        return parseFloat(formData.acompte_montant) || 0;
    }, [formData.acompte_montant, formData.acompte_paye_le]);

    // Reste dû (ancien calcul avec acompte)
    const resteDu = useMemo(() => {
        const total = parseFloat(formData.prix_total_ttc) || 0;
        return (total - acomptePaye).toFixed(2);
    }, [formData.prix_total_ttc, acomptePaye]);

    // Calculs paiements
    const totalEncaisse = useMemo(() => {
        const paiements = formData.payments.reduce((sum, p) => sum + (p.amount || 0), 0);
        return paiements + acomptePaye;
    }, [formData.payments, acomptePaye]);

    const resteDuAvecPaiements = useMemo(() => {
        const total = parseFloat(formData.prix_total_ttc) || 0;
        return total - totalEncaisse;
    }, [formData.prix_total_ttc, totalEncaisse]);

    // Badge de paiement (vert si complet, rouge si trop perçu, orange si partiel)
    const paymentBadgeType = useMemo(() => {
        if (totalEncaisse === 0) return null;
        const total = parseFloat(formData.prix_total_ttc) || 0;
        if (totalEncaisse > total) return 'error';
        if (totalEncaisse === total) return 'success';
        return 'warning';
    }, [totalEncaisse, formData.prix_total_ttc]);

    // Fonctions de gestion des paiements
    const addPayment = useCallback(() => {
        const newPayment: PaymentLine = {
            amount: 0,
            method: 'cb',
            paid_at: new Date().toISOString().slice(0, 16),
            note: '',
        };
        setFormData((prev) => ({
            ...prev,
            payments: [...prev.payments, newPayment],
        }));
    }, []);

    const updatePayment = useCallback((index: number, field: keyof PaymentLine, value: string | number) => {
        setFormData((prev) => ({
            ...prev,
            payments: prev.payments.map((p, i) =>
                i === index ? { ...p, [field]: value } : p
            ),
        }));
    }, []);

    const removePayment = useCallback((index: number) => {
        setFormData((prev) => ({
            ...prev,
            payments: prev.payments.filter((_, i) => i !== index),
        }));
    }, []);

    // Envoi email demande d'acompte - utilise les données du formulaire directement
    const handleSendAcompteEmail = useCallback(async () => {
        const clientEmail = newClientData.email;
        const clientNom = `${newClientData.nom} ${newClientData.prenom}`.trim();

        if (!clientEmail || !formData.acompte_montant) return;

        setIsSendingAcompteEmail(true);
        setAcompteEmailMessage(null);

        try {
            // Toujours utiliser la route directe avec les données du formulaire
            const response = await fetch('/api/reservations/send-acompte-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    client_email: clientEmail,
                    client_nom: clientNom,
                    montant_acompte: parseFloat(formData.acompte_montant),
                    date_reservation: formData.date_reservation,
                    date_retour: formData.date_retour,
                }),
            });

            const data = await response.json();

            if (data.success) {
                setAcompteEmailMessage({ type: 'success', text: data.message });
            } else {
                setAcompteEmailMessage({ type: 'error', text: data.error || 'Erreur lors de l\'envoi' });
            }
        } catch {
            setAcompteEmailMessage({ type: 'error', text: 'Erreur de connexion' });
        } finally {
            setIsSendingAcompteEmail(false);
        }
    }, [newClientData.email, newClientData.nom, newClientData.prenom, formData.acompte_montant, formData.date_reservation, formData.date_retour]);

    // Peut envoyer email acompte si: acompte demandé, montant > 0, email dans le formulaire, dates renseignées
    const canSendAcompteEmail = useMemo(() => {
        return (
            formData.acompte_demande &&
            parseFloat(formData.acompte_montant) > 0 &&
            newClientData.email &&
            formData.date_reservation &&
            formData.date_retour
        );
    }, [formData.acompte_demande, formData.acompte_montant, newClientData.email, formData.date_reservation, formData.date_retour]);

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
            // Filtrer les paiements valides (montant > 0)
            const validPayments = formData.payments
                .filter((p) => p.amount > 0)
                .map((p) => ({
                    amount: p.amount,
                    method: p.method,
                    paid_at: p.paid_at,
                    note: p.note || null,
                }));

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
                color: draft.color,
                payments: validPayments,
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

            const isEditing = !!draft.editingReservationId;
            const url = isEditing
                ? `/api/reservations/${draft.editingReservationId}`
                : '/api/reservations';

            const response = await fetch(url, {
                method: isEditing ? 'PUT' : 'POST',
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

            setMessage({ type: 'success', text: isEditing ? 'Réservation mise à jour avec succès' : 'Réservation créée avec succès' });

            // Rafraîchir la page après un court délai pour voir le message de succès
            setTimeout(() => {
                router.reload({ only: ['reservations'] });
            }, 800);

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
            actions.cancelSelection();
            onSuccess?.();
        } catch (error) {
            console.error('Error saving reservation:', error);
            setMessage({ type: 'error', text: `Erreur lors de ${draft.editingReservationId ? 'la mise à jour' : 'la création'} de la réservation` });
        } finally {
            setIsSaving(false);
        }
    }, [formData, isNewClientValid, newClientData, onSuccess, draft.color, draft.editingReservationId, actions]);

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

            {/* Sélecteur de couleur - visible en mode sélection */}
            {draft.isActive && (
                <div className="reservation-form__color-row">
                    <span className="reservation-form__color-label">Couleur :</span>
                    <ColorPicker value={draft.color} onChange={actions.setColor} />
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

            {/* Avertissement vélos HS */}
            {selectors.hasHSBikes && (
                <div className="reservation-form__warning">
                    Attention : un ou plusieurs vélos sélectionnés sont marqués HS (hors service).
                </div>
            )}

            {/* Mode sélection actif */}
            {draft.isActive && selectors.selectedBikes.length === 0 && (
                <div className="reservation-form__help reservation-form__help--selection">
                    Cliquez sur les cellules du calendrier pour sélectionner les vélos et les dates.
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

                {formData.items.length === 0 && !draft.isActive && (
                    <div className="reservation-form__help">
                        Sélectionnez au moins un vélo pour enregistrer la réservation
                    </div>
                )}
                {errors.items && <span className="reservation-form__error">{errors.items}</span>}

                {/* Vélos sélectionnés depuis le calendrier - groupés par période */}
                {selectors.selectedBikes.length > 0 && (() => {
                    // Grouper les vélos par dates exactes (pas juste start/end)
                    const byPeriod = new Map<string, typeof selectors.selectedBikes>();
                    for (const bike of selectors.selectedBikes) {
                        // Utiliser le tableau dates complet comme clé (trié)
                        const sortedDates = [...bike.dates].sort();
                        const key = sortedDates.join(',');
                        const group = byPeriod.get(key) || [];
                        group.push(bike);
                        byPeriod.set(key, group);
                    }

                    return (
                        <div className="reservation-form__selected-bikes">
                            <h4 className="reservation-form__selected-bikes-title">Vélos sélectionnés</h4>
                            <div className="reservation-form__selected-bikes-list">
                                {Array.from(byPeriod.entries()).map(([periodKey, bikes]) => {
                                    // Récupérer les dates depuis le premier vélo du groupe
                                    const dates = bikes[0]?.dates || [];
                                    const startDate = dates[0] || '';
                                    const endDate = dates[dates.length - 1] || '';
                                    const days = dates.length;
                                    const hasHS = bikes.some((b) => b.is_hs);

                                    return (
                                        <div
                                            key={periodKey}
                                            className={`reservation-form__selected-bike ${hasHS ? 'reservation-form__selected-bike--hs' : ''}`}
                                        >
                                            <div className="reservation-form__selected-bike-info">
                                                <span className="reservation-form__selected-bike-dates">
                                                    {startDate} → {endDate} ({days} j.)
                                                </span>
                                                <span className="reservation-form__selected-bike-labels">
                                                    {bikes.map((b) => (
                                                        <span key={b.bike_id} className="reservation-form__bike-label-chip">
                                                            {b.label}
                                                            {b.is_hs && <span className="reservation-form__badge reservation-form__badge--hs">HS</span>}
                                                            <button
                                                                type="button"
                                                                className="reservation-form__bike-label-remove"
                                                                onClick={() => actions.removeBike(b.bike_id)}
                                                                title={`Retirer ${b.label}`}
                                                            >
                                                                ×
                                                            </button>
                                                        </span>
                                                    ))}
                                                </span>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    );
                })()}

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
                            onWheel={(e) => e.currentTarget.blur()}
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
                                    onWheel={(e) => e.currentTarget.blur()}
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

                        {/* Bouton envoi email demande d'acompte */}
                        {canSendAcompteEmail && !formData.acompte_paye_le && (
                            <div className="reservation-form__acompte-email">
                                <button
                                    type="button"
                                    className="reservation-form__btn reservation-form__btn--secondary"
                                    onClick={handleSendAcompteEmail}
                                    disabled={isSendingAcompteEmail}
                                >
                                    {isSendingAcompteEmail ? 'Envoi en cours...' : 'Envoyer demande d\'acompte'}
                                </button>
                                {acompteEmailMessage && (
                                    <span className={`reservation-form__${acompteEmailMessage.type === 'success' ? 'success' : 'error'}`}>
                                        {acompteEmailMessage.text}
                                    </span>
                                )}
                            </div>
                        )}
                        {!newClientData.email && formData.acompte_demande && parseFloat(formData.acompte_montant) > 0 && (
                            <span className="reservation-form__hint">
                                Renseignez l'email du client pour envoyer la demande
                            </span>
                        )}
                    </div>
                )}

                <div className="reservation-form__field">
                    <label>Statut *</label>
                    <select
                        value={formData.statut}
                        onChange={(e) => {
                            const newStatut = e.target.value as ReservationStatut;
                            setFormData((prev) => {
                                // Si on passe à 'paye' et qu'il n'y a pas encore de paiements, ajouter une première ligne
                                if (newStatut === 'paye' && prev.payments.length === 0) {
                                    return {
                                        ...prev,
                                        statut: newStatut,
                                        payments: [{
                                            amount: 0,
                                            method: 'cb',
                                            paid_at: new Date().toISOString().slice(0, 16),
                                            note: '',
                                        }],
                                    };
                                }
                                return { ...prev, statut: newStatut };
                            });
                        }}
                    >
                        {STATUT_OPTIONS.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                </div>

                {/* Section Paiements - visible uniquement si statut payé */}
                {formData.statut === 'paye' && (
                    <div className="reservation-form__payments">
                        <div className="reservation-form__payments-header">
                            <h4 className="reservation-form__payments-title">Suivi des paiements</h4>
                            <button
                                type="button"
                                className="reservation-form__btn reservation-form__btn--secondary reservation-form__btn--small"
                                onClick={addPayment}
                            >
                                + Ajouter un paiement
                            </button>
                        </div>

                        {/* Résumé encaissements */}
                        {formData.prix_total_ttc && (
                            <div className="reservation-form__payments-summary">
                                <span className={`reservation-form__payments-badge reservation-form__payments-badge--${paymentBadgeType || 'neutral'}`}>
                                    Total encaissé : {totalEncaisse.toFixed(2)} € / {parseFloat(formData.prix_total_ttc).toFixed(2)} €
                                </span>
                                {acomptePaye > 0 && (
                                    <span className="reservation-form__payments-badge reservation-form__payments-badge--neutral">
                                        dont acompte : {acomptePaye.toFixed(2)} €
                                    </span>
                                )}
                                {resteDuAvecPaiements > 0 && (
                                    <span className="reservation-form__payments-remaining">
                                        Reste à encaisser : {resteDuAvecPaiements.toFixed(2)} €
                                    </span>
                                )}
                                {resteDuAvecPaiements < 0 && (
                                    <span className="reservation-form__payments-overpaid">
                                        Trop perçu : {Math.abs(resteDuAvecPaiements).toFixed(2)} €
                                    </span>
                                )}
                            </div>
                        )}

                        {/* Liste des paiements */}
                        {formData.payments.length > 0 && (
                            <div className="reservation-form__payments-list">
                                {formData.payments.map((payment, index) => (
                                    <div key={index} className="reservation-form__payment-row">
                                        <div className="reservation-form__payment-fields">
                                            <div className="reservation-form__field reservation-form__field--small">
                                                <label>Montant *</label>
                                                <div className="reservation-form__input-with-suffix">
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        value={payment.amount || ''}
                                                        onChange={(e) => updatePayment(index, 'amount', parseFloat(e.target.value) || 0)}
                                                        onWheel={(e) => e.currentTarget.blur()}
                                                        placeholder="0.00"
                                                    />
                                                    <span className="reservation-form__suffix">€</span>
                                                </div>
                                            </div>
                                            <div className="reservation-form__field reservation-form__field--small">
                                                <label>Mode *</label>
                                                <select
                                                    value={payment.method}
                                                    onChange={(e) => updatePayment(index, 'method', e.target.value as PaymentMethod)}
                                                >
                                                    <option value="cb">CB</option>
                                                    <option value="liquide">Espèces</option>
                                                    <option value="cheque">Chèque</option>
                                                    <option value="virement">Virement</option>
                                                    <option value="autre">Autre</option>
                                                </select>
                                            </div>
                                            <div className="reservation-form__field reservation-form__field--small">
                                                <label>Date *</label>
                                                <input
                                                    type="datetime-local"
                                                    value={payment.paid_at}
                                                    onChange={(e) => updatePayment(index, 'paid_at', e.target.value)}
                                                />
                                            </div>
                                            <div className="reservation-form__field reservation-form__field--small">
                                                <label>Note</label>
                                                <input
                                                    type="text"
                                                    value={payment.note}
                                                    onChange={(e) => updatePayment(index, 'note', e.target.value)}
                                                    placeholder="Commentaire"
                                                />
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            className="reservation-form__payment-remove"
                                            onClick={() => removePayment(index)}
                                            title="Supprimer ce paiement"
                                        >
                                            ×
                                        </button>
                                    </div>
                                ))}
                            </div>
                        )}

                        {formData.payments.length === 0 && (
                            <div className="reservation-form__payments-empty">
                                Aucun paiement enregistré
                            </div>
                        )}
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
                        {formData.acompte_demande && formData.acompte_montant && formData.acompte_paye_le && (
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
            {!viewingMode && (
                <div className="reservation-form__footer">
                    {isReadyToSubmit && !isSaving && (
                        <div className="reservation-form__tag reservation-form__tag--success">
                            Prêt à confirmer
                        </div>
                    )}
                    <button
                        type="button"
                        className={`reservation-form__btn reservation-form__btn--primary reservation-form__btn--large ${isSaving ? 'reservation-form__btn--loading' : ''}`}
                        onClick={handleSubmit}
                        disabled={isSaving || !isReadyToSubmit}
                    >
                        {isSaving ? (
                            <>
                                <span className="reservation-form__spinner" />
                                Enregistrement en cours...
                            </>
                        ) : draft.editingReservationId ? (
                            'Mettre à jour la réservation'
                        ) : (
                            'Enregistrer la réservation'
                        )}
                    </button>
                </div>
            )}
        </div>
    );
}
