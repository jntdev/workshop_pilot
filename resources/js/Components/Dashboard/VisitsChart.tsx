import { useEffect, useMemo, useRef, useState, type PointerEvent } from 'react';

export type VisitsGranularity = 'day' | 'week';

export interface VisitsPoint {
    date: string;
    visitors: number;
    views: number;
}

interface VisitsChartProps {
    points: VisitsPoint[];
    granularity: VisitsGranularity;
}

const HEIGHT = 260;
const PADDING = { top: 16, right: 16, bottom: 28, left: 48 };
const MIN_X_LABEL_SPACING_PX = 72;
const MAX_Y_INTERVALS = 4;

const formatInteger = (value: number): string => new Intl.NumberFormat('fr-FR').format(value);

function toDate(iso: string): Date {
    return new Date(`${iso}T12:00:00`);
}

function formatAxisDate(iso: string): string {
    return toDate(iso).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
}

function formatPointLabel(iso: string, granularity: VisitsGranularity): string {
    return granularity === 'week'
        ? `Semaine du ${formatAxisDate(iso)}`
        : toDate(iso).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
}

/** Pas de graduation « rond » (1, 2 ou 5 × 10^n, entier) donnant au plus MAX_Y_INTERVALS intervalles. */
function niceStep(maxValue: number): number {
    const rough = Math.max(1, maxValue) / MAX_Y_INTERVALS;
    const magnitude = 10 ** Math.floor(Math.log10(rough));
    const step = [1, 2, 5, 10].map(multiplier => multiplier * magnitude).find(candidate => candidate >= rough) ?? 10 * magnitude;
    return Math.max(1, step);
}

