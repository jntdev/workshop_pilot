interface Props {
    nomEtablissement?: string;
    onLogout: () => void;
    children: React.ReactNode;
}

export default function PartenaireLayout({ nomEtablissement, onLogout, children }: Props) {
    return (
        <div style={{ minHeight: '100vh', background: '#f8f9fa' }}>
            <header style={{
                background: 'white',
                borderBottom: '1px solid #dee2e6',
                padding: '0 2rem',
                height: '60px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
            }}>
                <span style={{ fontWeight: 600, fontSize: '1rem', color: '#212529' }}>
                    Les Vélos d'Armor — Espace partenaire
                    {nomEtablissement && (
                        <span style={{ fontWeight: 400, color: '#6c757d', marginLeft: '0.75rem' }}>
                            · {nomEtablissement}
                        </span>
                    )}
                </span>
                <button
                    type="button"
                    onClick={onLogout}
                    style={{
                        background: 'none',
                        border: 'none',
                        color: '#6c757d',
                        cursor: 'pointer',
                        fontSize: '0.9rem',
                    }}
                >
                    Déconnexion
                </button>
            </header>
            <main style={{ padding: '2rem', maxWidth: '900px', margin: '0 auto' }}>
                {children}
            </main>
        </div>
    );
}
