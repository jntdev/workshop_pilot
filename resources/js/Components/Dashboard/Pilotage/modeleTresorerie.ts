/**
 * Projection de trésorerie 2026-2029 : modèle du 22/11/2026, échéancier réel des T3S du 27/11/2026.
 * Repris de entrepreneur/projection-tresorerie/generateur/modele.js (relevés CIC pro, CA3, suivi des commandes NCM,
 * devis Leon Cycle). Les montants sont des estimations : des repères, pas une comptabilité.
 *
 * Période k : se termine le 7 du mois (k = 0 : 7 déc. 2026, k = 11 : 7 nov. 2027, k = 36 : 7 déc. 2029).
 * Mois am : déc. 2026 = 0, janv. 2027 = 1 … ; un paiement fait pendant le mois am compte dans la période k = am + 1.
 */

export const START = 29334.2;
// Flux nets réels des relevés pro : 0..5 = hiver 2025-26 (périodes finissant le 7 déc. à 7 mai), 6..11 = juin 2026, juil. 2026, puis août-nov. 2025
const TPL = [-1861.54, -4628.87, -4063.01, 1111.61, -3184.65, -2446.42, 2712.59, 10384.97, 5617.92, 17019.95, -7634.36, -4189.46];
// Jours-vélos VAE servis (parc plafonné à 25) par mois de location, 2026 : mars à août
const SRV: Record<number, number> = { 3: 125, 4: 154, 5: 138, 6: 180, 7: 534, 8: 573 };
const VTCM = 13.98; // marge HT par jour-vélo VTC
const MOIS = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
// CA HT (CA3) contenu dans les 12 mois de flux : nov. 2025 à avril 2026, mai et juin 2026, juil. à oct. 2025
const CA_BASE_CA3 = 134583;
// Poids saisonniers du CA (CA3 2026, octobre estimé), périodes 0..11 = activité de novembre à octobre
const CA_W = [7744, 7615, 4876, 6045, 7308, 9601, 14159, 19211, 26276, 29378, 17687, 8500];
export const LOAN_M = 531.91 + 192.92 + 102.07; // échéance mensuelle des 3 prêts
export const T3S_TTC = 22200; // 25 T3S, devis Leon Cycle de 18 500 € HT
export const T3S_TVA = 3700;

export interface Params {
    D: number; // salaire net par mois
    payStart: number; // mois am du premier remboursement de l'argent prêté
    ag: number; // dernier mois am payé sur l'argent prêté ; ensuite salaire + charges
    cca: number; // argent prêté à l'entreprise (compte courant)
    rate: number; // charges sociales (0,45 = 45 %)
    delivery: number; // mois de livraison des T3S (3 = mars)
    ech: number[]; // mois am des paiements des T3S : [acompte, 2e tiers, solde]
    price: number; // prix de revente TTC par vélo
    share: number; // part des 25 vélos revendus (0 à 1)
    surcout: number; // dépenses en plus chaque mois d'hiver
    winterAdj: number; // écart sur les 6 mois d'hiver
    pm: number; // marge HT par jour de location d'un VAE
    f: number; // part des locations VAE perdues reportées sur un VTC
    thresh: number; // réserve minimale
    capex: number; // vélo Adris Vitesse à payer en décembre
    ncm: boolean;
    cg7: boolean;
    ca: number[]; // objectifs de CA HT : [2026-27, 2027-28, 2028-29]
    caDone: number; // CA déjà réalisé de mai à octobre 2026
    caOn: boolean;
    margeCa: number; // part du CA en plus qui reste sur le compte
    loanEnd: number; // mois am de la dernière échéance des prêts
    renew: boolean; // rachat de 25 T3S chaque printemps
    ateL: number[];
    ateF: number[];
}

export interface PayPlan { out: number[]; cca: number[]; net: number[]; cot: number[]; left: number; gap: number; firstEmpty: number | null }
export interface Stats { min: number; kmin: number; neg: number; low: number }

const sum = (a: number[]): number => a.reduce((x, y) => x + y, 0);

export function pLabel(k: number): string { const m = (11 + k) % 12, y = 2026 + Math.floor((11 + k) / 12); return '7 ' + MOIS[m] + ' ' + y; }
export function pShort(k: number): string { const m = (11 + k) % 12, y = 2026 + Math.floor((11 + k) / 12); return MOIS[m] + ' ' + String(y).slice(2); }

function deltas(p: Params): number[] { return p.ateF.map((v, i) => v - p.ateL[i]); }
export function caBase(p: Params): number { return CA_BASE_CA3 + sum(deltas(p)) / 0.48; }

export function baseFlows(p: Params, N: number): number[] {
    const dl = deltas(p), out: number[] = [];
    for (let k = 0; k < N; k++) {
        const i = k % 12;
        let x = TPL[i];
        if (i < 6) x += -p.surcout + dl[i] + p.winterAdj / 6;
        out.push(x);
    }
    return out;
}

export function payPlan(p: Params, N: number): PayPlan {
    const out = Array(N).fill(0), cca = Array(N).fill(0), net = Array(N).fill(0), cot = Array(N).fill(0);
    let left = p.cca, gap = 0, firstEmpty: number | null = null;
    for (let k = 0; k < N; k++) {
        const am = k - 1;
        if (am < p.payStart) continue;
        if (am <= p.ag) {
            const d = Math.min(p.D, left);
            left -= d; cca[k] = d; out[k] = d;
            if (p.D - d > 0.5) { gap += p.D - d; if (firstEmpty === null) firstEmpty = am; }
        } else {
            net[k] = p.D; cot[k] = p.D * p.rate; out[k] = p.D * (1 + p.rate);
        }
    }
    return { out, cca, net, cot, left, gap, firstEmpty };
}

