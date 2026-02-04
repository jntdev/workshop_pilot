import { useState } from 'react';
import { QuoteTotals as QuoteTotalsType } from '@/types';

interface QuoteTotalsProps {
    totals: QuoteTotalsType;
    discountType: 'amount' | 'percent';
    discountValue: string;
    validUntil: string;
    totalEstimatedTimeMinutes: number | null;
    actualTimeMinutes: number | null;
    onDiscountTypeChange: (type: 'amount' | 'percent') => void;
    onDiscountValueChange: (value: string) => void;
    onValidUntilChange: (date: string) => void;
    onActualTimeChange: (minutes: number | null) => void;
    onSaveActualTime?: () => Promise<void>;
    disabled?: boolean;
    isInvoice?: boolean;
}

function formatCurrency(value: string | number): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(num) + ' €';
}

function formatTime(minutes: number | null): string {
    if (minutes === null || minutes === undefined) return '-';
    const hours = minutes / 60;
    return hours.toFixed(2) + ' h';
}

export default function QuoteTotals({
    totals,
    discountType,
    discountValue,
    validUntil,
    totalEstimatedTimeMinutes,
    actualTimeMinutes,
    onDiscountTypeChange,
    onDiscountValueChange,
    onValidUntilChange,
    onActualTimeChange,
    onSaveActualTime,
    disabled,
    isInvoice,
}: QuoteTotalsProps) {
    const [isSavingTime, setIsSavingTime] = useState(false);

    const handleActualTimeHoursChange = (hoursString: string) => {
        if (hoursString === '' || hoursString === null) {
            onActualTimeChange(null);
        } else {
            const hours = parseFloat(hoursString) || 0;
            const minutes = Math.round(hours * 60);
            onActualTimeChange(minutes);
        }
    };

    const handleSaveActualTime = async () => {
        if (!onSaveActualTime) return;
        setIsSavingTime(true);
        try {
            await onSaveActualTime();
        } finally {
            setIsSavingTime(false);
        }
    };

    return (
        <div>
            <div className="quote-totals">
                <div className="quote-totals__row">
                    <span className="quote-totals__label">Total HT</span>
                    <span className="quote-totals__value">{formatCurrency(totals.total_ht)}</span>
                </div>
                <div className="quote-totals__row">
                    <span className="quote-totals__label">TVA</span>
                    <span className="quote-totals__value">{formatCurrency(totals.total_tva)}</span>
                </div>
                <div className="quote-totals__row quote-totals__row--total">
                    <span className="quote-totals__label">Total TTC</span>
                    <span className="quote-totals__value">{formatCurrency(totals.total_ttc)}</span>
                </div>
                <div className="quote-totals__row">
                    <span className="quote-totals__label">Marge totale</span>
                    <span className="quote-totals__value">{formatCurrency(totals.margin_total_ht)}</span>
                </div>

                {/* Section temps - usage interne */}
                <div className="quote-totals__time-section">
                    <div className="quote-totals__row">
                        <span className="quote-totals__label" title="Usage interne - non visible sur PDF">
                            Temps estimé total
                        </span>
                        <span className="quote-totals__value">{formatTime(totalEstimatedTimeMinutes)}</span>
                    </div>
                    <div className="quote-totals__row">
                        <span className="quote-totals__label" title="Usage interne - non visible sur PDF">
                            Temps réel
                        </span>
                        <span className="quote-totals__value quote-totals__time-value">
                            <input
                                type="number"
                                step="0.25"
                                min="0"
                                value={actualTimeMinutes !== null ? (actualTimeMinutes / 60).toFixed(2) : ''}
                                onChange={(e) => handleActualTimeHoursChange(e.target.value)}
                                className="quote-totals__time-input"
                                placeholder="h"
                                title="Temps réel en heures (éditable même sur facture)"
                            />
                            <span className="quote-totals__time-unit">h</span>
                            {isInvoice && onSaveActualTime && (
                                <button
                                    type="button"
                                    onClick={handleSaveActualTime}
                                    disabled={isSavingTime}
                                    className="quote-totals__save-time-btn"
                                    title="Enregistrer le temps réel"
                                >
                                    {isSavingTime ? '...' : 'Enregistrer'}
                                </button>
                            )}
                        </span>
                    </div>
                </div>
            </div>

            <div className="quote-form__discount">
                <div className="quote-form__field">
                    <label htmlFor="discountType" className="quote-form__label">Type remise</label>
                    <select
                        id="discountType"
                        value={discountType}
                        onChange={(e) => onDiscountTypeChange(e.target.value as 'amount' | 'percent')}
                        className="quote-form__input"
                        disabled={disabled}
                    >
                        <option value="amount">Montant (€)</option>
                        <option value="percent">Pourcentage (%)</option>
                    </select>
                </div>

                <div className="quote-form__field">
                    <label htmlFor="discountValue" className="quote-form__label">Remise</label>
                    <input
                        type="number"
                        step="0.01"
                        id="discountValue"
                        value={discountValue}
                        onChange={(e) => onDiscountValueChange(e.target.value)}
                        className="quote-form__input"
                        disabled={disabled}
                    />
                </div>

                <div className="quote-form__field">
                    <label htmlFor="validUntil" className="quote-form__label">Date de validité</label>
                    <input
                        type="date"
                        id="validUntil"
                        value={validUntil}
                        onChange={(e) => onValidUntilChange(e.target.value)}
                        className="quote-form__input"
                        required
                        disabled={disabled}
                    />
                </div>
            </div>
        </div>
    );
}
