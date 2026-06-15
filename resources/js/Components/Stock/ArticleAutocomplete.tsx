import { useState, useCallback, useRef, useEffect } from 'react';
import type { Article } from '@/types';

interface Props {
    value: string;
    articleId: number | null;
    onChange: (value: string) => void;
    onSelect: (article: Article) => void;
    onDetach: () => void;
    placeholder?: string;
}

export default function ArticleAutocomplete({ value, articleId, onChange, onSelect, onDetach, placeholder }: Props) {
    const [results, setResults] = useState<Article[]>([]);
    const [open, setOpen] = useState(false);
    const [isSearching, setIsSearching] = useState(false);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const containerRef = useRef<HTMLDivElement>(null);

    const search = useCallback(async (q: string) => {
        if (q.length < 3) { setResults([]); setOpen(false); return; }
        setIsSearching(true);
        const res = await fetch(`/api/articles/search?q=${encodeURIComponent(q)}`, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setResults(data);
        setOpen(data.length > 0);
        setIsSearching(false);
    }, []);

    const handleChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        const v = e.target.value;
        onChange(v);
        if (debounceRef.current) { clearTimeout(debounceRef.current); }
        debounceRef.current = setTimeout(() => search(v), 300);
    }, [onChange, search]);

    const handleSelect = useCallback((article: Article) => {
        onSelect(article);
        setOpen(false);
        setResults([]);
    }, [onSelect]);

    useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    return (
        <div className="article-autocomplete" ref={containerRef}>
            <div className="article-autocomplete__input-wrap">
                <input
                    type="text"
                    className={`article-autocomplete__input ${articleId ? 'article-autocomplete__input--linked' : ''}`}
                    value={value}
                    onChange={handleChange}
                    placeholder={placeholder ?? 'Référence ou désignation...'}
                    onFocus={() => { if (results.length > 0) { setOpen(true); } }}
                />
                {articleId && (
                    <button type="button" className="article-autocomplete__detach" onClick={onDetach} title="Détacher du catalogue">
                        ⊘
                    </button>
                )}
                {isSearching && <span className="article-autocomplete__spinner">⟳</span>}
            </div>

            {open && results.length > 0 && (
                <div className="article-autocomplete__dropdown">
                    {results.map(article => (
                        <button
                            key={article.id}
                            type="button"
                            className="article-autocomplete__result"
                            onClick={() => handleSelect(article)}
                        >
                            <span className="article-autocomplete__result-ref">{article.reference}</span>
                            <span className="article-autocomplete__result-name">{article.designation}</span>
                            <span className={`article-autocomplete__result-stock ${article.stock_quantity > 0 ? 'article-autocomplete__result-stock--ok' : ''}`}>
                                {article.stock_quantity} en stock
                            </span>
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
