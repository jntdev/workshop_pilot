import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { AxiosError } from 'axios';

interface Props {
    onLogin: (email: string, password: string) => Promise<void>;
}

export default function LoginPage({ onLogin }: Props) {
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        setIsLoading(true);

        try {
            await onLogin(email, password);
            navigate('/dashboard');
        } catch (err) {
            const axiosErr = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>;
            if (axiosErr.response?.status === 403) {
                setError('Votre compte a été désactivé. Contactez-nous pour plus d\'informations.');
            } else {
                setError('Identifiants incorrects.');
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div style={{ minHeight: '100vh', background: '#f8f9fa', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <div style={{ background: 'white', borderRadius: '8px', padding: '2.5rem', width: '100%', maxWidth: '400px', boxShadow: '0 1px 3px rgba(0,0,0,0.1)' }}>
                <h1 style={{ fontSize: '1.5rem', marginBottom: '0.5rem', textAlign: 'center' }}>
                    Espace partenaire
                </h1>
                <p style={{ color: '#6c757d', textAlign: 'center', marginBottom: '2rem', fontSize: '0.9rem' }}>
                    Les Vélos d'Armor
                </p>

                <form onSubmit={handleSubmit}>
                    <div style={{ marginBottom: '1rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.4rem', fontWeight: 500, fontSize: '0.9rem' }}>
                            Adresse email
                        </label>
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                            autoComplete="email"
                            style={{ width: '100%', padding: '0.6rem 0.75rem', border: '1px solid #ced4da', borderRadius: '4px', fontSize: '1rem', boxSizing: 'border-box' }}
                        />
                    </div>

                    <div style={{ marginBottom: '1.5rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.4rem', fontWeight: 500, fontSize: '0.9rem' }}>
                            Mot de passe
                        </label>
                        <input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                            autoComplete="current-password"
                            style={{ width: '100%', padding: '0.6rem 0.75rem', border: '1px solid #ced4da', borderRadius: '4px', fontSize: '1rem', boxSizing: 'border-box' }}
                        />
                    </div>

                    {error && (
                        <p style={{ color: '#dc3545', fontSize: '0.875rem', marginBottom: '1rem', padding: '0.6rem', background: '#fff5f5', borderRadius: '4px' }}>
                            {error}
                        </p>
                    )}

                    <button
                        type="submit"
                        disabled={isLoading}
                        style={{ width: '100%', padding: '0.75rem', background: '#0d6efd', color: 'white', border: 'none', borderRadius: '4px', fontSize: '1rem', cursor: 'pointer', fontWeight: 500 }}
                    >
                        {isLoading ? 'Connexion...' : 'Se connecter'}
                    </button>
                </form>

                <p style={{ textAlign: 'center', marginTop: '1.5rem', fontSize: '0.875rem', color: '#6c757d' }}>
                    Première connexion ?{' '}
                    <Link to="/inscription" style={{ color: '#0d6efd' }}>Créer votre compte</Link>
                </p>
            </div>
        </div>
    );
}
