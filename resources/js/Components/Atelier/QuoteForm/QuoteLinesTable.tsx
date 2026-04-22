import React from 'react';
import { QuoteLine } from '@/types';
import Input from '@/Components/ui/Input';

interface QuoteLinesTableProps {
    lines: QuoteLine[];
    onLineChange: (index: number, field: keyof QuoteLine, value: string) => void;
    onLineUpdate: (index: number, updates: Partial<QuoteLine>) => void;
    onToggleNeedsOrder: (index: number) => void;
    onAddLine: () => void;
    onRemoveLine: (index: number) => void;
    disabled?: boolean;
}

// Calcul local des totaux de ligne
function calculateLineLocally(
    salePriceTtc: string,
    quantity: string,
    tvaRate: string,
    purchasePriceHt: string
): Partial<QuoteLine> {
    const ttc = parseFloat(salePriceTtc) || 0;
    const qty = parseFloat(quantity) || 1;
    const tva = parseFloat(tvaRate) || 20;
    const paHt = parseFloat(purchasePriceHt) || 0;

    // Total TTC = PV TTC × Quantité
    const lineTotalTtc = ttc * qty;
    // Total HT = Total TTC / (1 + TVA/100)
    const lineTotalHt = lineTotalTtc / (1 + tva / 100);
    // PV HT unitaire
    const saleHt = ttc / (1 + tva / 100);
    // Total PA = PA HT × Quantité
    const linePurchaseHt = paHt * qty;
    // Marge € = Total HT - Total PA
    const lineMarginHt = lineTotalHt - linePurchaseHt;
    // Marge unitaire
    const marginAmountHt = saleHt - paHt;
    // Taux de marge = Marge / PV HT × 100
    const marginRate = saleHt > 0 ? (marginAmountHt / saleHt) * 100 : 0;

    return {
        sale_price_ht: saleHt.toFixed(2),
        margin_amount_ht: marginAmountHt.toFixed(2),
        margin_rate: marginRate.toFixed(4),
        line_purchase_ht: linePurchaseHt.toFixed(2),
        line_margin_ht: lineMarginHt.toFixed(2),
        line_total_ht: lineTotalHt.toFixed(2),
        line_total_ttc: lineTotalTtc.toFixed(2),
    };
}

export default function QuoteLinesTable({
    lines,
    onLineChange,
    onLineUpdate,
    onToggleNeedsOrder,
    onAddLine,
    onRemoveLine,
    disabled,
}: QuoteLinesTableProps) {
    const handleFieldChange = (index: number, field: keyof QuoteLine, value: string) => {
        onLineChange(index, field, value);
    };

    // Recalcule la ligne quand PV TTC, Quantité ou PA HT change
    const handleRecalculate = (index: number) => {
        const line = lines[index];
        const updates = calculateLineLocally(
            line.sale_price_ttc,
            line.quantity,
            line.tva_rate,
            line.purchase_price_ht
        );
        onLineUpdate(index, updates);
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
                <div className="quote-lines-table__cell sensitive-column">PA HT</div>
                <div className="quote-lines-table__cell">TVA %</div>
                <div className="quote-lines-table__cell">PV TTC</div>
                <div className="quote-lines-table__cell">Qté</div>
                <div className="quote-lines-table__cell sensitive-column">Total PA</div>
                <div className="quote-lines-table__cell sensitive-column">Marge €</div>
                <div className="quote-lines-table__cell sensitive-column">Marge %</div>
                <div className="quote-lines-table__cell">Total HT</div>
                <div className="quote-lines-table__cell">Total TTC</div>
                <div className="quote-lines-table__cell sensitive-column" title="Temps estimé (heures)">Temps</div>
                <div className="quote-lines-table__cell" title="À commander">Cmd.</div>
                <div className="quote-lines-table__cell"></div>
            </div>

            {lines.every(isLineEmpty) && (
                <div className="quote-lines-table__empty">
                    <p>Aucune prestation renseignée. Le PDF sera généré comme un <strong>bon de dépôt</strong> avec un espace pour le diagnostic manuel.</p>
                </div>
            )}

            {lines.map((line, index) => (
                <div className="quote-lines-table__row" key={index}>
                    {/* Intitulé */}
                    <div className="quote-lines-table__cell">
                        <Input
                            type="text"
                            value={line.title}
                            onChange={(e) => handleFieldChange(index, 'title', e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            placeholder="Intitulé"
                            disabled={disabled}
                        />
                    </div>
                    {/* Réf. */}
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
                    {/* PA HT */}
                    <div className="quote-lines-table__cell sensitive-column">
                        <Input
                            type="number"
                            step="0.01"
                            value={line.purchase_price_ht}
                            onChange={(e) => handleFieldChange(index, 'purchase_price_ht', e.target.value)}
                            onBlur={() => handleRecalculate(index)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            disabled={disabled}
                        />
                    </div>
                    {/* TVA % (readonly) */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly">
                        {Math.round(parseFloat(line.tva_rate) || 0)} %
                    </div>
                    {/* PV TTC */}
                    <div className="quote-lines-table__cell">
                        <Input
                            type="number"
                            step="0.01"
                            value={line.sale_price_ttc}
                            onChange={(e) => handleFieldChange(index, 'sale_price_ttc', e.target.value)}
                            onBlur={() => handleRecalculate(index)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            disabled={disabled}
                        />
                    </div>
                    {/* Qté */}
                    <div className="quote-lines-table__cell">
                        <Input
                            type="number"
                            step="1"
                            value={Math.round(parseFloat(line.quantity) || 0)}
                            onChange={(e) => handleFieldChange(index, 'quantity', e.target.value)}
                            onBlur={() => handleRecalculate(index)}
                            onKeyDown={handleKeyDown}
                            className="quote-lines-table__input"
                            required
                            disabled={disabled}
                        />
                    </div>
                    {/* Total PA HT (readonly) */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly sensitive-column">
                        {line.line_purchase_ht ? parseFloat(line.line_purchase_ht).toFixed(2) : '-'} €
                    </div>
                    {/* Marge € (readonly) */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly sensitive-column">
                        {line.line_margin_ht ? parseFloat(line.line_margin_ht).toFixed(2) : '-'} €
                    </div>
                    {/* Marge % (readonly) */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly sensitive-column">
                        {line.line_total_ht && line.line_margin_ht && parseFloat(line.line_total_ht) > 0
                            ? ((parseFloat(line.line_margin_ht) / parseFloat(line.line_total_ht)) * 100).toFixed(1)
                            : '-'} %
                    </div>
                    {/* Total HT (readonly) */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly">
                        {line.line_total_ht ? parseFloat(line.line_total_ht).toFixed(2) : '-'} €
                    </div>
                    {/* Total TTC (readonly) */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--readonly">
                        {line.line_total_ttc ? parseFloat(line.line_total_ttc).toFixed(2) : '-'} €
                    </div>
                    {/* Temps */}
                    <div className="quote-lines-table__cell sensitive-column">
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
                    {/* À commander */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--center">
                        <div className="quote-lines-table__needs-order">
                            <input
                                type="checkbox"
                                checked={line.needs_order}
                                onChange={() => onToggleNeedsOrder(index)}
                                className="quote-lines-table__checkbox"
                                title="Marquer comme pièce à commander"
                                disabled={disabled}
                            />
                            {line.needs_order && !line.reference && (
                                <span className="quote-lines-table__needs-order-error" title="Référence obligatoire pour les pièces à commander">
                                    !
                                </span>
                            )}
                        </div>
                    </div>
                    {/* Supprimer */}
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