export default function VisitsChart({ points, granularity }: VisitsChartProps) {
    const containerRef = useRef<HTMLDivElement>(null);
    const [width, setWidth] = useState(0);
    const [hoverIndex, setHoverIndex] = useState<number | null>(null);

    useEffect(() => {
        const element = containerRef.current;
        if (!element) return;

        const observer = new ResizeObserver(entries => setWidth(entries[0].contentRect.width));
        observer.observe(element);

        return () => observer.disconnect();
    }, []);

    const { yMax, yTicks } = useMemo(() => {
        const maxValue = Math.max(0, ...points.map(point => Math.max(point.visitors, point.views)));
        const step = niceStep(maxValue);
        const top = Math.max(step, Math.ceil(maxValue / step) * step);
        return { yMax: top, yTicks: Array.from({ length: Math.round(top / step) + 1 }, (_, i) => i * step) };
    }, [points]);

    const innerWidth = Math.max(0, width - PADDING.left - PADDING.right);
    const innerHeight = HEIGHT - PADDING.top - PADDING.bottom;
    const baseline = PADDING.top + innerHeight;
    const lastIndex = points.length - 1;
    // Le dernier point (jour ou semaine en cours) est incomplet : son segment est tracé en pointillés.
    const lastCompleteIndex = Math.max(0, lastIndex - 1);
    const labelEvery = Math.max(1, Math.ceil(points.length / Math.max(1, Math.floor(innerWidth / MIN_X_LABEL_SPACING_PX))));

    const xOf = (index: number): number => PADDING.left + (lastIndex === 0 ? innerWidth / 2 : (index * innerWidth) / lastIndex);
    const yOf = (value: number): number => baseline - (value / yMax) * innerHeight;

    const pathThrough = (values: number[], from: number, to: number): string =>
        values
            .slice(from, to + 1)
            .map((value, offset) => `${offset === 0 ? 'M' : 'L'}${xOf(from + offset)},${yOf(value)}`)
            .join(' ');

    const visitors = points.map(point => point.visitors);
    const views = points.map(point => point.views);
    const hovered = hoverIndex === null ? null : points[hoverIndex];
    const markedIndex = hoverIndex ?? (lastIndex === 0 ? 0 : null);
    const marked = markedIndex === null ? null : points[markedIndex];

    const handlePointerMove = (event: PointerEvent<SVGRectElement>) => {
        const bounds = event.currentTarget.getBoundingClientRect();
        const ratio = bounds.width === 0 ? 0 : (event.clientX - bounds.left) / bounds.width;
        setHoverIndex(Math.min(lastIndex, Math.max(0, Math.round(ratio * lastIndex))));
    };

    return (
        <div className="visits-chart" ref={containerRef}>
            {width > 0 && (
                <svg width={width} height={HEIGHT} role="img" aria-label="Courbe des visiteurs et des pages vues sur la période">
                    {yTicks.map(tick => (
                        <g key={tick}>
                            <line className="visits-chart__grid-line" x1={PADDING.left} x2={PADDING.left + innerWidth} y1={yOf(tick)} y2={yOf(tick)} />
                            <text className="visits-chart__axis-label" x={PADDING.left - 8} y={yOf(tick)} textAnchor="end" dominantBaseline="middle">
                                {formatInteger(tick)}
                            </text>
                        </g>
                    ))}

                    {points.map((point, index) => index % labelEvery === 0 && (
                        <text
                            key={point.date}
                            className="visits-chart__axis-label"
                            x={xOf(index)}
                            y={HEIGHT - 8}
                            textAnchor={lastIndex > 0 && index === 0 ? 'start' : lastIndex > 0 && index === lastIndex ? 'end' : 'middle'}
                        >
                            {formatAxisDate(point.date)}
                        </text>
                    ))}

                    <path className="visits-chart__area" d={`${pathThrough(visitors, 0, lastIndex)} L${xOf(lastIndex)},${baseline} L${xOf(0)},${baseline} Z`} />
                    <path className="visits-chart__line visits-chart__line--views" d={pathThrough(views, 0, lastCompleteIndex)} />
                    <path className="visits-chart__line visits-chart__line--visitors" d={pathThrough(visitors, 0, lastCompleteIndex)} />
                    {lastIndex > 0 && (
                        <>
                            <path className="visits-chart__line visits-chart__line--views visits-chart__line--partial" d={pathThrough(views, lastCompleteIndex, lastIndex)} />
                            <path className="visits-chart__line visits-chart__line--visitors visits-chart__line--partial" d={pathThrough(visitors, lastCompleteIndex, lastIndex)} />
                        </>
                    )}

                    {marked && markedIndex !== null && (
                        <g>
                            {hovered && <line className="visits-chart__guide" x1={xOf(markedIndex)} x2={xOf(markedIndex)} y1={PADDING.top} y2={baseline} />}
                            <circle className="visits-chart__dot visits-chart__dot--views" cx={xOf(markedIndex)} cy={yOf(marked.views)} r={4} />
                            <circle className="visits-chart__dot visits-chart__dot--visitors" cx={xOf(markedIndex)} cy={yOf(marked.visitors)} r={4} />
                        </g>
                    )}

                    <rect
                        className="visits-chart__overlay"
                        x={PADDING.left}
                        y={PADDING.top}
                        width={innerWidth}
                        height={innerHeight}
                        onPointerMove={handlePointerMove}
                        onPointerLeave={() => setHoverIndex(null)}
                    />
                </svg>
            )}

            {hovered && hoverIndex !== null && (
                <div
                    className={`visits-chart__tooltip ${xOf(hoverIndex) > width / 2 ? 'visits-chart__tooltip--left' : ''}`}
                    style={{ left: xOf(hoverIndex), top: PADDING.top }}
                >
                    <div className="visits-chart__tooltip-date">
                        {formatPointLabel(hovered.date, granularity)}
                        {hoverIndex === lastIndex && ' (en cours)'}
                    </div>
                    <div className="visits-chart__tooltip-row visits-chart__tooltip-row--visitors">
                        <span>Visiteurs</span>
                        <strong>{formatInteger(hovered.visitors)}</strong>
                    </div>
                    <div className="visits-chart__tooltip-row visits-chart__tooltip-row--views">
                        <span>Pages vues</span>
                        <strong>{formatInteger(hovered.views)}</strong>
                    </div>
                </div>
            )}
        </div>
    );
}
