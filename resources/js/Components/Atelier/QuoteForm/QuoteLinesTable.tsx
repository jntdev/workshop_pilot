import { QuoteLine, LineCalculationResult } from '@/types';

interface QuoteLinesTableProps {
    lines: QuoteLine[];
    onLineChange: (index: number, field: keyof QuoteLine, value: string) => void;
    onLineCalculate: (index: number, calculationType: string, value: string) => void;
    onAddLine: () => void;
    onRemoveLine: (index: number) => void;
    disabled?: boolean;
}

export default function QuoteLinesTable({
    lines,
    onLineChange,
    onLineCalculate,
    onAddLine,
    onRemoveLine,
    disabled,
}: QuoteLinesTableProps) {
    const handleFieldChange = (index: number, field: keyof QuoteLine, value: string) => {
        onLineChange(index, field, value);
    };

    const handleCalculation = (index: number, calculationType: string, value: string) => {
        onLineCalculate(index, calculationType, value);
    };

    return (
        <div className="quote-lines-table">
            <div className="quote-lines-table__header">
                <div className="quote-lines-table__cell">Intitulé</div>
                <div className="quote-lines-table__cell">Réf.</div>
                <div className="quote-lines-table__cell">Qté</div>
                <div className="quote-lines-table__cell">PA HT</div>
                <div className="quote-lines-table__cell">PV HT</div>
                <div className="quote-lines-table__cell">Marge €</div>
                <div className="quote-lines-table__cell">Marge %</div>
                <div className="quote-lines-table__cell">TVA %</div>
                <div className="quote-lines-table__cell">PV TTC</div>
                <div className="quote-lines-table__cell"></div>
            </div>

            {lines.map((line, index) => (
                <div className="quote-lines-table__row" key={index}>
                    <div className="quote-lines-table__cell">
                        <input
                            type="text"
                            value={line.title}
                            onChange={(e) => handleFieldChange(index, 'title', e.target.value)}
                            className="quote-lines-table__input"
                            placeholder="Intitulé"
                            required
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="text"
                            value={line.reference || ''}
                            onChange={(e) => handleFieldChange(index, 'reference', e.target.value)}
                            className="quote-lines-table__input quote-lines-table__input--narrow"
                            placeholder="Réf"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="number"
                            step="0.01"
                            value={line.quantity}
                            onChange={(e) => handleFieldChange(index, 'quantity', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'sale_price_ht', line.sale_price_ht)}
                            className="quote-lines-table__input quote-lines-table__input--number"
                            required
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="number"
                            step="0.01"
                            value={line.purchase_price_ht}
                            onChange={(e) => handleFieldChange(index, 'purchase_price_ht', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'sale_price_ht', line.sale_price_ht)}
                            className="quote-lines-table__input quote-lines-table__input--number"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="number"
                            step="0.01"
                            value={line.sale_price_ht}
                            onChange={(e) => handleFieldChange(index, 'sale_price_ht', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'sale_price_ht', e.target.value)}
                            className="quote-lines-table__input quote-lines-table__input--number"
                            required
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="number"
                            step="0.01"
                            value={line.margin_amount_ht}
                            onChange={(e) => handleFieldChange(index, 'margin_amount_ht', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'margin_amount', e.target.value)}
                            className="quote-lines-table__input quote-lines-table__input--number"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="number"
                            step="0.0001"
                            value={line.margin_rate}
                            onChange={(e) => handleFieldChange(index, 'margin_rate', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'margin_rate', e.target.value)}
                            className="quote-lines-table__input quote-lines-table__input--number"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="number"
                            step="1"
                            value={line.tva_rate}
                            onChange={(e) => handleFieldChange(index, 'tva_rate', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'sale_price_ht', line.sale_price_ht)}
                            className="quote-lines-table__input quote-lines-table__input--narrow"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <input
                            type="number"
                            step="0.01"
                            value={line.sale_price_ttc}
                            onChange={(e) => handleFieldChange(index, 'sale_price_ttc', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'sale_price_ttc', e.target.value)}
                            className="quote-lines-table__input quote-lines-table__input--number"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <button
                            type="button"
                            onClick={() => onRemoveLine(index)}
                            className="quote-lines-table__btn-remove"
                            title="Supprimer la prestation"
                            disabled={disabled}
                        >
                            ×
                        </button>
                    </div>
                </div>
            ))}

            {!disabled && (
                <button
                    type="button"
                    onClick={onAddLine}
                    className="quote-form__btn-add-line"
                >
                    + Ajouter une ligne
                </button>
            )}
        </div>
    );
}
