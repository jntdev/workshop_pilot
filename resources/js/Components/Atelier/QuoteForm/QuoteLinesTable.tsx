import React, { useState, useRef } from 'react';
import { QuoteLine } from '@/types';
import type { Article } from '@/types';
import Input from '@/Components/ui/Input';
import ArticleAutocomplete from '@/Components/Stock/ArticleAutocomplete';
import CataloguePickerModal from '@/Components/Stock/CataloguePickerModal';

interface QuoteLinesTableProps {
    lines: QuoteLine[];
    onLineChange: (index: number, field: keyof QuoteLine, value: string) => void;
    onLineUpdate: (index: number, updates: Partial<QuoteLine>) => void;
    onToggleNeedsOrder: (index: number) => void;
    onReorder: (from: number, to: number) => void;
    onAddLine: () => void;
    onRemoveLine: (index: number) => void;
    onRemoveConflictLine: (index: number) => void;
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
    onReorder,
    onAddLine,
    onRemoveLine,
    onRemoveConflictLine,
    disabled,
}: QuoteLinesTableProps) {
    const [pickerForLine, setPickerForLine] = useState<number | null>(null);
    const dragFromRef = useRef<number | null>(null);
    const [dragOverIndex, setDragOverIndex] = useState<number | null>(null);
    const [draggingIndex, setDraggingIndex] = useState<number | null>(null);

    const handleDragStart = (index: number) => {
        dragFromRef.current = index;
        setDraggingIndex(index);
    };

    const handleDragOver = (e: React.DragEvent, index: number) => {
        e.preventDefault();
        if (dragFromRef.current !== null && dragFromRef.current !== index) {
            setDragOverIndex(index);
        }
    };

    const handleDrop = (e: React.DragEvent, index: number) => {
        e.preventDefault();
        if (dragFromRef.current !== null && dragFromRef.current !== index) {
            onReorder(dragFromRef.current, index);
        }
        dragFromRef.current = null;
        setDragOverIndex(null);
        setDraggingIndex(null);
    };

    const handleDragEnd = () => {
        dragFromRef.current = null;
        setDragOverIndex(null);
        setDraggingIndex(null);
    };

    const handleFieldChange = (index: number, field: keyof QuoteLine, value: string) => {
        onLineChange(index, field, value);
    };

    const handleArticleSelect = (index: number, article: Article) => {
        const salePriceTtc = (article.sale_price_ttc / 100).toFixed(2);
        const purchasePriceHt = (article.purchase_price_ht / 100).toFixed(2);
        const updates = calculateLineLocally(salePriceTtc, lines[index].quantity || '1', String(article.tva_rate), purchasePriceHt);
        onLineUpdate(index, {
            ...updates,
            article_id: article.id,
            reference: article.reference,
            title: lines[index].title || article.designation,
            purchase_price_ht: purchasePriceHt,
            sale_price_ttc: salePriceTtc,
            tva_rate: String(article.tva_rate),
        });
        setPickerForLine(null);
    };

    const handleArticleDetach = (index: number) => {
        onLineUpdate(index, { article_id: null });
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
                <div className="quote-lines-table__cell" />
                <div className="quote-lines-table__cell">Intitulé</div>
                <div className="quote-lines-table__cell">Réf. / Catalogue</div>
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

            {lines.map((line, index) => {
                if (line._conflict?.hasNoContent) {
                    return (
                        <div
                            key={line.id ?? line.client_key ?? index}
                            className="quote-lines-table__row quote-lines-table__row--conflict-theirs quote-lines-table__row--ghost-deleted"
                        >
                            <span>Ligne supprimée par l'autre onglet</span>
                            <button
                                type="button"
                                onClick={() => onRemoveConflictLine(index)}
                                className="quote-lines-table__btn-remove-conflict"
                            >
                                Supprimer cette version
                            </button>
                        </div>
                    );
                }

                return (
                <div
                    key={line.id ?? line.client_key ?? index}
                    className={[
                        'quote-lines-table__row',
                        draggingIndex === index ? 'quote-lines-table__row--dragging' : '',
                        dragOverIndex === index ? 'quote-lines-table__row--drag-over' : '',
                        line._conflict?.role === 'mine' ? 'quote-lines-table__row--conflict-mine' : '',
                        line._conflict?.role === 'theirs' ? 'quote-lines-table__row--conflict-theirs' : '',
                    ].join(' ').trim()}
                    onDragOver={(e) => handleDragOver(e, index)}
                    onDrop={(e) => handleDrop(e, index)}
                >
                    {/* Handle */}
                    <div
                        className="quote-lines-table__cell quote-lines-table__drag-handle"
                        title="Réorganiser"
                        draggable={!disabled}
                        onDragStart={() => handleDragStart(index)}
                        onDragEnd={handleDragEnd}
                    >
                        {!disabled && '⠿'}
                    </div>
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
                    {/* Réf. / Catalogue */}
                    <div className="quote-lines-table__cell quote-lines-table__cell--catalogue">
                        {disabled ? (
                            <span className="quote-lines-table__ref-text">{line.reference || ''}</span>
                        ) : (
                            <>
                                <ArticleAutocomplete
                                    value={line.reference || ''}
                                    articleId={line.article_id ?? null}
                                    onChange={v => handleFieldChange(index, 'reference', v)}
                                    onSelect={article => handleArticleSelect(index, article)}
                                    onDetach={() => handleArticleDetach(index)}
                                    placeholder="Réf ou catalogue..."
                                />
                                <button
                                    type="button"
                                    className="quote-lines-table__catalogue-btn"
                                    onClick={() => setPickerForLine(index)}
                                    title="Ouvrir le catalogue"
                                >
                                    📋
                                </button>
                            </>
                        )}
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
                        {line._conflict ? (
                            <button
                                type="button"
                                onClick={() => onRemoveConflictLine(index)}
                                className="quote-lines-table__btn-remove-conflict"
                                title="Supprimer cette version"
                            >
                                Supprimer cette version
                            </button>
                        ) : (
                            <button
                                type="button"
                                onClick={() => onRemoveLine(index)}
                                className="quote-lines-table__btn-remove"
                                title="Supprimer la prestation"
                                disabled={disabled}
                            >
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L13 13M13 1L1 13" stroke="currentColor" strokeWidth="1.75" strokeLinecap="round"/>
                                </svg>
                            </button>
                        )}
                    </div>
                </div>
                );
            })}

            {!disabled && (
                <button
                    type="button"
                    onClick={onAddLine}
                    className="quote-form__btn-add-line"
                >
                    + Ajouter une ligne
                </button>
            )}

            {pickerForLine !== null && (
                <CataloguePickerModal
                    onSelect={article => handleArticleSelect(pickerForLine, article)}
                    onClose={() => setPickerForLine(null)}
                />
            )}
        </div>
    );
}
