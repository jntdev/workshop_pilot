import { useState, useRef, useEffect } from 'react';

// Palette de 16 couleurs harmonieuses pour les catégories/tailles
const HEX_COLORS = [
    // Ligne 1: couleurs vives
    '#ef4444', '#f97316', '#eab308', '#22c55e',
    '#14b8a6', '#0ea5e9', '#6366f1', '#8b5cf6',
    // Ligne 2: couleurs complémentaires et neutres
    '#ec4899', '#f43f5e', '#78716c', '#64748b',
    '#FFD233', '#005D66', '#88c9bf', '#d97b5d',
];

interface HexColorPickerProps {
    value: string;
    onChange: (color: string) => void;
}

export default function HexColorPicker({ value, onChange }: HexColorPickerProps) {
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

    const handleColorSelect = (color: string) => {
        onChange(color);
        setIsOpen(false);
    };

    return (
        <div className="hex-color-picker" ref={containerRef}>
            <button
                type="button"
                className="hex-color-picker__trigger"
                onClick={() => setIsOpen(!isOpen)}
                style={{ backgroundColor: value }}
                title="Changer la couleur"
            />

            {isOpen && (
                <div className="hex-color-picker__panel">
                    <div className="hex-color-picker__grid">
                        {HEX_COLORS.map((color) => (
                            <button
                                key={color}
                                type="button"
                                className={`hex-color-picker__swatch ${color.toLowerCase() === value.toLowerCase() ? 'hex-color-picker__swatch--selected' : ''}`}
                                style={{ backgroundColor: color }}
                                onClick={() => handleColorSelect(color)}
                                title={color}
                            />
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
