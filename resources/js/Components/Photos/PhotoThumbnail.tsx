interface PhotoThumbnailProps {
    id: number;
    thumbUrl: string;
    fullUrl: string;
    name: string;
    onDelete?: (id: number) => void;
    canDelete?: boolean;
}

export default function PhotoThumbnail({
    id,
    thumbUrl,
    fullUrl,
    name,
    onDelete,
    canDelete = true,
}: PhotoThumbnailProps) {
    const handleClick = () => {
        window.open(fullUrl, '_blank');
    };

    const handleDelete = (e: React.MouseEvent) => {
        e.stopPropagation();
        if (onDelete) {
            onDelete(id);
        }
    };

    return (
        <div className="photo-thumbnail" onClick={handleClick}>
            <img
                src={thumbUrl}
                alt={name}
                className="photo-thumbnail__image"
            />
            {canDelete && onDelete && (
                <button
                    className="photo-thumbnail__delete"
                    onClick={handleDelete}
                    title="Supprimer"
                >
                    &times;
                </button>
            )}
        </div>
    );
}
