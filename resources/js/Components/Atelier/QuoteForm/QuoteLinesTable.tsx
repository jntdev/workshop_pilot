import { useRef, useCallback } from 'react';
import { QuoteLine } from '@/types';
import Input from '@/Components/ui/Input';

interface QuoteLinesTableProps {
    lines: QuoteLine[];
    onLineChange: (index: number, field: keyof QuoteLine, value: string) => void;
    onLineCalculate: (
        index: number,
        calculationType: string,
        value: string,
        currentLineValues?: { purchase_price_ht?: string; tva_rate?: string; quantity?: string }
    ) => void;
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
    // Refs to store references to input elements for each line
    const rowRefs = useRef<Map<number, Map<string, HTMLInputElement>>>(new Map());

    const setInputRef = useCallback((index: number, field: string, element: HTMLInputElement | null) => {
        if (!rowRefs.current.has(index)) {
            rowRefs.current.set(index, new Map());
        }
        if (element) {
            rowRefs.current.get(index)!.set(field, element);
        }
    }, []);

    const getInputValue = (index: number, field: string, fallback: string): string => {
        const rowMap = rowRefs.current.get(index);
        const input = rowMap?.get(field);
        return input?.value ?? fallback;
    };

    const handleFieldChange = (index: number, field: keyof QuoteLine, value: string) => {
        onLineChange(index, field, value);
    };

    const handleCalculation = (index: number, calculationType: string, value: string) => {
        const line = lines[index];
        // Get current values directly from DOM inputs
        const purchasePriceHt = getInputValue(index, 'purchase_price_ht', line.purchase_price_ht);
        const tvaRate = getInputValue(index, 'tva_rate', line.tva_rate);
        const quantity = getInputValue(index, 'quantity', line.quantity);

        onLineCalculate(index, calculationType, value, {
            purchase_price_ht: purchasePriceHt,
            tva_rate: tvaRate,
            quantity: quantity,
        });
    };

    const isLineEmpty = (line: QuoteLine): boolean => {
        return !line.title || line.title.trim() === '';
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Add a new line only if the last line is not empty
            const lastLine = lines[lines.length - 1];
            if (!lastLine || !isLineEmpty(lastLine)) {
                onAddLine();
            }
        }
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
                <div className="quote-lines-table__cell">Total HT</div>
                <div className="quote-lines-table__cell">Total TTC</div>
                <div className="quote-lines-table__cell" title="Temps estimé (heures)">Temps</div>
                <div className="quote-lines-table__cell"></div>
            </div>

            {lines.map((line, index) => (
                <div className="quote-lines-table__row" key={index}>
                    <div className="quote-lines-table__cell">
                        <Input
                            type="text"
                            value={line.title}
                            onChange={(e) => handleFieldChange(index, 'title', e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            placeholder="Intitulé"
                            required
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            type="text"
                            value={line.reference || ''}
                            onChange={(e) => handleFieldChange(index, 'reference', e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input quote-lines-table__input--narrow"
                            placeholder="Réf"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            ref={(el) => setInputRef(index, 'quantity', el)}
                            type="number"
                            step="1"
                            value={Math.round(parseFloat(line.quantity) || 0)}
                            onChange={(e) => handleFieldChange(index, 'quantity', e.target.value)}
                            onBlur={() => handleCalculation(index, 'sale_price_ht', line.sale_price_ht)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            required
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            ref={(el) => setInputRef(index, 'purchase_price_ht', el)}
                            type="number"
                            step="0.01"
                            value={line.purchase_price_ht}
                            onChange={(e) => handleFieldChange(index, 'purchase_price_ht', e.target.value)}
                            onBlur={() => handleCalculation(index, 'sale_price_ht', line.sale_price_ht)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            type="number"
                            step="0.01"
                            value={line.sale_price_ht}
                            onChange={(e) => handleFieldChange(index, 'sale_price_ht', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'sale_price_ht', e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            required
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            type="number"
                            step="0.01"
                            value={line.margin_amount_ht}
                            onChange={(e) => handleFieldChange(index, 'margin_amount_ht', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'margin_amount', e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            type="number"
                            step="0.01"
                            value={parseFloat(line.margin_rate || '0').toFixed(2)}
                            onChange={(e) => handleFieldChange(index, 'margin_rate', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'margin_rate', e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            ref={(el) => setInputRef(index, 'tva_rate', el)}
                            type="number"
                            step="1"
                            value={Math.round(parseFloat(line.tva_rate) || 0)}
                            onChange={(e) => handleFieldChange(index, 'tva_rate', e.target.value)}
                            onBlur={() => handleCalculation(index, 'sale_price_ht', line.sale_price_ht)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input quote-lines-table__input--narrow"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            type="number"
                            step="0.01"
                            value={line.sale_price_ttc}
                            onChange={(e) => handleFieldChange(index, 'sale_price_ttc', e.target.value)}
                            onBlur={(e) => handleCalculation(index, 'sale_price_ttc', e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            disabled={disabled}
                        />
                    </div>
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly">
                        {line.line_total_ht ? parseFloat(line.line_total_ht).toFixed(2) : '-'} €
                    </div>
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly">
                        {line.line_total_ttc ? parseFloat(line.line_total_ttc).toFixed(2) : '-'} €
                    </div>
                    <div className="quote-lines-table__cell">
                        <Input
                            type="number"
                            step="0.25"
                            min="0"
                            value={line.estimated_time_minutes ? (parseFloat(String(line.estimated_time_minutes)) / 60).toFixed(2) : ''}
                            onChange={(e) => {
                                const hours = parseFloat(e.target.value) || 0;
                                const minutes = Math.round(hours * 60);
                                handleFieldChange(index, 'estimated_time_minutes', String(minutes));
                            }}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input quote-lines-table__input--narrow"
                            placeholder="h"
                            title="Temps estimé en heures"
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
