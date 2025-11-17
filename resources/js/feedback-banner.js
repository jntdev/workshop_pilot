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

        // Retirer toutes les classes de type précédentes
        this.element.classList.remove('feedback-banner--success', 'feedback-banner--error', 'feedback-banner--hidden');

        // Ajouter la classe BEM appropriée
        this.element.classList.add(`feedback-banner--${type}`);

        // Mettre à jour le message
        this.messageElement.textContent = message;

        // Programmer le masquage automatique
        this.hideTimeout = setTimeout(() => {
            this.hide();
        }, HIDE_DELAY);
    }

    hide() {
        if (!this.element) return;

        this.element.classList.remove('feedback-banner--success', 'feedback-banner--error');
        this.element.classList.add('feedback-banner--hidden');
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
