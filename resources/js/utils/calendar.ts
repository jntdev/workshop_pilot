import type { DayInfo } from '@/types';

const MONTHS_SHORT = [
    'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin',
    'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'
];

const DAYS_SHORT = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

function isLeapYear(year: number): boolean {
    return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
}

function getDaysInYear(year: number): number {
    return isLeapYear(year) ? 366 : 365;
}

function formatDate(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function getTodayString(): string {
    return formatDate(new Date());
}

export function generateYearDays(year: number): DayInfo[] {
    const days: DayInfo[] = [];
    const totalDays = getDaysInYear(year);
    const today = getTodayString();

    const startDate = new Date(year, 0, 1);

    for (let i = 0; i < totalDays; i++) {
        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);

        const dateString = formatDate(currentDate);
        const dayOfWeek = currentDate.getDay();

        days.push({
            date: dateString,
            dayOfWeek,
            dayNumber: currentDate.getDate(),
            monthShort: MONTHS_SHORT[currentDate.getMonth()],
            isToday: dateString === today,
            isWeekend: dayOfWeek === 0 || dayOfWeek === 6,
        });
    }

    return days;
}

export function getDayLabel(day: DayInfo): string {
    return DAYS_SHORT[day.dayOfWeek];
}

export function formatDayHeader(day: DayInfo): string {
    return `${getDayLabel(day)} ${day.dayNumber} ${day.monthShort}`;
}
