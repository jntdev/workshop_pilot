import { ASSISTANT_TAB_NAME } from '@/lib/assistantBus';

/**
 * Bouton « Assistant » en bas à droite de chaque page du workshop : il ouvre la conversation dans son
 * propre onglet (réutilisé s'il est déjà ouvert). Ainsi la page de travail reste visible en entier, et
 * ce que fait l'assistant s'y affiche sans rafraîchir.
 */
export default function AssistantLauncher() {
    return (
        <div className="assistant-launcher">
            <a
                href="/assistant"
                target={ASSISTANT_TAB_NAME}
                className="assistant-launcher__button"
                title="Ouvre l'assistant dans un onglet à part"
            >
                Assistant <span aria-hidden="true">&#8599;</span>
            </a>
        </div>
    );
}
