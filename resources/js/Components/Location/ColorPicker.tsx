import { useState, useRef, useEffect } from 'react';
import type { ReservationColorIndex } from '@/types';

// Palette de couleurs correspondant au SCSS
const RESERVATION_COLORS: Record<ReservationColorIndex, string> = {
    // Couleurs saturées
    0: '#3b82f6', // Bleu (défaut)
    1: '#ef4444', // Rouge
    2: '#22c55e', // Vert
    3: '#f59e0b', // Orange
    4: '#8b5cf6', // Violet
    5: '#ec4899', // Rose
    6: '#14b8a6', // Turquoise
    7: '#f97316', // Orange foncé
    8: '#6366f1', // Indigo
    9: '#84cc16', // Lime
    // Versions claires (40% blanc)
    10: '#8fb8f9', // Bleu clair
    11: '#f59090', // Rouge clair
    12: '#7dda9b', // Vert clair
    13: '#f9c56b', // Orange clair
    14: '#b99cf9', // Violet clair
    15: '#f3a0c4', // Rose clair
    16: '#6dd4c8', // Turquoise clair
    17: '#fba76f', // Orange foncé clair
    18: '#a1a3f7', // Indigo clair
    19: '#b5de6d', // Lime clair
    // Versions très claires (60% blanc)
    20: '#b8d4fb', // Bleu très clair
    21: '#f9b8b8', // Rouge très clair
    22: '#a8e8be', // Vert très clair
    23: '#fbda9e', // Orange très clair
    24: '#d4c4fb', // Violet très clair
    25: '#f7c5da', // Rose très clair
    26: '#9de5dd', // Turquoise très clair
    27: '#fcc9a3', // Orange foncé très clair
    28: '#c5c6f9', // Indigo très clair
    29: '#d1e9a0', // Lime très clair
};

// Ordre des couleurs par teinte (bleu → violet → rose → rouge → orange → lime → vert → turquoise)
// Ligne 1: saturées, Ligne 2: claires, Ligne 3: très claires
const COLOR_GRID: ReservationColorIndex[][] = [
    [0, 8, 4, 5, 1, 7, 3, 9, 2, 6],   // Saturées
    [10, 18, 14, 15, 11, 17, 13, 19, 12, 16], // Claires
    [20, 28, 24, 25, 21, 27, 23, 29, 22, 26], // Très claires
];

interface ColorPickerProps {
    value: ReservationColorIndex;
    onChange: (color: ReservationColorIndex) => void;
}

export default function ColorPicker({ value, onChange }: ColorPickerProps) {
    const [isOpen, setIsOpen] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    // Fermer le panneau quand on clique en dehors
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [isOpen]);

    const handleColorSelect = (colorIndex: ReservationColorIndex) => {
        onChange(colorIndex);
        setIsOpen(false);
    };

    return (
        <div className="color-picker" ref={containerRef}>
            <button
                type="button"
                className="color-picker__trigger"
                onClick={() => setIsOpen(!isOpen)}
                style={{ backgroundColor: RESERVATION_COLORS[value] }}
                title="Changer la couleur"
            >
                <span className="color-picker__trigger-icon">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
                    </svg>
                </span>
            </button>

            {isOpen && (
                <div className="color-picker__panel">
                    <div className="color-picker__grid">
                        {COLOR_GRID.map((row, rowIndex) => (
                            row.map((colorIndex) => (
                                <button
                                    key={colorIndex}
                                    type="button"
                                    className={`color-picker__swatch ${colorIndex === value ? 'color-picker__swatch--selected' : ''}`}
                                    style={{ backgroundColor: RESERVATION_COLORS[colorIndex] }}
                                    onClick={() => handleColorSelect(colorIndex)}
                                    title={`Couleur ${colorIndex + 1}`}
                                />
                            ))
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
