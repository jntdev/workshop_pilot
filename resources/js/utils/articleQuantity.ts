const CONTINUOUS_UNITS = ['litre', 'kg'];

export function quantityStep(unit: string): string {
    return CONTINUOUS_UNITS.includes(unit) ? '0.01' : '1';
}
