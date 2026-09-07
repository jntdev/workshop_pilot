export const ATTRIBUTE_LABELS: Record<string, string> = {
    practice_type: 'Pratique',
    valve_type: 'Type de valve',
    chainring_diameter_mm: 'Diamètre (mm)',
    tooth_count: 'Nombre de dents',
    speed_count: 'Nombre de vitesses',
    crank_length_mm: 'Longueur manivelle (mm)',
    side: 'Côté',
    speed_compat: 'Compatibilité vitesses',
    axle_type: 'Type d\'axe',
    position: 'Position',
    power_source: 'Alimentation',
    wheel_diameter: 'Diamètre de roue',
    wheel_width_mm: 'Largeur (mm)',
    wheel_width_inches: 'Largeur (pouces)',
    tooth_range_min: 'Petit pignon',
    tooth_range_max: 'Grand pignon',
};

export const ATTRIBUTE_ORDER = [
    'wheel_diameter', 'wheel_width_mm', 'wheel_width_inches', 'tooth_range_min', 'tooth_range_max',
    'practice_type', 'valve_type', 'chainring_diameter_mm', 'tooth_count',
    'speed_count', 'crank_length_mm', 'side', 'speed_compat', 'axle_type',
    'position', 'power_source',
];

export function orderedAttributeEntries(attributes: Record<string, string[]>): [string, string[]][] {
    return Object.entries(attributes).sort(
        ([a], [b]) => ATTRIBUTE_ORDER.indexOf(a) - ATTRIBUTE_ORDER.indexOf(b),
    );
}

export function formatOptionLabel(key: string, value: string): string {
    if (key === 'practice_type' && value.includes('VTC')) {
        return 'VTC/Urbain';
    }

    return value;
}
