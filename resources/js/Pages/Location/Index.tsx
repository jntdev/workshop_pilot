import { Head, router } from '@inertiajs/react';
import { useMemo, useRef, useCallback, useEffect, useState } from 'react';
import {
    createColumnHelper,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { useVirtualizer } from '@tanstack/react-virtual';
import MainLayout from '@/Layouts/MainLayout';
import ReservationForm from '@/Components/Location/ReservationForm';
import ColorPicker from '@/Components/Location/ColorPicker';
import PlanningPanel, { type PlanningReservation } from '@/Components/Location/PlanningPanel';
import SettingsPanel from '@/Components/Location/SettingsPanel';
import LocationSyncOverlay from '@/Components/Location/LocationSyncOverlay';
import AgendaDriftBanner from '@/Components/Location/AgendaDriftBanner';
import { useReservationDraft } from '@/hooks/useReservationDraft';
import { useAgendaStore } from '@/hooks/useAgendaStore';
import { useAgendaVersionWatcher } from '@/hooks/useAgendaVersionWatcher';
import { useOptimisticMutation } from '@/hooks/useOptimisticMutation';
import type { BikeDefinition, DayInfo, LocationPageProps, LoadedReservation, ReservationColorIndex } from '@/types';
import { generateYearDays, formatDayHeader } from '@/utils/calendar';

// Mode d'affichage du panneau latéral
type SidePanelMode = 'closed' | 'reservation' | 'planning' | 'settings';

interface RowData extends DayInfo {
    [bikeId: string]: string | number | boolean | undefined;
}

// Info d'une cellule réservée
interface ReservedCellInfo {
    reservationId: number;
    color: ReservationColorIndex;
    clientName: string;
    clientLastName: string;
    statut: string;
    isFirstCell: boolean; // Première cellule de cette réservation pour ce vélo
}

const columnHelper = createColumnHelper<RowData>();

export default function LocationIndex({ bikes, bikeCategories, bikeSizes, year, reservations: initialReservations, openAgenda, agendaVersion }: LocationPageProps) {
    const tableContainerRef = useRef<HTMLDivElement>(null);

    // Initialize AgendaStore with server data
    const {
        data: agendaData,
        version: currentVersion,
        isLoading: isAgendaLoading,
        source: agendaSource,
    } = useAgendaStore(agendaVersion, {
        bikes,
        bikeCategories,
        bikeSizes,
        reservations: initialReservations,
    });

    // Watch for version drift (multi-tab sync)
    const {
        hasDrift,
        isRefreshing: isDriftRefreshing,
        dismissDrift,
    } = useAgendaVersionWatcher({ enabled: true });

    // Use data from store or fallback to props
    const activeBikes = agendaData?.bikes ?? bikes;
    const activeCategories = agendaData?.bikeCategories ?? bikeCategories;
    const activeSizes = agendaData?.bikeSizes ?? bikeSizes;

    const { draft, actions, selectors } = useReservationDraft({ bikes: activeBikes });

    // Optimistic mutation hook
    const { updateReservation } = useOptimisticMutation();

    // État local des réservations - sourced from AgendaStore
    const [reservations, setReservations] = useState<LoadedReservation[]>(
        agendaData?.reservations ?? initialReservations
    );

    // Synchroniser avec l'AgendaStore quand les données changent
    useEffect(() => {
        if (agendaData?.reservations) {
            setReservations(agendaData.reservations);
        }
    }, [agendaData?.reservations]);

    // État pour la réservation en cours d'édition (mode formulaire)
    const [editingReservation, setEditingReservation] = useState<LoadedReservation | null>(null);

    // État pour la réservation en mode visualisation (highlight sans édition)
    const [viewingReservationId, setViewingReservationId] = useState<number | null>(null);

    // État pour le mode du panneau latéral
    const [sidePanelMode, setSidePanelMode] = useState<SidePanelMode>('closed');

    // État pour le planning journalier
    const [planningDate, setPlanningDate] = useState<string>(new Date().toISOString().split('T')[0]);
    const [planningData, setPlanningData] = useState<{ departures: PlanningReservation[]; returns: PlanningReservation[] }>({
        departures: [],
        returns: [],
    });
    const [isLoadingPlanning, setIsLoadingPlanning] = useState(false);

    // État pour le drag selection
    const [isDragging, setIsDragging] = useState(false);
    const dragCellsRef = useRef<Set<string>>(new Set());

    // État pour le vélo sélectionné (affichage des infos dans la bannière)
    const [selectedBike, setSelectedBike] = useState<BikeDefinition | null>(null);

    // Index des réservations par ID pour accès rapide
    const reservationsById = useMemo(() => {
        const map = new Map<number, LoadedReservation>();
        for (const reservation of reservations) {
            map.set(reservation.id, reservation);
        }
        return map;
    }, [reservations]);

    const handleColumnHover = useCallback((bikeId: string | null) => {
        const container = tableContainerRef.current;
        if (!container) return;

        // Retirer le highlight précédent
        container.querySelectorAll('[data-column-hovered="true"]').forEach((el) => {
            el.removeAttribute('data-column-hovered');
        });

        // Ajouter le highlight sur la nouvelle colonne
        if (bikeId) {
            container.querySelectorAll(`[data-bike-id="${bikeId}"]`).forEach((el) => {
                el.setAttribute('data-column-hovered', 'true');
            });
        }
    }, []);

    // Gestionnaire pour sélectionner un vélo (clic sur le header)
    const handleBikeHeaderClick = useCallback((bike: BikeDefinition) => {
        // Toggle : si on clique sur le même vélo, on désélectionne
        if (selectedBike?.id === bike.id) {
            setSelectedBike(null);
        } else {
            setSelectedBike(bike);
            // Désélectionner la réservation si on sélectionne un vélo
            setViewingReservationId(null);
            setEditingReservation(null);
        }
    }, [selectedBike]);

    const days = useMemo(() => generateYearDays(year), [year]);

    // Index du jour actuel dans le tableau
    const todayIndex = useMemo(() => {
        return days.findIndex((day) => day.isToday);
    }, [days]);

    // Index des cellules réservées : Map<"bikeId:date", ReservedCellInfo>
    const reservedCellsIndex = useMemo(() => {
        const index = new Map<string, ReservedCellInfo>();

        for (const reservation of reservations) {
            // Extraire le nom de famille (dernier mot du nom complet)
            const nameParts = reservation.client_name.split(' ');
            const lastName = nameParts.length > 1 ? nameParts[nameParts.length - 1] : reservation.client_name;

            // Parcourir la sélection de chaque réservation
            for (const bike of reservation.selection) {
                // Trier les dates pour identifier la première
                const sortedDates = [...bike.dates].sort();
                const firstDate = sortedDates[0];

                // Pour chaque date du vélo
                for (const date of bike.dates) {
                    const key = `${bike.bike_id}:${date}`;
                    index.set(key, {
                        reservationId: reservation.id,
                        color: reservation.color as ReservationColorIndex,
                        clientName: reservation.client_name,
                        clientLastName: lastName,
                        statut: reservation.statut,
                        isFirstCell: date === firstDate,
                    });
                }
            }
        }

        return index;
    }, [reservations]);

    // Index des cellules de la réservation en mode visualisation
    const viewingCellsIndex = useMemo(() => {
        if (!viewingReservationId) return new Set<string>();

        const reservation = reservationsById.get(viewingReservationId);
        if (!reservation) return new Set<string>();

        const cells = new Set<string>();
        for (const bike of reservation.selection) {
            for (const date of bike.dates) {
                cells.add(`${bike.bike_id}:${date}`);
            }
        }
        return cells;
    }, [viewingReservationId, reservationsById]);

    const rowData: RowData[] = useMemo(() => {
        return days.map((day) => {
            const row: RowData = { ...day };
            activeBikes.forEach((bike) => {
                row[bike.column_id] = 'available';
            });
            return row;
        });
    }, [days, activeBikes]);

    const handleCellClick = useCallback((date: string, bikeId: string, isHS: boolean) => {
        if (!draft.isActive) return;
        actions.toggleCell({ bikeId, date, isHS });
    }, [draft.isActive, actions]);

    // Gestionnaire pour visualiser et éditer une réservation existante
    const handleReservationView = useCallback((reservationId: number) => {
        const reservation = reservationsById.get(reservationId);
        if (reservation) {
            // Toggle : si on clique sur la même réservation, on désactive la visualisation
            if (viewingReservationId === reservationId) {
                setViewingReservationId(null);
                setEditingReservation(null);
                if (draft.isActive) {
                    actions.cancelSelection();
                }
            } else {
                // Mode édition avec highlight dans l'agenda
                setViewingReservationId(reservationId);
                setEditingReservation(reservation);
                actions.loadReservation(reservation);
            }
        }
    }, [reservationsById, viewingReservationId, draft.isActive, actions]);

    // Gestionnaire pour changer la couleur d'une réservation directement
    const handleQuickColorChange = useCallback((newColor: ReservationColorIndex) => {
        if (!editingReservation || !viewingReservationId) return;

        const reservationId = viewingReservationId;

        // Fermer la visualisation immédiatement
        setViewingReservationId(null);
        setEditingReservation(null);

        // Optimistic update avec rollback automatique si erreur
        updateReservation(reservationId, { color: newColor }, { color: newColor });
    }, [editingReservation, viewingReservationId, updateReservation]);

    // Drag selection handlers
    const handleCellMouseDown = useCallback((date: string, bikeId: string, isHS: boolean, reservationId?: number) => {
        // Si pas en mode sélection
        if (!draft.isActive) {
            // Si clic sur une cellule réservée, highlight cette réservation
            if (reservationId) {
                handleReservationView(reservationId);
            } else {
                // Si clic sur une cellule vide et qu'on a une réservation highlighted, la désélectionner
                if (viewingReservationId) {
                    setViewingReservationId(null);
                    setEditingReservation(null);
                }
            }
            return;
        }

        // Mode sélection active
        setIsDragging(true);
        dragCellsRef.current = new Set([`${bikeId}:${date}`]);
        actions.toggleCell({ bikeId, date, isHS });
    }, [draft.isActive, actions, handleReservationView, viewingReservationId]);

    const handleCellMouseEnter = useCallback((date: string, bikeId: string, isHS: boolean) => {
        if (!draft.isActive || !isDragging) return;
        const key = `${bikeId}:${date}`;
        if (!dragCellsRef.current.has(key)) {
            dragCellsRef.current.add(key);
            actions.toggleCell({ bikeId, date, isHS });
        }
    }, [draft.isActive, isDragging, actions]);

    const handleMouseUp = useCallback(() => {
        setIsDragging(false);
        dragCellsRef.current = new Set();
    }, []);

    // Écouter mouseup au niveau du document pour gérer le cas où on relâche en dehors
    useEffect(() => {
        document.addEventListener('mouseup', handleMouseUp);
        return () => document.removeEventListener('mouseup', handleMouseUp);
    }, [handleMouseUp]);

    // Écouter la touche Escape pour fermer le panneau ou la bannière vélo
    useEffect(() => {
        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                // Fermer d'abord la bannière vélo si elle est ouverte
                if (selectedBike) {
                    setSelectedBike(null);
                    return;
                }
                // Sinon fermer le panneau latéral
                if (sidePanelMode === 'planning') {
                    setSidePanelMode('closed');
                } else if (sidePanelMode === 'reservation') {
                    setViewingReservationId(null);
                    setEditingReservation(null);
                    if (draft.isActive) {
                        actions.cancelSelection();
                    }
                    setSidePanelMode('closed');
                }
            }
        };

        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [sidePanelMode, draft.isActive, actions, selectedBike]);

    // Charger les données du planning pour une date
    const loadPlanningData = useCallback(async (date: string) => {
        setIsLoadingPlanning(true);
        try {
            const response = await fetch(`/api/location/planning?date=${date}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            if (response.ok) {
                const data = await response.json();
                setPlanningData(data);
            }
        } catch (error) {
            console.error('Erreur lors du chargement du planning:', error);
        } finally {
            setIsLoadingPlanning(false);
        }
    }, []);

    // Ouvrir/fermer le planning (toggle)
    const handleTogglePlanning = useCallback(() => {
        if (sidePanelMode === 'planning') {
            setSidePanelMode('closed');
        } else {
            const today = new Date().toISOString().split('T')[0];
            setPlanningDate(today);
            setSidePanelMode('planning');
            setViewingReservationId(null);
            setEditingReservation(null);
            loadPlanningData(today);
        }
    }, [sidePanelMode, loadPlanningData]);

    // Changer la date du planning
    const handlePlanningDateChange = useCallback((newDate: string) => {
        setPlanningDate(newDate);
        loadPlanningData(newDate);
    }, [loadPlanningData]);

    // Fermer le planning
    const handleClosePlanning = useCallback(() => {
        setSidePanelMode('closed');
    }, []);

    // Clic sur une réservation depuis le planning
    const handlePlanningReservationClick = useCallback((reservationId: number) => {
        const reservation = reservationsById.get(reservationId);
        if (reservation) {
            setViewingReservationId(reservationId);
            setEditingReservation(reservation);
            setSidePanelMode('reservation');
        }
    }, [reservationsById]);

    // Ouvrir automatiquement l'agenda si demandé via props (au montage uniquement)
    useEffect(() => {
        if (openAgenda) {
            const today = new Date().toISOString().split('T')[0];
            setPlanningDate(today);
            setSidePanelMode('planning');
            loadPlanningData(today);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // Mettre à jour le sidePanelMode quand on entre en mode édition/visualisation
    useEffect(() => {
        if (draft.isActive || viewingReservationId) {
            setSidePanelMode('reservation');
        }
    }, [draft.isActive, viewingReservationId]);

    // Référence pour stocker le virtualizer (sera défini plus tard)
    const rowVirtualizerRef = useRef<ReturnType<typeof useVirtualizer<HTMLDivElement>> | null>(null);

    const columns = useMemo(() => {
        const dateColumn = columnHelper.accessor('date', {
            id: 'date',
            header: () => <span className="location-table__header-date">Date</span>,
            cell: (info) => {
                const day = info.row.original;
                return (
                    <div className={`location-table__date-cell ${day.isWeekend ? 'location-table__date-cell--weekend' : ''}`}>
                        <span className="location-table__date-label">
                            {formatDayHeader(day)}
                        </span>
                    </div>
                );
            },
            size: 100,
        });

        const bikeColumns: ReturnType<typeof columnHelper.accessor>[] = [];

        activeBikes.forEach((bike, index) => {
            const prevBike = index > 0 ? activeBikes[index - 1] : null;
            const isNewCategory = prevBike && prevBike.category?.name !== bike.category?.name;
            const isNewSize = prevBike && prevBike.size?.name !== bike.size?.name && !isNewCategory;

            // Ajouter une colonne spacer entre les catégories
            if (isNewCategory) {
                bikeColumns.push(
                    columnHelper.accessor(() => '', {
                        id: `spacer-${bike.category}`,
                        header: () => <div className="location-table__spacer" />,
                        cell: () => <div className="location-table__spacer" />,
                        size: 120,
                    })
                );
            }

            let separatorClass = '';
            if (isNewSize) {
                separatorClass = 'location-table__separator--size';
            } else if (prevBike && !isNewCategory) {
                separatorClass = 'location-table__separator--bike';
            }

            bikeColumns.push(columnHelper.accessor((row) => row[bike.column_id], {
                id: bike.column_id,
                header: () => (
                    <div
                        className={`location-table__header-bike location-table__header-bike--frame-${bike.frame_type} ${bike.status === 'HS' ? 'location-table__header-bike--hs' : 'location-table__header-bike--ok'} ${separatorClass} ${selectedBike?.id === bike.id ? 'location-table__header-bike--selected' : ''}`}
                        title={`${bike.category?.name} ${bike.size?.name} ${bike.frame_type === 'b' ? 'cadre bas' : 'cadre haut'}`}
                        data-bike-id={bike.column_id}
                        data-status={bike.status}
                        onMouseEnter={() => handleColumnHover(bike.column_id)}
                        onMouseLeave={() => handleColumnHover(null)}
                        onClick={() => handleBikeHeaderClick(bike)}
                    >
                        <span className="location-table__header-name">{bike.name}</span>
                    </div>
                ),
                cell: (info) => {
                    const day = info.row.original;
                    const isHS = bike.status === 'HS';
                    const cellKeyStr = `${bike.column_id}:${day.date}`;
                    const isSelected = draft.cells.has(cellKeyStr);
                    const isSelectionMode = draft.isActive;
                    const isViewing = viewingCellsIndex.has(cellKeyStr);

                    // En mode visualisation, dimmer les cellules qui ne font pas partie de la réservation
                    const isViewingActive = viewingReservationId !== null;
                    const isDimmed = isViewingActive && !isViewing;

                    // Vérifier si cette cellule est réservée
                    const reservedInfo = reservedCellsIndex.get(cellKeyStr);
                    const isReserved = !!reservedInfo;

                    // Déterminer la couleur à afficher
                    let cellColor: number | undefined;
                    if (isSelected) {
                        cellColor = draft.color;
                    } else if (isReserved) {
                        cellColor = reservedInfo.color;
                    }

                    // Afficher le nom seulement sur la première cellule de la réservation
                    const showClientName = isReserved && reservedInfo.isFirstCell && !isSelected;

                    return (
                        <div
                            className={`location-table__cell ${separatorClass} ${day.isToday ? 'location-table__cell--today' : ''} ${isHS ? 'location-table__cell--hs' : ''} ${isSelected ? 'location-table__cell--selected' : ''} ${isReserved && !isSelected ? 'location-table__cell--reserved' : ''} ${isViewing ? 'location-table__cell--viewing' : ''} ${isDimmed ? 'location-table__cell--dimmed' : ''} ${isSelectionMode ? 'location-table__cell--selectable' : ''} ${reservedInfo?.statut === 'en_attente_acompte' ? 'location-table__cell--pending-acompte' : ''}`}
                            data-bike-id={bike.column_id}
                            data-status={bike.status}
                            data-color={cellColor}
                            title={reservedInfo ? `${reservedInfo.clientName} (${reservedInfo.statut})` : undefined}
                            onMouseDown={() => handleCellMouseDown(day.date, bike.column_id, isHS, reservedInfo?.reservationId)}
                            onMouseEnter={() => {
                                handleColumnHover(bike.column_id);
                                handleCellMouseEnter(day.date, bike.column_id, isHS);
                            }}
                            onMouseLeave={() => handleColumnHover(null)}
                        >
                            <div className="location-table__cell-banner" />
                            <div className="location-table__cell-content">
                                {showClientName && (
                                    <span className="location-table__cell-label">{reservedInfo.clientLastName}</span>
                                )}
                            </div>
                        </div>
                    );
                },
                size: 40,
            }));
        });

        return [dateColumn, ...bikeColumns];
    }, [activeBikes, handleColumnHover, handleCellMouseDown, handleCellMouseEnter, handleBikeHeaderClick, draft.cells, draft.isActive, draft.color, reservedCellsIndex, viewingCellsIndex, viewingReservationId, selectedBike]);

    const table = useReactTable({
        data: rowData,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    const { rows } = table.getRowModel();

    const rowVirtualizer = useVirtualizer({
        count: rows.length,
        getScrollElement: () => tableContainerRef.current,
        estimateSize: () => 24,
        overscan: 10,
    });

    const virtualRows = rowVirtualizer.getVirtualItems();
    const totalSize = rowVirtualizer.getTotalSize();

    const paddingTop = virtualRows.length > 0 ? virtualRows[0].start : 0;
    const paddingBottom =
        virtualRows.length > 0
            ? totalSize - virtualRows[virtualRows.length - 1].end
            : 0;

    // Calcul des colspans pour la bande de catégorie (avec spacers)
    const categoryBands = useMemo(() => {
        const bands: { category: string; color: string; colspan: number; isSpacer?: boolean }[] = [];
        let currentCategory = '';
        let currentColor = '';
        let currentColspan = 0;

        activeBikes.forEach((bike) => {
            const categoryName = bike.category?.name ?? '';
            const categoryColor = bike.category?.color ?? '#888888';

            if (categoryName !== currentCategory) {
                if (currentCategory) {
                    bands.push({ category: currentCategory, color: currentColor, colspan: currentColspan });
                    // Ajouter un spacer après chaque catégorie (sauf la dernière)
                    bands.push({ category: 'spacer', color: '', colspan: 1, isSpacer: true });
                }
                currentCategory = categoryName;
                currentColor = categoryColor;
                currentColspan = 1;
            } else {
                currentColspan++;
            }
        });

        if (currentCategory) {
            bands.push({ category: currentCategory, color: currentColor, colspan: currentColspan });
        }

        return bands;
    }, [activeBikes]);

    // Calcul des colspans pour la bande de taille (avec spacers entre catégories)
    const sizeBands = useMemo(() => {
        const bands: { size: string; color: string; category: string; colspan: number; isSpacer?: boolean }[] = [];
        let currentCategory = '';
        let currentSize = '';
        let currentColor = '';
        let currentColspan = 0;

        activeBikes.forEach((bike) => {
            const categoryName = bike.category?.name ?? '';
            const sizeName = bike.size?.name ?? '';
            const sizeColor = bike.size?.color ?? '#888888';

            // Changement de catégorie = spacer + nouvelle taille
            if (categoryName !== currentCategory) {
                if (currentCategory) {
                    bands.push({ size: currentSize, color: currentColor, category: currentCategory, colspan: currentColspan });
                    // Ajouter un spacer après chaque catégorie
                    bands.push({ size: 'spacer', color: '', category: '', colspan: 1, isSpacer: true });
                }
                currentCategory = categoryName;
                currentSize = sizeName;
                currentColor = sizeColor;
                currentColspan = 1;
            } else if (sizeName !== currentSize) {
                // Même catégorie mais taille différente
                bands.push({ size: currentSize, color: currentColor, category: currentCategory, colspan: currentColspan });
                currentSize = sizeName;
                currentColor = sizeColor;
                currentColspan = 1;
            } else {
                currentColspan++;
            }
        });

        if (currentSize) {
            bands.push({ size: currentSize, color: currentColor, category: currentCategory, colspan: currentColspan });
        }

        return bands;
    }, [activeBikes]);

    // Stocker la référence du virtualizer
    rowVirtualizerRef.current = rowVirtualizer;

    // Scroll vers la date du jour au chargement
    useEffect(() => {
        if (todayIndex >= 0) {
            rowVirtualizer.scrollToIndex(todayIndex, { align: 'start' });
        }
    }, [todayIndex, rowVirtualizer]);

    return (
        <MainLayout>
            <Head title="Location" />

            {/* Drift detection banner */}
            <AgendaDriftBanner isVisible={hasDrift} onDismiss={dismissDrift} />

            <div id="location_calendar" className={`location ${sidePanelMode !== 'closed' ? 'location--panel-open' : ''} ${sidePanelMode === 'planning' ? 'location--panel-planning' : ''}`}>
                {/* Sync overlay during loading */}
                <LocationSyncOverlay isVisible={isAgendaLoading || isDriftRefreshing} />

                <div className="location__table-panel">
                    <div className="location__header">
                        <h1 className="location__title">
                            Disponibilités {year}
                        </h1>
                        <div className="location__header-actions">
                            <button
                                type="button"
                                className={`location__btn ${sidePanelMode === 'settings' ? 'location__btn--active' : 'location__btn--outline'}`}
                                onClick={() => setSidePanelMode(sidePanelMode === 'settings' ? 'closed' : 'settings')}
                            >
                                Reglages agenda
                            </button>
                            <button
                                type="button"
                                className={`location__btn ${sidePanelMode === 'planning' ? 'location__btn--active' : 'location__btn--outline'}`}
                                onClick={handleTogglePlanning}
                            >
                                {sidePanelMode === 'planning' ? 'Fermer planning' : 'Voir aujourd\'hui'}
                            </button>
                            {!draft.isActive ? (
                                <button
                                    type="button"
                                    className="location__btn location__btn--primary"
                                    onClick={() => {
                                        setViewingReservationId(null);
                                        setEditingReservation(null);
                                        setSidePanelMode('reservation');
                                        actions.startSelection();
                                    }}
                                >
                                    Nouvelle reservation
                                </button>
                            ) : (
                                <button
                                    type="button"
                                    className="location__btn location__btn--danger"
                                    onClick={() => {
                                        setViewingReservationId(null);
                                        setEditingReservation(null);
                                        setSidePanelMode('closed');
                                        actions.cancelSelection();
                                    }}
                                >
                                    Annuler la selection
                                </button>
                            )}
                        </div>
                        {editingReservation && viewingReservationId && !draft.isActive && (
                            <div className="location__viewing-info">
                                <ColorPicker
                                    value={editingReservation.color}
                                    onChange={handleQuickColorChange}
                                />
                                <span className="location__viewing-name">
                                    {editingReservation.client?.prenom} {editingReservation.client?.nom}
                                </span>
                                {editingReservation.client?.telephone && (
                                    <span className="location__viewing-phone">{editingReservation.client.telephone}</span>
                                )}
                            </div>
                        )}
                        {selectedBike && !viewingReservationId && !draft.isActive && (
                            <div className="location__viewing-info location__viewing-info--bike">
                                <span className={`location__bike-status location__bike-status--${selectedBike.status.toLowerCase()}`}>
                                    {selectedBike.status}
                                </span>
                                <span className="location__viewing-name">
                                    {selectedBike.name}
                                </span>
                                <span className="location__bike-type">
                                    {selectedBike.category?.name} {selectedBike.size?.name} {selectedBike.frame_type === 'b' ? 'cadre bas' : 'cadre haut'}
                                    {selectedBike.model && ` - ${selectedBike.model}`}
                                </span>
                                {selectedBike.battery_type && (
                                    <span className="location__bike-battery">
                                        Batterie {selectedBike.battery_type}
                                    </span>
                                )}
                                {selectedBike.notes && (
                                    <span className="location__bike-notes">{selectedBike.notes}</span>
                                )}
                                <button
                                    type="button"
                                    className="location__btn location__btn--icon location__btn--close-bike"
                                    onClick={() => setSelectedBike(null)}
                                    aria-label="Fermer"
                                >
                                    x
                                </button>
                            </div>
                        )}
                    </div>

                    <div
                        ref={tableContainerRef}
                        className="location-table__container"
                    >
                        <table className="location-table">
                            <thead className="location-table__head">
                                <tr className="location-table__category-row">
                                    <th className="location-table__th location-table__th--category-empty" style={{ width: 100 }} />
                                    {categoryBands.map((band, index) => (
                                        <th
                                            key={band.isSpacer ? `spacer-${index}` : band.category}
                                            className={band.isSpacer
                                                ? 'location-table__th location-table__th--category-spacer'
                                                : 'location-table__th location-table__th--category'
                                            }
                                            colSpan={band.colspan}
                                            style={band.isSpacer ? undefined : { backgroundColor: band.color }}
                                        >
                                            {band.isSpacer ? '' : band.category}
                                        </th>
                                    ))}
                                </tr>
                                <tr className="location-table__size-row">
                                    <th className="location-table__th location-table__th--size-empty" style={{ width: 100 }} />
                                    {sizeBands.map((band, index) => (
                                        <th
                                            key={band.isSpacer ? `spacer-size-${index}` : `${band.category}-${band.size}-${index}`}
                                            className={band.isSpacer
                                                ? 'location-table__th location-table__th--size-spacer'
                                                : 'location-table__th location-table__th--size'
                                            }
                                            colSpan={band.colspan}
                                            style={band.isSpacer ? undefined : { backgroundColor: band.color }}
                                        >
                                            {band.isSpacer ? '' : `Taille ${band.size}`}
                                        </th>
                                    ))}
                                </tr>
                                {table.getHeaderGroups().map((headerGroup) => (
                                    <tr key={headerGroup.id} className="location-table__header-row">
                                        {headerGroup.headers.map((header) => (
                                            <th
                                                key={header.id}
                                                className="location-table__th"
                                                style={{ width: header.getSize() }}
                                            >
                                                {header.isPlaceholder
                                                    ? null
                                                    : flexRender(
                                                          header.column.columnDef.header,
                                                          header.getContext()
                                                      )}
                                            </th>
                                        ))}
                                    </tr>
                                ))}
                            </thead>
                            <tbody className="location-table__body">
                                {paddingTop > 0 && (
                                    <tr>
                                        <td style={{ height: `${paddingTop}px` }} />
                                    </tr>
                                )}
                                {virtualRows.map((virtualRow) => {
                                    const row = rows[virtualRow.index];
                                    return (
                                        <tr
                                            key={row.id}
                                            className="location-table__row"
                                            data-index={virtualRow.index}
                                        >
                                            {row.getVisibleCells().map((cell) => (
                                                <td
                                                    key={cell.id}
                                                    className="location-table__td"
                                                    style={{ width: cell.column.getSize() }}
                                                >
                                                    {flexRender(
                                                        cell.column.columnDef.cell,
                                                        cell.getContext()
                                                    )}
                                                </td>
                                            ))}
                                        </tr>
                                    );
                                })}
                                {paddingBottom > 0 && (
                                    <tr>
                                        <td style={{ height: `${paddingBottom}px` }} />
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Panneau latéral contextuel */}
                <div className={`location__side-panel ${sidePanelMode !== 'closed' ? 'location__side-panel--open' : ''}`}>
                    {sidePanelMode === 'reservation' && (
                        <div className="location__form-panel">
                            <div className="location__form-header">
                                <h2 className="location__form-title">
                                    {draft.isActive && draft.editingReservationId
                                        ? `Réservation #${draft.editingReservationId}`
                                        : 'Nouvelle réservation'}
                                </h2>
                                <div className="location__form-actions">
                                    <button
                                        type="button"
                                        className="location__btn location__btn--icon"
                                        onClick={() => {
                                            setViewingReservationId(null);
                                            setEditingReservation(null);
                                            if (draft.isActive) {
                                                actions.cancelSelection();
                                            }
                                            setSidePanelMode('closed');
                                        }}
                                        aria-label="Fermer"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                            <div className="location__form-content">
                                <ReservationForm
                                    draft={draft}
                                    selectors={selectors}
                                    actions={actions}
                                    editingReservation={editingReservation}
                                />
                            </div>
                        </div>
                    )}

                    {sidePanelMode === 'planning' && (
                        <PlanningPanel
                            date={planningDate}
                            departures={planningData.departures}
                            returns={planningData.returns}
                            onDateChange={handlePlanningDateChange}
                            onClose={handleClosePlanning}
                            onReservationClick={handlePlanningReservationClick}
                        />
                    )}

                    {sidePanelMode === 'settings' && (
                        <SettingsPanel
                            categories={activeCategories}
                            sizes={activeSizes}
                            onClose={() => setSidePanelMode('closed')}
                            onUpdate={() => router.reload()}
                        />
                    )}
                </div>
            </div>
        </MainLayout>
    );
}
