import { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { apiGet } from '@/utils/api';

interface JournalEntry {
    id: number;
    user: string | null;
    question: string;
    status: 'pending' | 'processing' | 'answered' | 'failed';
    status_label: string;
    answer: string | null;
    error: string | null;
    consultations: string[];
    created_at: string;
    duration_seconds: number | null;
}

interface JournalPage {
    data: JournalEntry[];
    current_page: number;
    last_page: number;
    total: number;
}

function formatDate(iso: string): string {
    return new Date(iso).toLocaleString('fr-FR', { dateStyle: 'short', timeStyle: 'short' });
}

export default function Journal() {
    const [page, setPage] = useState(1);
    const [journal, setJournal] = useState<JournalPage | null>(null);
    const [hasError, setHasError] = useState(false);

    useEffect(() => {
        let cancelled = false;
        apiGet<JournalPage>(`/api/assistant/journal?page=${page}`)
            .then((data) => {
                if (!cancelled) {
                    setJournal(data);
                    setHasError(false);
                }
            })
            .catch(() => {
                if (!cancelled) {
                    setHasError(true);
                }
            });

        return () => {
            cancelled = true;
        };
    }, [page]);

    return (
        <MainLayout>
            <Head title="Journal de l'assistant" />
            <div className="assistant-journal">
                <h1 className="assistant-journal__title">Journal de l&apos;assistant</h1>
                <p className="assistant-journal__intro">
                    Toutes les questions posées à l&apos;assistant, ses réponses et ce qu&apos;il a consulté.
                    {journal && ` ${journal.total} échange(s).`}
                </p>

                {hasError && <p className="assistant-journal__error">Le journal n&apos;a pas pu être chargé.</p>}
                {journal && journal.data.length === 0 && <p>Aucune question pour le moment.</p>}

                {journal?.data.map((entry) => (
                    <article key={entry.id} className={`assistant-journal__entry assistant-journal__entry--${entry.status}`}>
                        <header className="assistant-journal__meta">
                            <span>{formatDate(entry.created_at)}</span>
                            <span>{entry.user ?? 'Utilisateur supprimé'}</span>
                            <span className="assistant-journal__status">{entry.status_label}</span>
                            {entry.duration_seconds !== null && <span>{entry.duration_seconds} s</span>}
                        </header>
                        <p className="assistant-journal__question">{entry.question}</p>
                        {entry.answer && <p className="assistant-journal__answer">{entry.answer}</p>}
                        {entry.error && <p className="assistant-journal__failure">{entry.error}</p>}
                        {entry.consultations.length > 0 && (
                            <p className="assistant-journal__sources">Consulté : {entry.consultations.join(', ')}</p>
                        )}
                    </article>
                ))}

                {journal && journal.last_page > 1 && (
                    <nav className="assistant-journal__pager">
                        <button type="button" disabled={page <= 1} onClick={() => setPage(page - 1)}>
                            Plus récents
                        </button>
                        <span>
                            Page {journal.current_page} / {journal.last_page}
                        </span>
                        <button type="button" disabled={page >= journal.last_page} onClick={() => setPage(page + 1)}>
                            Plus anciens
                        </button>
                    </nav>
                )}
            </div>
        </MainLayout>
    );
}
