import { Link, usePage, router } from '@inertiajs/react';
import { ReactNode } from 'react';
import { PageProps } from '@/types';
import { useServerWarmup } from '@/hooks/useServerWarmup';
import { usePrivacyMode } from '@/hooks/usePrivacyMode';

interface MainLayoutProps {
    children: ReactNode;
    title?: string;
}

export default function MainLayout({ children, title }: MainLayoutProps) {
    const { flash } = usePage<PageProps>().props;
    const { isComptoir, toggle } = usePrivacyMode();

    // Warmup silencieux du serveur pour les hébergements mutualisés
    useServerWarmup();

    const handleLogout = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <div className="layout" data-privacy-mode={isComptoir ? 'comptoir' : 'atelier'}>
            <header className="layout-header">
                <div className="layout-header__inner">
                    <div className="layout-header__bar">
                        <h1 className="layout-header__title">
                            <Link href="/">Workshop Pilot</Link>
                        </h1>

                        <nav className="layout-nav">
                            <Link href="/location" className="layout-nav__link">
                                Location
                            </Link>
                            <Link href="/clients" className="layout-nav__link">
                                Clients
                            </Link>
                            <Link href="/atelier" className="layout-nav__link">
                                Atelier
                            </Link>
                            <Link href="/dashboard" className="layout-nav__link">
                                Dashboard
                            </Link>
                            <Link href="/bikes" className="layout-nav__link">
                                Velos
                            </Link>

                            <div className="privacy-toggle" title="Ctrl+Shift+P pour basculer">
                                <span className={`privacy-toggle__label ${!isComptoir ? 'privacy-toggle__label--active' : ''}`}>
                                    Atelier
                                </span>
                                <button
                                    type="button"
                                    className={`privacy-toggle__switch ${isComptoir ? 'privacy-toggle__switch--comptoir' : ''}`}
                                    onClick={toggle}
                                    aria-label="Basculer entre mode Atelier et Comptoir"
                                >
                                    <span className="privacy-toggle__slider" />
                                </button>
                                <span className={`privacy-toggle__label ${isComptoir ? 'privacy-toggle__label--active' : ''}`}>
                                    Comptoir
                                </span>
                            </div>

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

            {isComptoir && <div className="privacy-banner" />}

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
