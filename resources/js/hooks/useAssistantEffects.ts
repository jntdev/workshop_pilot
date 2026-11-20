import { useEffect, useRef } from 'react';
import { AssistantEffect, subscribeAssistantEffects } from '@/lib/assistantBus';

/**
 * Pour une page du workshop : appelle `handler` quand l'assistant, dans son propre onglet, vient de
 * modifier quelque chose (statut d'un devis, pièce commandée, rendez-vous déplacé).
 *
 * Le handler met à jour l'état de la page quand il le peut (affichage immédiat, sans réseau) et ne recharge
 * les données qu'en dernier recours. Une page que l'effet ne concerne pas l'ignore.
 */
export function useAssistantEffects(handler: (effect: AssistantEffect) => void): void {
    const handlerRef = useRef(handler);
    handlerRef.current = handler;

    useEffect(() => subscribeAssistantEffects((effect) => handlerRef.current(effect)), []);
}
