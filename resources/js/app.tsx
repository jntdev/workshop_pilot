import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { MessagingProvider } from '@/Contexts/MessagingContext';
import type { PageProps } from '@/types';

createInertiaApp({
    title: (title) => (title ? `${title} - Workshop Pilot` : 'Workshop Pilot'),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx')
        ),
    setup({ el, App, props }) {
        const pageProps = (props.initialPage.props as unknown as PageProps);
        const currentUserId = pageProps?.auth?.user?.id ?? null;
        const users = pageProps?.messaging_users ?? [];
        const root = createRoot(el);
        root.render(
            <MessagingProvider currentUserId={currentUserId} users={users}>
                <App {...props} />
            </MessagingProvider>
        );
    },
    progress: {
        color: '#2196F3',
    },
});