/** T3S : paiements par tiers aux mois p.ech, TVA rendue sur la déclaration du mois de livraison, revente en octobre et novembre. */
export function t3sFlows(p: Params, N: number): number[] {
    const d = Array(N).fill(0), years = p.renew ? Math.ceil(N / 12) + 1 : 1;
    for (let y = 0; y < years; y++) {
        const o = 12 * y;
        for (const am of p.ech) { const k = am + 1 + o; if (k >= 0 && k < N) d[k] -= T3S_TTC / p.ech.length; }
        const kt = p.delivery + 2 + o;
        if (kt < N) d[kt] += T3S_TVA;
        const n = 25 * p.share, enc = n * p.price, tva = (n * p.price) / 6;
        if (10 + o < N) d[10 + o] += enc / 2;
        if (11 + o < N) d[11 + o] += enc / 2;
        if (11 + o < N) d[11 + o] -= tva / 2;
        if (12 + o < N) d[12 + o] -= tva / 2;
    }
    return d;
}

/** Locations perdues tant que les T3S ne sont pas livrés (mois de mars à upTo), en partie reportées sur des VTC. */
export function lossFlows(p: Params, N: number, upTo: number): number[] {
    const d = Array(N).fill(0);
    for (let m = 3; m <= upTo; m++) { const k = m + 1; if (k < N && SRV[m]) d[k] -= SRV[m] * (p.pm - p.f * VTCM); }
    return d;
}

export function capexFlows(p: Params, N: number): number[] {
    const d = Array(N).fill(0);
    d[0] -= p.capex || 0;
    if (N > 2) d[2] += (p.capex || 0) / 6;
    return d;
}

/** Factures Leon Cycle (NCM) à payer en décembre et ventes correspondantes (suivi NCM du 22/11/2026), TTC. */
export function ncmFlows(p: Params, N: number): number[] {
    const d = Array(N).fill(0);
    if (!p.ncm) return d;
    const achats = [2266.39 + 1159.6, 1159.6];
    const ventes = 1119 + 1679 + 1439 + (p.cg7 ? 0 : 2159);
    const tvaNette = (1119 + 1679 + 1439 + 2159 - (2266.39 + 1159.6 + 1159.6)) / 6;
    d[0] += ventes - achats[0];
    if (N > 1) d[1] -= achats[1];
    if (N > 2) d[2] -= tvaNette;
    return d;
}

function caWinter(p: Params): number { return sum(CA_W.slice(0, 6)) + sum(deltas(p)) / 0.48; }

/** CA à faire chaque mois de novembre à avril pour atteindre l'objectif de l'exercice en cours. */
export function caTargets(p: Params): number[] {
    const dl = deltas(p), w = CA_W.slice(0, 6).map((v, i) => v + dl[i] / 0.48), tw = sum(w), R = p.ca[0] - p.caDone;
    return w.map(x => (R * x) / tw);
}

export function caFlows(p: Params, N: number): number[] {
    const d = Array(N).fill(0);
    if (!p.caOn) return d;
    const tot = sum(CA_W), w6 = sum(CA_W.slice(0, 6));
    const extra0 = (p.ca[0] - p.caDone - caWinter(p)) * p.margeCa;
    for (let k = 0; k < Math.min(6, N); k++) d[k] += (extra0 * CA_W[k]) / w6;
    for (let k = 6; k < N; k++) {
        const e = Math.floor((k - 6) / 12) + 1, X = p.ca[Math.min(e, p.ca.length - 1)];
        d[k] += ((X - caBase(p)) * p.margeCa * CA_W[k % 12]) / tot;
    }
    return d;
}

/** Fin des prêts : l'échéance du mois am est prélevée dans la période k = am + 1. */
export function loanFlows(p: Params, N: number): number[] {
    const d = Array(N).fill(0);
    for (let k = 0; k < N; k++) if (k >= p.loanEnd + 2) d[k] += LOAN_M;
    return d;
}

function balances(flows: number[]): number[] { let b = START; return flows.map(x => (b += x)); }

export function sim(p: Params, N: number, mode: 'avec' | 'sans'): number[] {
    const base = baseFlows(p, N), pay = payPlan(p, N).out;
    const cx = capexFlows(p, N), nc = ncmFlows(p, N), ca = caFlows(p, N), ln = loanFlows(p, N);
    const loss = lossFlows(p, N, mode === 'sans' ? 8 : p.delivery - 1);
    const t3 = mode === 'sans' ? Array(N).fill(0) : t3sFlows(p, N);
    return balances(base.map((x, i) => x - pay[i] + t3[i] + loss[i] + cx[i] + nc[i] + ca[i] + ln[i]));
}

/** Gain de l'exercice 2027-28 (mai 2027 à avril 2028) avant votre salaire, sans les achats ponctuels de 2026. */
export function exSurplus(p: Params): number { const a = sim({ ...p, D: 0, capex: 0, ncm: false }, 18, 'avec'); return a[17] - a[5]; }

export function stats(pa: number[], thresh: number): Stats {
    let mn = Infinity, am = 0, neg = -1, low = -1;
    pa.forEach((v, k) => { if (v < mn) { mn = v; am = k; } if (neg < 0 && v < 0) neg = k; if (low < 0 && v < thresh) low = k; });
    return { min: mn, kmin: am, neg, low };
}
