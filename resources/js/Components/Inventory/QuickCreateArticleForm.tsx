import { useEffect, useState } from 'react';
import type { ArticleCategory, ArticleFilterOptions, ArticleSubcategory, Brand } from '@/types';
import { ATTRIBUTE_LABELS, orderedAttributeEntries, formatOptionLabel } from '@/utils/articleFilters';
import { quantityStep } from '@/utils/articleQuantity';
import PhotoCapture from '@/Components/Inventory/PhotoCapture';

interface Props {
    barcode: string;
    csrfToken: string;
    onCreated: () => void;
    onCancel: () => void;
}

function eurosToCents(value: string): number {
    return Math.round(parseFloat(value.replace(',', '.')) * 100) || 0;
}

export default function QuickCreateArticleForm({ barcode, csrfToken, onCreated, onCancel }: Props) {
    const [step, setStep] = useState<'photo' | 'form'>('photo');
    const [imageUrl, setImageUrl] = useState<string | null>(null);
    const [isUploadingPhoto, setIsUploadingPhoto] = useState(false);

    const [subcategories, setSubcategories] = useState<ArticleSubcategory[]>([]);
    const [brands, setBrands] = useState<Brand[]>([]);
    const [filterOptions, setFilterOptions] = useState<ArticleFilterOptions | null>(null);
    const [selectedAttributes, setSelectedAttributes] = useState<Record<string, string>>({});

    const [designation, setDesignation] = useState('');
    const [subcategoryId, setSubcategoryId] = useState<number | null>(null);
    const [brandId, setBrandId] = useState<number | null>(null);
    const [purchasePriceHt, setPurchasePriceHt] = useState('');
    const [salePriceTtc, setSalePriceTtc] = useState('');
    const [quantity, setQuantity] = useState('');

    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        fetch('/api/article-categories?source=catalogue', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setSubcategories((data.categories as ArticleCategory[] ?? []).flatMap(c => c.subcategories)));

        fetch('/api/brands', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setBrands(data.brands ?? []));
    }, []);

    useEffect(() => {
        setSelectedAttributes({});

        if (subcategoryId === null) {
            setFilterOptions(null);
            return;
        }

        fetch(`/api/article-subcategories/${subcategoryId}/filter-options`, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setFilterOptions(data));
    }, [subcategoryId]);

    const setAttributeValue = (key: string, value: string) => {
        setSelectedAttributes(prev => {
            const next = { ...prev };
            if (value) { next[key] = value; } else { delete next[key]; }
            return next;
        });
    };

    const handlePhotoCaptured = async (file: File) => {
        setIsUploadingPhoto(true);
        setError(null);

        const formData = new FormData();
        formData.append('photo', file);

        const res = await fetch('/api/articles/upload-photo', {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        });

        setIsUploadingPhoto(false);

        if (res.ok) {
            const data = await res.json();
            setImageUrl(data.image_url);
            setStep('form');
        } else {
            setError('Échec de l\'envoi de la photo.');
        }
    };

    const isValid = imageUrl && designation && subcategoryId && brandId && purchasePriceHt && salePriceTtc && quantity;

    const submit = async () => {
        if (!isValid) { return; }

        setIsSaving(true);
        setError(null);

        const res = await fetch('/api/inventory/articles', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                barcode,
                designation,
                article_subcategory_id: subcategoryId,
                brand_id: brandId,
                purchase_price_ht: eurosToCents(purchasePriceHt),
                sale_price_ttc: eurosToCents(salePriceTtc),
                quantity: parseFloat(quantity) || 0,
                image_url: imageUrl,
                attributes: selectedAttributes,
            }),
        });

        setIsSaving(false);

        if (res.ok) {
            onCreated();
        } else {
            const data = await res.json();
            setError(data.message || 'Erreur lors de la création.');
        }
    };

    if (step === 'photo') {
        return (
            <div className="quick-create-form__photo-step">
                {isUploadingPhoto ? (
                    <div className="quick-create-form__uploading">Envoi de la photo...</div>
                ) : (
                    <PhotoCapture onCaptured={handlePhotoCaptured} onCancel={onCancel} />
                )}
                {error && <div className="quick-create-form__error">{error}</div>}
            </div>
        );
    }

    return (
        <div className="quick-create-form">
            <p className="quick-create-form__barcode">Code-barres : <strong>{barcode}</strong></p>

            {imageUrl && (
                <img src={imageUrl} alt="Photo capturée" className="quick-create-form__preview" />
            )}

            <input
                type="text"
                className="quick-create-form__input"
                placeholder="Désignation"
                value={designation}
                onChange={e => setDesignation(e.target.value)}
            />

            <select
                className="quick-create-form__input"
                value={subcategoryId ?? ''}
                onChange={e => setSubcategoryId(e.target.value ? Number(e.target.value) : null)}
            >
                <option value="">Sous-catégorie</option>
                {subcategories.map(sub => (
                    <option key={sub.id} value={sub.id}>{sub.name}</option>
                ))}
            </select>

            <select
                className="quick-create-form__input"
                value={brandId ?? ''}
                onChange={e => setBrandId(e.target.value ? Number(e.target.value) : null)}
            >
                <option value="">Marque</option>
                {brands.map(brand => (
                    <option key={brand.id} value={brand.id}>{brand.name}</option>
                ))}
            </select>

            <div className="quick-create-form__price-row">
                <input
                    type="text"
                    inputMode="decimal"
                    className="quick-create-form__input"
                    placeholder="Prix d'achat HT (€)"
                    value={purchasePriceHt}
                    onChange={e => setPurchasePriceHt(e.target.value)}
                />
                <input
                    type="text"
                    inputMode="decimal"
                    className="quick-create-form__input"
                    placeholder="Prix de vente TTC (€)"
                    value={salePriceTtc}
                    onChange={e => setSalePriceTtc(e.target.value)}
                />
            </div>

            <input
                type="number"
                step={quantityStep('pièce')}
                className="quick-create-form__input"
                placeholder="Quantité comptée"
                value={quantity}
                onChange={e => setQuantity(e.target.value)}
            />

            {filterOptions && Object.keys(filterOptions.attributes).length > 0 && (
                <div className="quick-create-form__attributes">
                    {orderedAttributeEntries(filterOptions.attributes).map(([key, values]) => (
                        <select
                            key={key}
                            className="quick-create-form__input"
                            value={selectedAttributes[key] ?? ''}
                            onChange={e => setAttributeValue(key, e.target.value)}
                        >
                            <option value="">{ATTRIBUTE_LABELS[key] ?? key} (optionnel)</option>
                            {values.map(value => (
                                <option key={value} value={value}>{formatOptionLabel(key, value)}</option>
                            ))}
                        </select>
                    ))}
                </div>
            )}

            {error && <div className="quick-create-form__error">{error}</div>}

            <div className="quick-create-form__actions">
                <button type="button" className="quick-create-form__cancel-btn" onClick={onCancel}>Annuler</button>
                <button
                    type="button"
                    className="quick-create-form__submit-btn"
                    onClick={submit}
                    disabled={!isValid || isSaving}
                >
                    Créer l'article
                </button>
            </div>
        </div>
    );
}
