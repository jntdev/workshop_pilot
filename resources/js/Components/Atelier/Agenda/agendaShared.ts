export const DAY_START_HOUR = 8;
export const DAY_END_HOUR = 19;
export const SLOT_MINUTES = 30;
export const SLOTS_PER_HOUR = 60 / SLOT_MINUTES;
export const TOTAL_SLOTS = (DAY_END_HOUR - DAY_START_HOUR) * SLOTS_PER_HOUR;
export const SLOT_HEIGHT_PX = 48;
export const DEFAULT_APPOINTMENT_DURATION_MINUTES = 30;

/** Largeur de la colonne des heures (agenda__week-hours) dans la grille semaine. */
export const AGENDA_HOURS_COL_PX = 48;

/** Largeur minimale d'une colonne jour (voir .agenda__week-col min-width), utilisée pour calculer combien de jours tiennent dans la largeur disponible. */
export const AGENDA_MIN_DAY_COL_PX = 130;

const WEEKDAY_LABELS = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

export const getCsrfToken = (): string => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

export const apiHeaders = (): HeadersInit => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-XSRF-TOKEN': getCsrfToken(),
});

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

export function toIso(year: number, month: number, day: number): string {
    return `${year}-${pad(month)}-${pad(day)}`;
}

export function todayIso(): string {
    const now = new Date();
    return toIso(now.getFullYear(), now.getMonth() + 1, now.getDate());
}

export function addDays(dateIso: string, days: number): string {
    const [year, month, day] = dateIso.split('-').map(Number);
    const d = new Date(year, month - 1, day, 12);
    d.setDate(d.getDate() + days);
    return toIso(d.getFullYear(), d.getMonth() + 1, d.getDate());
}

/** Lundi de la semaine contenant dateIso. */
export function startOfWeek(dateIso: string): string {
    const [year, month, day] = dateIso.split('-').map(Number);
    const d = new Date(year, month - 1, day, 12);
    const dow = d.getDay(); // 0 = dimanche
    const diff = dow === 0 ? -6 : 1 - dow;
    d.setDate(d.getDate() + diff);
    return toIso(d.getFullYear(), d.getMonth() + 1, d.getDate());
}

/** `count` jours consécutifs à partir de startIso (inclus). */
export function daysFrom(startIso: string, count: number): string[] {
    return Array.from({ length: count }, (_, i) => addDays(startIso, i));
}

export function formatDayLabel(dateIso: string): { weekday: string; day: number } {
    const [, , day] = dateIso.split('-').map(Number);
    const idx = new Date(dateIso + 'T12:00:00').getDay();
    return { weekday: WEEKDAY_LABELS[idx === 0 ? 6 : idx - 1], day };
}

export function formatDateRangeLabel(days: string[]): string {
    if (days.length === 0) return '';
    const format = (iso: string) => new Date(iso + 'T12:00:00').toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    return `${format(days[0])} – ${format(days[days.length - 1])}`;
}

export function formatDayFullLabel(dateIso: string): string {
    const [year, month, day] = dateIso.split('-').map(Number);
    return new Date(year, month - 1, day, 12).toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
}

export function formatTime(isoString: string): string {
    return new Date(isoString).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

/** Position (en nombre de créneaux de 30 min depuis DAY_START_HOUR) d'une date, arrondie au créneau inférieur. */
export function slotIndexOf(date: Date): number {
    const minutesSinceStart = (date.getHours() - DAY_START_HOUR) * 60 + date.getMinutes();
    return Math.floor(minutesSinceStart / SLOT_MINUTES);
}

export function dateIsoOf(date: Date): string {
    return toIso(date.getFullYear(), date.getMonth() + 1, date.getDate());
}

export interface DragQuotePayload {
    kind: 'unscheduled';
    quoteId: number;
    durationMinutes: number;
}

export interface DragAppointmentPayload {
    kind: 'appointment';
    appointmentId: number;
    durationMinutes: number;
}

export type DragPayload = DragQuotePayload | DragAppointmentPayload;

export type ResizeEdge = 'start' | 'end';

export interface ResizeState {
    appointmentId: number;
    edge: ResizeEdge;
    dayIso: string;
}
