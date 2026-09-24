import { useState } from 'react';

interface Props {
    top: number;
    onSubmit: (title: string, detail: string) => void;
    onCancel: () => void;
}

export default function NewEventPopover({ top, onSubmit, onCancel }: Props) {
    const [title, setTitle] = useState('');
    const [detail, setDetail] = useState('');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!title.trim()) return;
        onSubmit(title.trim(), detail.trim());
    };

    return (
        <div className="agenda__new-event-popover" style={{ top }} onClick={e => e.stopPropagation()}>
            <form onSubmit={handleSubmit}>
                <input
                    type="text"
                    className="agenda__new-event-popover-input"
                    placeholder="Titre"
                    value={title}
                    onChange={e => setTitle(e.target.value)}
                    autoFocus
                />
                <textarea
                    className="agenda__new-event-popover-textarea"
                    placeholder="Détail (optionnel)"
                    value={detail}
                    onChange={e => setDetail(e.target.value)}
                    rows={2}
                />
                <div className="agenda__new-event-popover-actions">
                    <button type="button" className="agenda__new-event-popover-btn agenda__new-event-popover-btn--cancel" onClick={onCancel}>
                        Annuler
                    </button>
                    <button type="submit" className="agenda__new-event-popover-btn agenda__new-event-popover-btn--submit" disabled={!title.trim()}>
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    );
}
