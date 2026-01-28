import { Link, usePage, router } from '@inertiajs/react';
import { ReactNode } from 'react';
import { PageProps } from '@/types';

interface MainLayoutProps {
    children: ReactNode;
    title?: string;
}

export default function MainLayout({ children, title }: MainLayoutProps) {
    const { flash } = usePage<PageProps>().props;

    const handleLogout = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <div className="layout">
            <header className="layout-header">
                <div className="layout-header__inner">
                    <div className="layout-header__bar">
                        <h1 className="layout-header__title">
                            <Link href="/">Workshop Pilot</Link>
                        </h1>

                        <nav className="layout-nav">
                            <Link href="/" className="layout-nav__link">
                                Accueil
                            </Link>
                            <Link href="/clients" className="layout-nav__link">
                                Clients
                            </Link>
                            <Link href="/atelier" className="layout-nav__link">
                                Atelier
                            </Link>
                            <Link href="/location" className="layout-nav__link">
                                Location
                            </Link>

                            <form onSubmit={handleLogout} style={{ display: 'inline' }}>
                                <button
                                    type="submit"
                                    className="layout-nav__link layout-nav__link--logout"
                                >
                                    Déconnexion
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>
            </header>

            {(flash.message || flash.error) && (
                <div className="feedback-host">
                    {flash.message && (
                        <div className="alert alert--success">{flash.message}</div>
                    )}
                    {flash.error && (
                        <div className="alert alert--error">{flash.error}</div>
                    )}
                </div>
            )}

            <main className="layout-main">{children}</main>
        </div>
    );
}
