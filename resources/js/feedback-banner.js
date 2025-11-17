/**
 * Feedback Banner Module
 * Gère l'affichage et le masquage automatique des messages de feedback
 */

const HIDE_DELAY = 3000; // 3 secondes

class FeedbackBanner {
    constructor() {
        this.element = document.getElementById('feedback-banner');
        this.messageElement = this.element?.querySelector('.feedback-banner__message');
        this.hideTimeout = null;

        this.init();
    }

    init() {
        if (!this.element) return;

        // Initialiser avec les données de session si disponibles
        if (window.feedbackBannerData) {
            this.show(window.feedbackBannerData.type, window.feedbackBannerData.message);
            delete window.feedbackBannerData;
        }

        // Écouter les événements Livewire
        window.addEventListener('feedback-banner', (event) => {
            const { type, message } = event.detail;
            this.show(type, message);
        });
    }

    show(type, message) {
        if (!this.element || !this.messageElement) return;

        // Effacer le timeout précédent si existant
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
        }

        // Mettre à jour le contenu et le type
        this.messageElement.textContent = message;
        this.element.setAttribute('data-type', type);
        this.element.setAttribute('data-visible', 'true');

        // Programmer le masquage automatique
        this.hideTimeout = setTimeout(() => {
            this.hide();
        }, HIDE_DELAY);
    }

    hide() {
        if (!this.element) return;

        this.element.setAttribute('data-visible', 'false');
        this.hideTimeout = null;
    }
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    new FeedbackBanner();
});

// Initialiser aussi pour Livewire (navigation SPA)
document.addEventListener('livewire:navigated', () => {
    new FeedbackBanner();
});
