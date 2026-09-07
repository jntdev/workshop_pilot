import { useState } from 'react';
import type { Article } from '@/types';

export default function ArticleCardImage({ article }: { article: Article }) {
    const [hasError, setHasError] = useState(false);

    if (!article.image_url || hasError) {
        return <span className="article-card__image-placeholder">Pas de photo</span>;
    }

    return (
        <img
            className="article-card__image"
            src={article.image_url}
            alt={article.designation}
            loading="lazy"
            onError={() => setHasError(true)}
        />
    );
}
