import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { MessagingProvider } from '@/Contexts/MessagingContext';

createInertiaApp({
    title: (title) => (title ? `${title} - Workshop Pilot` : 'Workshop Pilot'),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx')
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <MessagingProvider>
                <App {...props} />
            </MessagingProvider>
        );
    },
    progress: {
        color: '#2196F3',
    },
});
