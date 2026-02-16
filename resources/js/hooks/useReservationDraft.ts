import { useState, useCallback, useMemo } from 'react';
import type {
    SelectedCell,
    SelectionBike,
    BikesByPeriod,
    ReservationDraft,
    ReservationDraftActions,
    ReservationDraftSelectors,
    ReservationItem,
    BikeDefinition,
    ReservationColorIndex,
    LoadedReservation,
} from '@/types';

function generateDraftId(): string {
    return `draft-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;
}

function cellKey(bikeId: string, date: string): string {
    return `${bikeId}:${date}`;
}

function bikeToTypeId(bike: BikeDefinition): string {
    return `${bike.category}_${bike.size}${bike.frame_type}`;
}

/**
 * Génère l'étiquette courte d'un vélo
 * VAE = E (électrique), VTC = pas de préfixe
 * Exemples: vae-m-01 → EMb1, vtc-l-03 → Lb3
 */
function bikeToLabel(bike: BikeDefinition): string {
    const prefix = bike.category === 'VAE' ? 'E' : '';
    const size = bike.size;
    const frame = bike.frame_type;
    // Extraire le numéro de l'id (vae-m-01 → 1)
    const parts = bike.id.split('-');
    const num = parts.length >= 3 ? parseInt(parts[2], 10) : 0;
    return `${prefix}${size}${frame}${num}`;
}

interface UseReservationDraftOptions {
    bikes: BikeDefinition[];
}

interface UseReservationDraftReturn {
    draft: ReservationDraft;
    actions: ReservationDraftActions;
    selectors: ReservationDraftSelectors;
}

export function useReservationDraft({ bikes }: UseReservationDraftOptions): UseReservationDraftReturn {
    const [draft, setDraft] = useState<ReservationDraft>({
        id: generateDraftId(),
        cells: new Map(),
        isActive: false,
        color: 0,
        editingReservationId: null,
    });

    const bikesMap = useMemo(() => {
        const map = new Map<string, BikeDefinition>();
        bikes.forEach((bike) => map.set(bike.id, bike));
        return map;
    }, [bikes]);

    const startSelection = useCallback(() => {
        setDraft({
            id: generateDraftId(),
            cells: new Map(),
            isActive: true,
            color: 0,
            editingReservationId: null,
        });
    }, []);

    const cancelSelection = useCallback(() => {
        setDraft((prev) => ({
            ...prev,
            cells: new Map(),
            isActive: false,
            editingReservationId: null,
        }));
    }, []);

    const toggleCell = useCallback((cell: SelectedCell) => {
        setDraft((prev) => {
            if (!prev.isActive) return prev;

            const newCells = new Map(prev.cells);
            const key = cellKey(cell.bikeId, cell.date);

            if (newCells.has(key)) {
                newCells.delete(key);
            } else {
                newCells.set(key, cell);
            }

            return { ...prev, cells: newCells };
        });
    }, []);

    const removeBike = useCallback((bikeId: string) => {
        setDraft((prev) => {
            const newCells = new Map(prev.cells);
            for (const [key, cell] of newCells) {
                if (cell.bikeId === bikeId) {
                    newCells.delete(key);
                }
            }
            return { ...prev, cells: newCells };
        });
    }, []);

    const clearSelection = useCallback(() => {
        setDraft((prev) => ({
            ...prev,
            cells: new Map(),
        }));
    }, []);

    const setColor = useCallback((color: ReservationColorIndex) => {
        setDraft((prev) => ({
            ...prev,
            color,
        }));
    }, []);

    const loadReservation = useCallback((reservation: LoadedReservation) => {
        // Reconstruire les cellules à partir de la sélection
        const newCells = new Map<string, SelectedCell>();

        for (const bike of reservation.selection) {
            for (const date of bike.dates) {
                const key = cellKey(bike.bike_id, date);
                newCells.set(key, {
                    bikeId: bike.bike_id,
                    date,
                    isHS: bike.is_hs,
                });
            }
        }

        setDraft({
            id: generateDraftId(),
            cells: newCells,
            isActive: true,
            color: reservation.color as ReservationColorIndex,
            editingReservationId: reservation.id,
        });
    }, []);

    const selectors = useMemo((): ReservationDraftSelectors => {
        const cellsArray = Array.from(draft.cells.values());

        if (cellsArray.length === 0) {
            return {
                selectedBikes: [],
                globalMinDate: null,
                globalMaxDate: null,
                hasHSBikes: false,
                selectedBikeIds: new Set(),
                items: [],
            };
        }

        // Grouper par vélo
        const bikeGroups = new Map<string, SelectedCell[]>();
        for (const cell of cellsArray) {
            const existing = bikeGroups.get(cell.bikeId) || [];
            existing.push(cell);
            bikeGroups.set(cell.bikeId, existing);
        }

        // Construire selectedBikes
        const selectedBikes: SelectionBike[] = [];
        let globalMin: string | null = null;
        let globalMax: string | null = null;
        let hasHS = false;

        for (const [bikeId, cells] of bikeGroups) {
            const bike = bikesMap.get(bikeId);
            const dates = cells.map((c) => c.date).sort();
            const startDate = dates[0];
            const endDate = dates[dates.length - 1];
            const isHS = cells.some((c) => c.isHS);

            if (isHS) hasHS = true;

            selectedBikes.push({
                bike_id: bikeId,
                label: bike ? bikeToLabel(bike) : bikeId,
                start_date: startDate,
                end_date: endDate,
                dates,
                is_hs: isHS,
            });

            if (!globalMin || startDate < globalMin) globalMin = startDate;
            if (!globalMax || endDate > globalMax) globalMax = endDate;
        }

        // Construire items (groupés par bike_type)
        const typeQuantities = new Map<string, number>();
        for (const [bikeId] of bikeGroups) {
            const bike = bikesMap.get(bikeId);
            if (bike) {
                const typeId = bikeToTypeId(bike);
                typeQuantities.set(typeId, (typeQuantities.get(typeId) || 0) + 1);
            }
        }

        const items: ReservationItem[] = Array.from(typeQuantities.entries()).map(
            ([bike_type_id, quantite]) => ({ bike_type_id, quantite })
        );

        return {
            selectedBikes,
            globalMinDate: globalMin,
            globalMaxDate: globalMax,
            hasHSBikes: hasHS,
            selectedBikeIds: new Set(bikeGroups.keys()),
            items,
        };
    }, [draft.cells, bikesMap]);

    const actions: ReservationDraftActions = useMemo(
        () => ({
            startSelection,
            cancelSelection,
            toggleCell,
            removeBike,
            clearSelection,
            setColor,
            loadReservation,
        }),
        [startSelection, cancelSelection, toggleCell, removeBike, clearSelection, setColor, loadReservation]
    );

    return { draft, actions, selectors };
}
