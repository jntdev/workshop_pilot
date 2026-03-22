import PhotoThumbnail from './PhotoThumbnail';

interface Photo {
    id: number;
    url: string;
    thumb_url: string;
    name: string;
}

interface PhotoGalleryProps {
    photos: Photo[];
    onDelete?: (id: number) => void;
    canDelete?: boolean;
}

export default function PhotoGallery({
    photos,
    onDelete,
    canDelete = false,
}: PhotoGalleryProps) {
    if (photos.length === 0) {
        return null;
    }

    return (
        <div className="photo-gallery">
            {photos.map((photo) => (
                <PhotoThumbnail
                    key={photo.id}
                    id={photo.id}
                    thumbUrl={photo.thumb_url}
                    fullUrl={photo.url}
                    name={photo.name}
                    onDelete={onDelete}
                    canDelete={canDelete}
                />
            ))}
        </div>
    );
}
