import { useState, type PointerEvent } from 'react';

export interface TunnelChartStep {
    label: string;
    visits: number;
}

interface TunnelChartProps {
    steps: TunnelChartStep[];
    title: string;
}

/** Une teinte par étape, du clair au foncé : une seule couleur, l'ordre du tunnel se lit à son intensité (rampe validée, 5 étapes max). */
const STEP_COLORS = ['#86a8ea', '#628bd9', '#436ec3', '#2d54a8', '#1e3a8a'];

const formatInteger = (value: number): string => new Intl.NumberFormat('fr-FR').format(value);

function formatShare(value: number): string {
    return `${new Intl.NumberFormat('fr-FR', { maximumFractionDigits: value < 10 ? 1 : 0 }).format(value)} %`;
}

function stepColor(index: number, count: number): string {
    return STEP_COLORS[count <= 1 ? STEP_COLORS.length - 1 : Math.round((index / (count - 1)) * (STEP_COLORS.length - 1))];
}

interface Hover {
    index: number;
    x: number;
    y: number;
}

/** Tunnel en barres : chaque étape est une barre proportionnelle aux visites du début, avec ce qui reste et ce qui se perd d'une étape à l'autre. */
export default function TunnelChart({ steps, title }: TunnelChartProps) {
    const [hover, setHover] = useState<Hover | null>(null);
    const first = Math.max(1, steps[0]?.visits ?? 0);

    const show = (index: number, event: PointerEvent<HTMLElement>) => {
        const bounds = event.currentTarget.closest('.tunnel-chart')?.getBoundingClientRect();
        setHover({ index, x: event.clientX - (bounds?.left ?? 0), y: event.clientY - (bounds?.top ?? 0) });
    };

    const hovered = hover === null ? null : steps[hover.index];
    const hoveredPrevious = hover !== null && hover.index > 0 ? steps[hover.index - 1].visits : null;

    return (
        <div className="tunnel-chart" role="img" aria-label={`Tunnel ${title} : ${steps.map(step => `${step.label} ${formatInteger(step.visits)}`).join(', ')}`}>
            {steps.map((step, index) => {
                const previous = index > 0 ? steps[index - 1].visits : null;
                const lost = previous !== null && previous > 0 ? Math.max(0, ((previous - step.visits) / previous) * 100) : null;

                return (
                    <div
                        key={`${index}-${step.label}`}
                        className="tunnel-chart__row"
                        tabIndex={0}
                        onPointerMove={event => show(index, event)}
                        onPointerLeave={() => setHover(null)}
                        onFocus={event => {
                            const bar = event.currentTarget.getBoundingClientRect();
                            const bounds = event.currentTarget.closest('.tunnel-chart')?.getBoundingClientRect();
                            setHover({ index, x: bar.left - (bounds?.left ?? 0) + 24, y: bar.top - (bounds?.top ?? 0) });
                        }}
                        onBlur={() => setHover(null)}
                    >
                        <span className="tunnel-chart__label">{step.label}</span>
                        <span className="tunnel-chart__track">
                            <span
                                className="tunnel-chart__bar"
                                style={{ width: `${Math.max(step.visits > 0 ? 1 : 0, (step.visits / first) * 100)}%`, background: stepColor(index, steps.length) }}
                            />
                            <span className="tunnel-chart__value">{formatInteger(step.visits)}</span>
                        </span>
                        <span className="tunnel-chart__meta">
                            {index === 0 ? '100 %' : formatShare((step.visits / first) * 100)}
                            {lost !== null && <span className="tunnel-chart__lost">−{formatShare(lost)} depuis l'étape précédente</span>}
                        </span>
                    </div>
                );
            })}

            {hover !== null && hovered !== null && (
                <div className="tunnel-chart__tooltip" style={{ left: hover.x, top: hover.y }} role="status">
                    <span className="tunnel-chart__tooltip-title">{hovered.label}</span>
                    <span className="tunnel-chart__tooltip-value">{formatInteger(hovered.visits)} visites</span>
                    <span>{formatShare((hovered.visits / first) * 100)} des visites du début</span>
                    {hoveredPrevious !== null && hoveredPrevious > 0 && (
                        <span>
                            {formatShare((hovered.visits / hoveredPrevious) * 100)} de l'étape précédente ({formatInteger(Math.max(0, hoveredPrevious - hovered.visits))} perdues)
                        </span>
                    )}
                </div>
            )}
        </div>
    );
}
