import { useCallback, useEffect, useMemo, useRef, useState, type PointerEvent } from 'react';
import {
    START, LOAN_M, T3S_TTC, T3S_TVA, type Params,
    sim, stats, payPlan, caBase, exSurplus, caTargets, baseFlows, capexFlows, ncmFlows, lossFlows, caFlows, loanFlows, t3sFlows,
    pLabel, pShort,
} from './Pilotage/modeleTresorerie';

/** Onglet « Pilotage » : projection de trésorerie 2026-2029 avec 25 T3S, réglable (objectifs de CA, salaire, T3S, prêts). */

const N = 37; // du 7 déc. 2026 au 7 déc. 2029
const CA_DONE = 115211;
const CCA = 12000;
const STORAGE_KEY = 'dashboard.pilotage.reglages';

interface Reglages {
    ca0: number; ca1: number; ca2: number; marge: number;
    D: number; rate: number;
    renew: boolean; price: number; nVendus: number; delivery: number; acompte: number; solde: number;
    loanEnd: number; winterAdj: number; thresh: number;
}

const DEFAULTS: Reglages = {
    ca0: 160000, ca1: 184000, ca2: 184000, marge: 50,
    D: 1750, rate: 45,
    renew: true, price: 900, nVendus: 25, delivery: 3, acompte: 0, solde: 7,
    loanEnd: 25, winterAdj: 0, thresh: 5000,
};

const FIXED = {
    payStart: 5, ag: 10, cca: CCA, surcout: 500, pm: 22.69, f: 0.5, capex: 3548.22, ncm: true, cg7: false, caDone: CA_DONE, caOn: true,
    ateL: [300, 300, 596, 1684, 2037, 3377], ateF: [3000, 2000, 2500, 2200, 2500, 3400],
};

