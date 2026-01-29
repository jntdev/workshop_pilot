import { QuoteTotals as QuoteTotalsType } from '@/types';

interface QuoteTotalsProps {
    totals: QuoteTotalsType;
    discountType: 'amount' | 'percent';
    discountValue: string;
    validUntil: string;
    onDiscountTypeChange: (type: 'amount' | 'percent') => void;
    onDiscountValueChange: (value: string) => void;
    onValidUntilChange: (date: string) => void;
    disabled?: boolean;
}

function formatCurrency(value: string | number): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(num) + ' €';
}

export default function QuoteTotals({
    totals,
    discountType,
    discountValue,
    validUntil,
    onDiscountTypeChange,
    onDiscountValueChange,
    onValidUntilChange,
    disabled,
}: QuoteTotalsProps) {
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
