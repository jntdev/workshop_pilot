import { Link } from '@inertiajs/react';
import { useMessaging } from '@/Contexts/MessagingContext';

export default function MessagingButton() {
    const { unreadCount } = useMessaging();

    return (
        <Link
            href="/messages"
            className="layout-nav__link layout-nav__link--with-badge"
            aria-label={`Messages - ${unreadCount} non lus`}
        >
            Messages
            {unreadCount > 0 && (
                <span className="layout-nav__badge">{unreadCount}</span>
            )}
        </Link>
    );
}