const MOIS_AM = ['décembre', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre'];

const nf = new Intl.NumberFormat('fr-FR');
const NB = ' ';
const num = (v: number): string => nf.format(v);
const eur = (v: number, r = 100): string => { const x = Math.round(v / r) * r; return (x < 0 ? '−' : '') + num(Math.abs(x)) + NB + '€'; };
const eurS = (v: number, r = 10): string => { const x = Math.round(v / r) * r; return (x > 0 ? '+' : x < 0 ? '−' : '') + num(Math.abs(x)); };
const pct = (v: number): string => v + NB + '%';

type Field =
    | { id: keyof Reglages; kind: 'range'; label: string; min: number; max: number; step: number; fmt: (v: number) => string; hint?: (r: Reglages) => string }
    | { id: keyof Reglages; kind: 'select'; label: string; options: [number, string][]; hint?: (r: Reglages) => string }
    | { id: keyof Reglages; kind: 'check'; label: string };

const GROUPS: { title: string; fields: Field[] }[] = [
    {
        title: "Chiffre d'affaires (HT, par exercice)",
        fields: [
            {
                id: 'ca0', kind: 'range', label: '2026-27 (en cours)', min: 120000, max: 220000, step: 1000, fmt: v => eur(v, 1000),
                hint: r => `Déjà fait de mai à octobre : ${eur(CA_DONE, 1)}. Reste ${eur(r.ca0 - CA_DONE)}, soit ${eur((r.ca0 - CA_DONE) / 6)} par mois jusqu'à fin avril.`,
            },
            { id: 'ca1', kind: 'range', label: '2027-28', min: 120000, max: 240000, step: 1000, fmt: v => eur(v, 1000) },
            { id: 'ca2', kind: 'range', label: '2028-29', min: 120000, max: 240000, step: 1000, fmt: v => eur(v, 1000) },
            { id: 'marge', kind: 'range', label: 'Part du CA en plus qui reste sur le compte', min: 30, max: 70, step: 5, fmt: pct, hint: () => '50 % : estimation prudente.' },
        ],
    },
    {
        title: 'Votre salaire',
        fields: [
            {
                id: 'D', kind: 'range', label: 'Salaire net par mois', min: 0, max: 3500, step: 50, fmt: v => eur(v, 1),
                hint: r => `Coût pour l'entreprise, charges comprises : ${eur(r.D * (1 + r.rate / 100), 10)} par mois, à partir de novembre 2027.`,
            },
            { id: 'rate', kind: 'range', label: 'Charges sociales', min: 25, max: 80, step: 1, fmt: pct, hint: () => '45 % : à faire valider par le comptable.' },
        ],
    },
    {
        title: 'Vélos T3S',
        fields: [
            { id: 'renew', kind: 'check', label: 'Racheter 25 T3S chaque printemps' },
            { id: 'price', kind: 'range', label: 'Prix de revente par vélo (TTC)', min: 400, max: 1300, step: 50, fmt: v => eur(v, 1) },
            { id: 'nVendus', kind: 'range', label: 'Vélos revendus', min: 0, max: 25, step: 1, fmt: v => `${v} sur 25` },
            {
                id: 'delivery', kind: 'select', label: 'Livraison', options: [[3, 'mars'], [4, 'avril'], [5, 'mai'], [6, 'juin']],
                hint: r => (r.delivery > 3 ? 'Les locations perdues avant la livraison sont déduites.' : ''),
            },
            { id: 'acompte', kind: 'select', label: 'Acompte (1/3)', options: [[0, 'décembre'], [1, 'janvier']] },
            {
                id: 'solde', kind: 'select', label: 'Solde (2 × 1/3)', options: [[6, 'juin et juillet'], [7, 'juillet et août'], [8, 'août et septembre']],
                hint: () => `${eur(T3S_TTC / 3, 1)} TTC à chaque paiement, compté dans le solde du 7 du mois suivant. L'État rend les ${eur(T3S_TVA, 1)} de TVA après la livraison (à confirmer par le comptable).`,
            },
        ],
    },
    {
        title: 'Prêts, hiver et réserve',
        fields: [
            {
                id: 'loanEnd', kind: 'select', label: 'Dernière échéance des prêts', options: [[19, 'juillet 2028'], [25, 'janvier 2029'], [31, 'juillet 2029'], [40, 'après 2029']],
                hint: () => `${eur(LOAN_M, 1)} par mois pour les 3 prêts. Date non confirmée : les tableaux de remboursement manquent.`,
            },
            {
                id: 'winterAdj', kind: 'range', label: 'Hiver meilleur ou moins bon', min: -10000, max: 10000, step: 500, fmt: v => (v > 0 ? '+' : '') + eur(v, 1),
                hint: () => "Écart sur les 6 mois d'hiver (novembre à avril), chaque année.",
            },
            { id: 'thresh', kind: 'range', label: 'Réserve minimale', min: 0, max: 15000, step: 500, fmt: v => eur(v, 1) },
        ],
    },
];

function loadReglages(): Reglages {
    const r: Reglages = { ...DEFAULTS };
    try {
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null') as Partial<Reglages> | null;
        if (saved) {
            (Object.keys(DEFAULTS) as (keyof Reglages)[]).forEach(k => {
                if (typeof saved[k] === typeof DEFAULTS[k]) (r as unknown as Record<string, unknown>)[k] = saved[k];
            });
        }
    } catch {
        // réglages par défaut
    }
    return r;
}

function toParams(r: Reglages): Params {
    return {
        ...FIXED, D: r.D, rate: r.rate / 100, renew: r.renew, price: r.price, share: r.nVendus / 25, delivery: r.delivery,
        ech: [r.acompte, r.solde, r.solde + 1], loanEnd: r.loanEnd, winterAdj: r.winterAdj, thresh: r.thresh,
        ca: [r.ca0, r.ca1, r.ca2], margeCa: r.marge / 100,
    };
}

function compute(r: Reglages) {
    const p = toParams(r);
    const avec = sim(p, N, 'avec'), sans = sim({ ...p, caOn: false }, N, 'avec');
    const pay = payPlan(p, N), cb = caBase(p), loanYr = LOAN_M * 12;
    const surplus = exSurplus(p), surplus0 = exSurplus({ ...p, caOn: false }), cost = p.D * (1 + p.rate) * 12;
    const netEq = (w: boolean) => (surplus + (w ? loanYr : 0)) / (12 * (1 + p.rate));
    const need = (w: boolean) => cb + (cost - surplus0 - (w ? loanYr : 0)) / p.margeCa;
    return {
        p, avec, sans, pay, cb,
        st: stats(avec, p.thresh), st12: stats(avec.slice(0, 12), p.thresh), stS: stats(sans, p.thresh),
        net: [netEq(false), netEq(true)], need: [need(false), need(true)],
    };
}
type Result = ReturnType<typeof compute>;

function events(p: Params): Record<number, string[]> {
    const e: Record<number, string[]> = {};
    const add = (k: number, t: string) => { if (k >= 0 && k < N) (e[k] = e[k] || []).push(t); };
    add(0, 'Paiement du vélo Adris Vitesse ; achats et ventes NCM (CG7, T3S, T7S, F1000)');
    for (let y = 0; y < (p.renew ? 3 : 1); y++) {
        const o = 12 * y, saison = 2027 + y;
        add(p.ech[0] + 1 + o, `Acompte des T3S de ${saison} (1/3)`);
        add(p.ech[1] + 1 + o, `2e paiement des T3S de ${saison}`);
        add(p.ech[2] + 1 + o, `Solde des T3S de ${saison}`);
        add(p.delivery + 2 + o, `L'État rend ${eur(T3S_TVA, 1)} de TVA (livraison des T3S)`);
        if (p.share > 0) {
            add(10 + o, 'Revente des T3S, 1re moitié');
            add(11 + o, 'Revente, 2e moitié ; TVA à reverser (1re part)');
            add(12 + o, 'TVA à reverser (2e part)');
        }
    }
    add(p.payStart + 1, `Vous commencez à vous payer : remboursement de l'argent prêté (${eur(CCA, 1)})`);
    add(p.ag + 2, "Après l'AG : salaire net + charges sociales");
    add(p.loanEnd + 2, `Fin des prêts : ${eur(LOAN_M, 1)} de plus chaque mois`);
    return e;
}

const level = (v: number, th: number): string => (v < 0 ? 'crit' : v < th ? 'warn' : '');

/* ---------- graphique ---------- */

function niceStep(x: number): number { return [2000, 5000, 10000, 20000, 25000, 50000, 100000, 200000].find(s => s >= x) ?? 200000; }

function TresorerieChart({ result }: { result: Result }) {
    const boxRef = useRef<HTMLDivElement>(null);
    const [width, setWidth] = useState(720);
    const [hover, setHover] = useState<number | null>(null);

    useEffect(() => {
        if (!boxRef.current) return;
        const observer = new ResizeObserver(entries => setWidth(entries[0].contentRect.width));
        observer.observe(boxRef.current);
        return () => observer.disconnect();
    }, []);

    const { p, avec, sans, st12, stS } = result;
    const W = Math.max(300, Math.round(width)), narrow = W < 560;
    const H = narrow ? 270 : 340, ml = narrow ? 40 : 58, mr = narrow ? 64 : 84, mt = 12, mb = 30;
    const all = avec.concat(sans, [0, p.thresh]);
    let lo = Math.min(...all), hi = Math.max(...all);
    const pad = (hi - lo) * 0.06;
    lo -= pad; hi += pad;
    const step = niceStep((hi - lo) / 4);
    lo = Math.floor(lo / step) * step; hi = Math.ceil(hi / step) * step;
    if (hi <= lo) hi = lo + step;
    const pw = W - ml - mr, ph = H - mt - mb;
    const xAt = (k: number) => ml + (pw * k) / (N - 1);
    const yAt = (v: number) => mt + (ph * (hi - v)) / (hi - lo);
    const ev = useMemo(() => events(p), [p]);

    const bands: { k: number; cls: string }[] = [];
    for (let y = 0; y < (p.renew ? 3 : 1); y++) {
        p.ech.forEach(am => bands.push({ k: am + 1 + 12 * y, cls: 'buy' }));
        if (p.share > 0) { bands.push({ k: 10 + 12 * y, cls: 'sell' }); bands.push({ k: 11 + 12 * y, cls: 'sell' }); }
    }
    const ticks: number[] = [];
    for (let v = lo; v <= hi + 0.5; v += step) ticks.push(v);
    const axis = (v: number) => (v === 0 ? '0' : narrow ? `${v < 0 ? '−' : ''}${Math.abs(v / 1000)} k` : `${v < 0 ? '−' : ''}${num(Math.abs(v))}`);
    const pts = (a: number[]) => a.map((v, k) => `${xAt(k).toFixed(1)},${yAt(v).toFixed(1)}`).join(' ');
    const clampX = (x: number, half: number) => Math.min(Math.max(x, ml + half), W - mr - half);
    const below = (v: number) => { const y = yAt(v) + 20; return y > H - mb - 4 ? yAt(v) - 11 : y; };

    let yA = yAt(avec[N - 1]), yS = yAt(sans[N - 1]);
    if (Math.abs(yA - yS) < 16) { const m = (yA + yS) / 2, up = yA <= yS; yA = m + (up ? -8 : 8); yS = m + (up ? 8 : -8); }
    const showSansMin = stS.kmin !== st12.kmin || Math.abs(sans[stS.kmin] - avec[st12.kmin]) > 1;

    const onMove = (e: PointerEvent<SVGRectElement>) => {
        const r = e.currentTarget.ownerSVGElement?.getBoundingClientRect();
        if (!r) return;
        const x = ((e.clientX - r.left) * W) / r.width;
        const k = Math.round(((x - ml) / pw) * (N - 1));
        setHover(k >= 0 && k < N ? k : null);
    };

    const tipLeft = hover === null ? 0 : (xAt(hover) * (width / W)) + 14 + 230 < width ? (xAt(hover) * (width / W)) + 14 : Math.max(0, (xAt(hover) * (width / W)) - 244);

    return (
        <div className="pilotage-chart" ref={boxRef}>
            <svg viewBox={`0 0 ${W} ${H}`} role="img" aria-label="Solde du compte pro de décembre 2026 à décembre 2029">
                {bands.map((b, i) => {
                    const x0 = Math.max(ml, xAt(b.k - 0.5)), x1 = Math.min(W - mr, xAt(b.k + 0.5));
                    return x1 > x0 ? <rect key={i} className={`pilotage-chart__band pilotage-chart__band--${b.cls}`} x={x0} y={mt} width={x1 - x0} height={ph} /> : null;
                })}
                {ticks.map(v => (
                    <g key={v}>
                        <line className="pilotage-chart__grid" x1={ml} x2={W - mr} y1={yAt(v)} y2={yAt(v)} />
                        <text className="pilotage-chart__axis" x={ml - 7} y={yAt(v) + 4} textAnchor="end">{axis(v)}</text>
                    </g>
                ))}
                {Array.from({ length: N }, (_, k) => k).filter(k => k % (narrow ? 6 : 4) === 0).map(k => (
                    <text key={k} className="pilotage-chart__axis" x={xAt(k)} y={H - mb + 19} textAnchor="middle">{pShort(k)}</text>
                ))}
                {lo < 0 && hi > 0 && <line className="pilotage-chart__zero" x1={ml} x2={W - mr} y1={yAt(0)} y2={yAt(0)} />}
                {p.thresh > 0 && <line className="pilotage-chart__reserve" x1={ml} x2={W - mr} y1={yAt(p.thresh)} y2={yAt(p.thresh)} />}
                <polyline className="pilotage-chart__line pilotage-chart__line--sans" points={pts(sans)} />
                <polyline className="pilotage-chart__line pilotage-chart__line--avec" points={pts(avec)} />

                <circle className="pilotage-chart__dot pilotage-chart__dot--avec" cx={xAt(st12.kmin)} cy={yAt(avec[st12.kmin])} r={4.5} />
                <text className="pilotage-chart__label" x={clampX(xAt(st12.kmin), narrow ? 26 : 70)} y={below(avec[st12.kmin])} textAnchor="middle">
                    {narrow ? eur(avec[st12.kmin]) : `${eur(avec[st12.kmin])} le ${pLabel(st12.kmin)}`}
                </text>
                {showSansMin && (
                    <>
                        <circle className="pilotage-chart__dot pilotage-chart__dot--sans" cx={xAt(stS.kmin)} cy={yAt(sans[stS.kmin])} r={4} />
                        <text className="pilotage-chart__label pilotage-chart__label--sans" x={clampX(xAt(stS.kmin), narrow ? 26 : 70)} y={below(sans[stS.kmin])} textAnchor="middle">
                            {narrow ? eur(sans[stS.kmin]) : `${eur(sans[stS.kmin])} le ${pLabel(stS.kmin)}`}
                        </text>
                    </>
                )}
                <circle className="pilotage-chart__dot pilotage-chart__dot--sans" cx={xAt(N - 1)} cy={yAt(sans[N - 1])} r={4} />
                <circle className="pilotage-chart__dot pilotage-chart__dot--avec" cx={xAt(N - 1)} cy={yAt(avec[N - 1])} r={4.5} />
                <text className="pilotage-chart__label pilotage-chart__label--avec" x={xAt(N - 1) + 8} y={yA + 4}>{eur(avec[N - 1])}</text>
                <text className="pilotage-chart__label pilotage-chart__label--sans" x={xAt(N - 1) + 8} y={yS + 4}>{eur(sans[N - 1])}</text>

                {hover !== null && (
                    <>
                        <line className="pilotage-chart__guide" x1={xAt(hover)} x2={xAt(hover)} y1={mt} y2={H - mb} />
                        <circle className="pilotage-chart__dot pilotage-chart__dot--sans" cx={xAt(hover)} cy={yAt(sans[hover])} r={4} />
                        <circle className="pilotage-chart__dot pilotage-chart__dot--avec" cx={xAt(hover)} cy={yAt(avec[hover])} r={5} />
                    </>
                )}
                <rect className="pilotage-chart__hit" x={ml} y={mt} width={pw} height={ph} onPointerMove={onMove} onPointerDown={onMove} onPointerLeave={() => setHover(null)} />
            </svg>
            {hover !== null && (
                <div className="pilotage-chart__tip" style={{ left: tipLeft }}>
                    <div className="pilotage-chart__tip-date">{pLabel(hover)}</div>
                    <div className="pilotage-chart__tip-row"><i className="pilotage__swatch pilotage__swatch--avec" />Vos objectifs <b>{eur(avec[hover], 10)}</b></div>
                    <div className="pilotage-chart__tip-row"><i className="pilotage__swatch pilotage__swatch--sans" />CA de référence <b>{eur(sans[hover], 10)}</b></div>
                    {ev[hover] && <div className="pilotage-chart__tip-ev">{ev[hover].map(t => <div key={t}>{t}</div>)}</div>}
                </div>
            )}
        </div>
    );
}

/* ---------- panneau ---------- */

export default function PilotagePanel() {
    const [reglages, setReglages] = useState<Reglages>(loadReglages);

    useEffect(() => {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(reglages)); } catch { /* sans mémoire */ }
    }, [reglages]);

    const update = useCallback((id: keyof Reglages, value: number | boolean) => setReglages(r => ({ ...r, [id]: value })), []);
    const result = useMemo(() => compute(reglages), [reglages]);
    const { p, avec, st, st12, stS, pay, cb, net, need } = result;
    const th = p.thresh;

    const verdict = st.min >= th
        ? { cls: 'good', label: 'Ça tient', text: `Le compte reste au-dessus de la réserve de ${eur(th, 1)} pendant les trois ans. Point bas : ${eur(st.min)} le ${pLabel(st.kmin)}.` }
        : st.min >= 0
            ? { cls: 'warn', label: 'Juste', text: `Le compte passe sous la réserve de ${eur(th, 1)} le ${pLabel(st.low)}. Point bas : ${eur(st.min)} le ${pLabel(st.kmin)}.` }
            : { cls: 'crit', label: 'Découvert', text: `Le compte passe sous 0 le ${pLabel(st.neg)} et descend à ${eur(st.min)} le ${pLabel(st.kmin)}.` };

    const kpis: [string, string, string, string][] = [
        ["Solde le plus bas avant l'automne 2027", eur(st12.min), `le ${pLabel(st12.kmin)}`, level(st12.min, th)],
        ['Solde après la 1re revente', eur(avec[11]), 'au 7 novembre 2027', level(avec[11], th)],
        ['Solde au 7 décembre 2029', eur(avec[N - 1]), p.loanEnd + 2 <= N - 1 ? 'les prêts sont terminés' : 'les prêts courent encore', level(avec[N - 1], th)],
        ['Solde le plus bas sur trois ans', eur(st.min), `le ${pLabel(st.kmin)}`, level(st.min, th)],
        ['Salaire net possible en 2027-28', eur(net[0], 10), `par mois ; ${eur(net[1], 10)} après les prêts`, net[0] >= p.D ? 'good' : 'warn'],
        [`CA nécessaire pour ${eur(p.D, 10)} net par mois`, eur(need[0], 1000), `par exercice ; ${eur(need[1], 1000)} après les prêts`, ''],
    ];

    const annual = [0, 1, 2].map(w => {
        const k0 = 12 * w, k1 = k0 + 11, start = w === 0 ? START : avec[k0 - 1], end = avec[k1];
        const payTot = pay.out.slice(k0, k1 + 1).reduce((a, b) => a + b, 0), seg = avec.slice(k0, k1 + 1), mn = Math.min(...seg);
        return { lab: `7 nov. ${2026 + w} → 7 nov. ${2027 + w}`, start, res: end - start + payTot, payTot, end, mn, kmn: k0 + seg.indexOf(mn) };
    });

    const targets = caTargets(p), reste = p.ca[0] - p.caDone;
    const cumul = targets.reduce<number[]>((acc, v) => [...acc, (acc[acc.length - 1] ?? p.caDone) + v], []);

    const monthly = useMemo(() => {
        const base = baseFlows(p, N), cx = capexFlows(p, N), nc = ncmFlows(p, N), loss = lossFlows(p, N, p.delivery - 1);
        const caf = caFlows(p, N), ln = loanFlows(p, N), t3 = t3sFlows(p, N), ev = events(p);
        return Array.from({ length: N }, (_, k) => ({
            k, expl: base[k] + loss[k] + caf[k] + ln[k], adr: cx[k] + nc[k], t3: t3[k], rem: -pay.out[k], solde: avec[k], ev: (ev[k] || []).join(' ; '),
        }));
    }, [p, pay, avec]);

    const dash = (v: number) => (Math.abs(v) > 0.5 ? eurS(v) : '–');

    return (
        <div className="pilotage">
            <div className="pilotage__intro">
                <div>
                    <h2 className="pilotage__title">Projection de trésorerie 2026-2029</h2>
                    <p className="pilotage__lead">
                        L'argent sur le compte pro, au 7 de chaque mois, du 7 décembre 2026 au 7 décembre 2029 : 25 vélos T3S achetés pour la saison,
                        loués puis revendus à l'automne. Modèle du 22/11/2026 (relevés pro, CA3, suivi NCM). Faites varier les réglages : tout se recalcule.
                    </p>
                </div>
                <button type="button" className="pilotage__reset" onClick={() => setReglages({ ...DEFAULTS })}>Revenir aux chiffres de départ</button>
            </div>

            <div className="pilotage__layout">
                <aside className="pilotage__controls" aria-label="Réglages">
                    {GROUPS.map(g => (
                        <fieldset key={g.title} className="pilotage__group">
                            <legend className="pilotage__group-title">{g.title}</legend>
                            {g.fields.map(f => {
                                const id = `pilotage-${f.id}`;
                                const hint = 'hint' in f && f.hint ? f.hint(reglages) : '';
                                if (f.kind === 'check') {
                                    return (
                                        <label key={f.id} className="pilotage__check" htmlFor={id}>
                                            <input id={id} type="checkbox" checked={reglages[f.id] as boolean} onChange={e => update(f.id, e.target.checked)} />
                                            {f.label}
                                        </label>
                                    );
                                }
                                if (f.kind === 'select') {
                                    return (
                                        <div key={f.id} className="pilotage__field pilotage__field--row">
                                            <label htmlFor={id}>{f.label}</label>
                                            <select id={id} value={reglages[f.id] as number} onChange={e => update(f.id, Number(e.target.value))}>
                                                {f.options.map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                                            </select>
                                            {hint && <div className="pilotage__hint">{hint}</div>}
                                        </div>
                                    );
                                }
                                return (
                                    <div key={f.id} className="pilotage__field">
                                        <div className="pilotage__field-top">
                                            <label htmlFor={id}>{f.label}</label>
                                            <output htmlFor={id}>{f.fmt(reglages[f.id] as number)}</output>
                                        </div>
                                        <input id={id} type="range" min={f.min} max={f.max} step={f.step} value={reglages[f.id] as number} onChange={e => update(f.id, Number(e.target.value))} />
                                        {hint && <div className="pilotage__hint">{hint}</div>}
                                    </div>
                                );
                            })}
                        </fieldset>
                    ))}
                </aside>

                <div className="pilotage__results">
                    <div className="pilotage__verdict">
                        <span className={`pilotage__pill pilotage__pill--${verdict.cls}`}>{verdict.label}</span>
                        <div>
                            <p>{verdict.text}</p>
                            <p className="pilotage__verdict-sub">
                                Avec le CA de référence (environ {eur(cb, 1000)} par exercice), le point bas serait de {eur(stS.min)} le {pLabel(stS.kmin)}.
                                {pay.gap > 0.5 && ` Attention : l'argent prêté (${eur(CCA, 1)}) ne couvre pas votre salaire de mai à octobre 2027. Il manque ${eur(pay.gap)}, qui n'est pas compté.`}
                            </p>
                        </div>
                    </div>

                    <div className="pilotage__kpis">
                        {kpis.map(([k, v, s, c]) => (
                            <div key={k} className="pilotage__kpi">
                                <div className="pilotage__kpi-label">{k}</div>
                                <div className={`pilotage__kpi-value ${c ? `pilotage__kpi-value--${c}` : ''}`}>{v}</div>
                                <div className="pilotage__kpi-sub">{s}</div>
                            </div>
                        ))}
                    </div>

                    <section className="pilotage__card">
                        <div className="pilotage__card-head">
                            <h3 className="pilotage__card-title">Argent sur le compte pro</h3>
                            <div className="pilotage__legend">
                                <span><i className="pilotage__swatch pilotage__swatch--avec" />Avec vos objectifs de CA</span>
                                <span><i className="pilotage__swatch pilotage__swatch--sans" />Avec le CA de référence</span>
                                <span><i className="pilotage__swatch pilotage__swatch--buy" />Paiement des T3S</span>
                                <span><i className="pilotage__swatch pilotage__swatch--sell" />Revente</span>
                                <span><i className="pilotage__swatch pilotage__swatch--reserve" />Réserve minimale</span>
                            </div>
                        </div>
                        <TresorerieChart result={result} />
                        <p className="pilotage__caption">
                            Départ : 29 334 € au 7 novembre 2026. La courbe orange garde le CA de référence (environ {eur(cb, 1000)} par exercice) avec les mêmes autres réglages.
                            Passez sur le graphique pour lire chaque mois.
                        </p>
                    </section>

                    <section className="pilotage__card">
                        <h3 className="pilotage__card-title">Année par année</h3>
                        <p className="pilotage__sub">Début et fin : solde du compte. Gagné : ce que l'entreprise dégage avant de vous payer. Salaire : ce que votre rémunération coûte à l'entreprise.</p>
                        <div className="pilotage__table-wrap">
                            <table className="pilotage__table">
                                <thead><tr><th>Du … au …</th><th className="n">Début</th><th className="n">Gagné</th><th className="n">Salaire</th><th className="n">Fin</th><th className="n">Le plus bas</th></tr></thead>
                                <tbody>
                                    {annual.map(a => (
                                        <tr key={a.lab}>
                                            <td className="nowrap">{a.lab}</td>
                                            <td className="n">{eur(a.start)}</td>
                                            <td className="n">{eurS(a.res, 100)}</td>
                                            <td className="n">{a.payTot ? eurS(-a.payTot, 100) : '–'}</td>
                                            <td className="n b">{eur(a.end)}</td>
                                            <td className={`n ${level(a.mn, th)}`}>{eur(a.mn)}<br /><span className="pilotage__mini">{pLabel(a.kmn)}</span></td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section className="pilotage__card">
                        <h3 className="pilotage__card-title">CA à faire cet hiver</h3>
                        <p className="pilotage__sub">
                            Exercice 2026-27, objectif de {eur(p.ca[0], 1)} HT. Mai à septembre : 106 711 € (CA3), octobre estimé à 8 500 €. Il reste {eur(reste)} à faire en 6 mois,
                            soit {eur(reste / 6)} par mois en moyenne, réparti selon la saison. L'hiver dernier : 43 189 €, soit {eur(43189 / 6)} par mois.
                        </p>
                        <div className="pilotage__table-wrap">
                            <table className="pilotage__table">
                                <thead><tr><th>Mois</th><th className="n">fin oct.</th>{['nov.', 'déc.', 'janv.', 'févr.', 'mars', 'avril'].map(m => <th key={m} className="n">{m}</th>)}</tr></thead>
                                <tbody>
                                    <tr><td>À faire dans le mois</td><td className="n">–</td>{targets.map((v, i) => <td key={i} className="n">{num(Math.round(v / 100) * 100)}</td>)}</tr>
                                    <tr><td>Cumul de l'exercice</td><td className="n">{num(Math.round(p.caDone / 100) * 100)}</td>{cumul.map((v, i) => <td key={i} className="n">{num(Math.round(v / 100) * 100)}</td>)}</tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section className="pilotage__card">
                        <details>
                            <summary className="pilotage__summary"><h3 className="pilotage__card-title">Mois par mois</h3><span>37 mois</span></summary>
                            <p className="pilotage__sub">
                                Montants en euros pour chaque mois jusqu'au 7. « Activité normale » reprend l'argent de l'an dernier, avec la hausse de l'atelier, l'objectif de CA,
                                les locations perdues avant la livraison des T3S et la fin des prêts.
                            </p>
                            <div className="pilotage__table-wrap">
                                <table className="pilotage__table">
                                    <thead><tr><th>Au</th><th className="n">Activité normale</th><th className="n">Vélos Adris et NCM</th><th className="n">T3S</th><th className="n">Salaire</th><th className="n">Solde</th><th>Ce qui se passe</th></tr></thead>
                                    <tbody>
                                        {monthly.map(m => (
                                            <tr key={m.k} className={m.k > 0 && m.k % 12 === 0 ? 'sep' : ''}>
                                                <td className="nowrap">{pLabel(m.k)}</td>
                                                <td className="n">{eurS(m.expl)}</td>
                                                <td className="n">{dash(m.adr)}</td>
                                                <td className="n">{dash(m.t3)}</td>
                                                <td className="n">{dash(m.rem)}</td>
                                                <td className={`n b ${level(m.solde, th)}`}>{eur(m.solde, 10)}</td>
                                                <td className="ev">{m.ev}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    </section>

                    <section className="pilotage__card">
                        <h3 className="pilotage__card-title">Ce que le modèle suppose</h3>
                        <ul className="pilotage__hyp">
                            <li>Départ : 29 334 € au 7 novembre 2026, moins 3 548 € pour le vélo Adris Vitesse payé en décembre. Achats à Leon Cycle (NCM) de 4 586 € TTC en décembre. Ventes NCM de novembre : 6 396 € TTC, dont le CG7 payé le 13 novembre.</li>
                            <li>
                                Activité normale : l'argent réellement entré et sorti l'an dernier, mois par mois (relevés pro), avec la hausse de l'atelier et 500 € de dépenses en plus chaque mois d'hiver.
                                Le CA de référence (environ {eur(cb, 1000)}) mélange l'hiver 2025-26, mai-juin 2026 et l'été 2025 (134 600 €), plus 15 200 € pour la hausse de l'atelier.
                                Pour comparer : 111 582 € sur l'exercice 2025-26, 157 280 € sur les 12 derniers mois déclarés.
                            </li>
                            <li>Objectif de CA : l'écart entre le CA visé et le CA de référence, multiplié par la part qui reste sur le compte ({pct(reglages.marge)}), réparti selon la saison de 2026.</li>
                            <li>
                                T3S : 18 500 € HT, soit {eur(T3S_TTC, 1)} TTC. Acompte d'un tiers ({eur(T3S_TTC / 3, 1)}) en {MOIS_AM[p.ech[0] % 12]}, puis un tiers en {MOIS_AM[p.ech[1] % 12]} et un tiers
                                en {MOIS_AM[p.ech[2] % 12]} ; chaque paiement compte dans le solde du 7 du mois suivant. L'État rend {eur(T3S_TVA, 1)} de TVA sur la déclaration du mois de livraison
                                ({MOIS_AM[p.delivery]}), payée le mois d'après : à confirmer par le comptable. Revente en octobre et novembre à {eur(p.price, 1)} TTC, TVA reversée le mois suivant.
                                La marge de location (22,69 € HT par jour de location d'un vélo électrique) est déjà comptée dans l'activité normale. {p.renew ? 'Même opération chaque année, aux mêmes prix.' : 'Une seule saison.'}
                            </li>
                            <li>
                                Salaire : rien avant mai 2027. De mai à octobre 2027, l'entreprise vous rembourse l'argent que vous lui avez prêté ({eur(CCA, 1)}), au rythme du salaire net, sans charges.
                                À partir de novembre 2027, après l'AG : salaire net + {pct(reglages.rate)} de charges sociales.
                            </li>
                            <li>Prêts : {eur(LOAN_M, 1)} par mois pour les 3 prêts, jusqu'à la dernière échéance choisie. Années 2028 et 2029 : mêmes saisons que cette année, sans inflation ni croissance en dehors de l'objectif de CA. L'impôt sur les bénéfices n'est pas compté.</li>
                        </ul>
                        <p className="pilotage__sources">
                            Sources : relevés CIC pro du 07/07/2025 au 07/11/2026 ; workshop (sauvegarde du 19/11/2026, API au 21/11/2026) ; suivi des commandes NCM ; devis Leon Cycle ; factures Adris ;
                            CA3 d'avril 2025 à septembre 2026. Ce sont des estimations, à prendre comme des repères. Les réglages sont gardés dans ce navigateur.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    );
}
