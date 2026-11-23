import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

/**
 * Texte d'une réponse de l'assistant, avec ses références cliquables.
 *
 * L'assistant n'écrit jamais d'adresse : il écrit [[devis:2001]], [[client:45]], [[pieces]] ou
 * [[agenda:2026-11-24]] (avec un libellé facultatif : [[devis:2001|DEV-2001]]). L'écran les transforme
 * en liens vers les pages du workshop. Seules ces destinations existent, avec un numéro ou une date
 * vérifiés : une référence mal formée s'affiche comme du texte simple, jamais comme un lien.
 *
 * Dans l'onglet de l'assistant, `onNavigate` ouvre la page dans l'onglet de travail ; ailleurs (journal),
 * le lien navigue dans l'onglet courant.
 */
const REFERENCE = /\[\[(devis|client|pieces|agenda)(?::([^\]|]*))?(?:\|([^\]]*))?\]\]/g;

interface ResolvedReference {
    href: string;
    label: string;
}

export function resolveReference(kind: string, argument: string | undefined): ResolvedReference | null {
    const value = argument?.trim() ?? '';

    switch (kind) {
        case 'devis':
            return /^\d{1,9}$/.test(value) ? { href: `/atelier/devis/${value}`, label: `Devis n°${value}` } : null;
        case 'client':
            return /^\d{1,9}$/.test(value) ? { href: `/clients/${value}`, label: `Client n°${value}` } : null;
        case 'pieces':
            return { href: '/atelier/pieces-a-commander', label: 'Pièces à commander' };
        case 'agenda':
            if (value === '') {
                return { href: '/atelier/agenda', label: 'Agenda' };
            }

            return /^\d{4}-\d{2}-\d{2}$/.test(value)
                ? { href: `/atelier/agenda?date=${value}`, label: `Agenda du ${value.split('-').reverse().join('/')}` }
                : null;
        default:
            return null;
    }
}

function cleanLabel(label: string | undefined): string | null {
    // eslint-disable-next-line no-control-regex
    const cleaned = (label ?? '').replace(/[\u0000-\u001f]/g, ' ').trim().slice(0, 80);

    return cleaned === '' ? null : cleaned;
}

interface AssistantTextProps {
    text: string;
    onNavigate?: (href: string) => void;
}

export default function AssistantText({ text, onNavigate }: AssistantTextProps) {
    const nodes: ReactNode[] = [];
    let cursor = 0;
    let key = 0;

    for (const match of text.matchAll(REFERENCE)) {
        const index = match.index ?? 0;
        const [raw, kind, argument, label] = match;

        if (index > cursor) {
            nodes.push(text.slice(cursor, index));
        }

        const target = resolveReference(kind, argument);
        const shown = cleanLabel(label) ?? target?.label ?? argument?.trim() ?? '';

        if (target !== null && onNavigate) {
            nodes.push(
                <a
                    key={key++}
                    href={target.href}
                    className="assistant-ref"
                    onClick={(event) => {
                        event.preventDefault();
                        onNavigate(target.href);
                    }}
                >
                    {shown}
                </a>,
            );
        } else if (target !== null) {
            nodes.push(
                <Link key={key++} href={target.href} className="assistant-ref">
                    {shown}
                </Link>,
            );
        } else if (shown !== '') {
            nodes.push(shown);
        }

        cursor = index + raw.length;
    }

    if (cursor < text.length) {
        nodes.push(text.slice(cursor));
    }

    return <>{nodes}</>;
}
