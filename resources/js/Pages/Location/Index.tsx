import { Head } from '@inertiajs/react';
import { useMemo, useRef, useCallback, useEffect } from 'react';
import {
    createColumnHelper,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { useVirtualizer } from '@tanstack/react-virtual';
import MainLayout from '@/Layouts/MainLayout';
import ReservationForm from '@/Components/Location/ReservationForm';
import type { DayInfo, LocationPageProps } from '@/types';
import { generateYearDays, formatDayHeader } from '@/utils/calendar';

interface RowData extends DayInfo {
    [bikeId: string]: string | number | boolean | undefined;
}

const columnHelper = createColumnHelper<RowData>();

export default function LocationIndex({ bikes, bikeTypes, year }: LocationPageProps) {
    const tableContainerRef = useRef<HTMLDivElement>(null);

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

    const days = useMemo(() => generateYearDays(year), [year]);

    // Index du jour actuel dans le tableau
    const todayIndex = useMemo(() => {
        return days.findIndex((day) => day.isToday);
    }, [days]);

    const rowData: RowData[] = useMemo(() => {
        return days.map((day) => {
            const row: RowData = { ...day };
            bikes.forEach((bike) => {
                row[bike.id] = 'available';
            });
            return row;
        });
    }, [days, bikes]);

    const handleCellClick = useCallback((date: string, bikeId: string) => {
        console.log('Cell clicked:', { date, bikeId });
    }, []);

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

        bikes.forEach((bike, index) => {
            const prevBike = index > 0 ? bikes[index - 1] : null;
            const isNewCategory = prevBike && prevBike.category !== bike.category;
            const isNewSize = prevBike && prevBike.size !== bike.size && !isNewCategory;

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

            bikeColumns.push(columnHelper.accessor((row) => row[bike.id], {
                id: bike.id,
                header: () => (
                    <div
                        className={`location-table__header-bike ${bike.status === 'HS' ? 'location-table__header-bike--hs' : 'location-table__header-bike--ok'} ${separatorClass}`}
                        title={bike.notes || undefined}
                        data-bike-id={bike.id}
                        data-status={bike.status}
                        onMouseEnter={() => handleColumnHover(bike.id)}
                        onMouseLeave={() => handleColumnHover(null)}
                    >
                        <span className="location-table__header-category">{bike.category}</span>
                        <span className="location-table__header-size">{bike.size}{bike.frame_type}</span>
                    </div>
                ),
                cell: (info) => {
                    const day = info.row.original;
                    const isHS = bike.status === 'HS';
                    return (
                        <div
                            className={`location-table__cell ${separatorClass} ${day.isToday ? 'location-table__cell--today' : ''} ${isHS ? 'location-table__cell--hs' : ''}`}
                            data-bike-id={bike.id}
                            data-status={bike.status}
                            onClick={() => !isHS && handleCellClick(day.date, bike.id)}
                            onMouseEnter={() => handleColumnHover(bike.id)}
                            onMouseLeave={() => handleColumnHover(null)}
                        >
                            <div className="location-table__cell-banner" />
                            <div className="location-table__cell-content" />
                        </div>
                    );
                },
                size: 40,
            }));
        });

        return [dateColumn, ...bikeColumns];
    }, [bikes, handleCellClick, handleColumnHover]);

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
        const bands: { category: string; colspan: number; isSpacer?: boolean }[] = [];
        let currentCategory = '';
        let currentColspan = 0;

        bikes.forEach((bike) => {
            if (bike.category !== currentCategory) {
                if (currentCategory) {
                    bands.push({ category: currentCategory, colspan: currentColspan });
                    // Ajouter un spacer après chaque catégorie (sauf la dernière)
                    bands.push({ category: 'spacer', colspan: 1, isSpacer: true });
                }
                currentCategory = bike.category;
                currentColspan = 1;
            } else {
                currentColspan++;
            }
        });

        if (currentCategory) {
            bands.push({ category: currentCategory, colspan: currentColspan });
        }

        return bands;
    }, [bikes]);

    // Scroll vers la date du jour au chargement
    useEffect(() => {
        if (todayIndex >= 0) {
            rowVirtualizer.scrollToIndex(todayIndex, { align: 'start' });
        }
    }, [todayIndex, rowVirtualizer]);

    return (
        <MainLayout>
            <Head title="Location" />

            <div id="location_calendar" className="location">
                <div className="location__table-panel">
                    <div className="location__header">
                        <h1 className="location__title">Disponibilités {year}</h1>
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
                                                : `location-table__th location-table__th--category location-table__th--category-${band.category.toLowerCase()}`
                                            }
                                            colSpan={band.colspan}
                                        >
                                            {band.isSpacer ? '' : band.category}
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

                <div className="location__form-panel">
                    <div className="location__form-header">
                        <h2 className="location__form-title">Nouvelle réservation</h2>
                    </div>
                    <div className="location__form-content">
                        <ReservationForm bikeTypes={bikeTypes} />
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
