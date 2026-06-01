import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { AxiosError } from 'axios';

interface Props {
    onRegister: (data: {
        nom: string;
        email: string;
        password: string;
        password_confirmation: string;
    }) => Promise<void>;
}

export default function InscriptionPage({ onRegister }: Props) {
    const navigate = useNavigate();
    const [nom, setNom] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [error, setError] = useState('');
    const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        setFieldErrors({});
        setIsLoading(true);

        try {
            await onRegister({ nom, email, password, password_confirmation: passwordConfirmation });
            navigate('/dashboard');
        } catch (err) {
            const axiosErr = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>;
            if (axiosErr.response?.status === 422) {
                const data = axiosErr.response.data;
                if (data.errors) {
                    setFieldErrors(data.errors);
                } else {
                    setError(data.message ?? 'Erreur de validation.');
                }
            } else if (axiosErr.response?.status === 403) {
                setError('Cette adresse email n\'est pas autorisée à créer un compte partenaire.');
            } else {
                setError('Une erreur est survenue. Veuillez réessayer.');
            }
        } finally {
            setIsLoading(false);
        }
    };

    const inputStyle: React.CSSProperties = {
        width: '100%',
        padding: '0.6rem 0.75rem',
        border: '1px solid #ced4da',
        borderRadius: '4px',
        fontSize: '1rem',
        boxSizing: 'border-box',
    };

    const fieldError = (field: string) => fieldErrors[field]?.[0];

    return (
        <div style={{ minHeight: '100vh', background: '#f8f9fa', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <div style={{ background: 'white', borderRadius: '8px', padding: '2.5rem', width: '100%', maxWidth: '440px', boxShadow: '0 1px 3px rgba(0,0,0,0.1)' }}>
                <h1 style={{ fontSize: '1.5rem', marginBottom: '0.5rem', textAlign: 'center' }}>
                    Créer votre compte
                </h1>
                <p style={{ color: '#6c757d', textAlign: 'center', marginBottom: '2rem', fontSize: '0.9rem' }}>
                    Les Vélos d'Armor — Espace partenaire
                </p>

                <form onSubmit={handleSubmit}>
                    <div style={{ marginBottom: '1rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.4rem', fontWeight: 500, fontSize: '0.9rem' }}>
                            Nom de l'établissement
                        </label>
                        <input
                            type="text"
                            value={nom}
                            onChange={(e) => setNom(e.target.value)}
                            required
                            autoComplete="organization"
                            style={{ ...inputStyle, borderColor: fieldError('nom') ? '#dc3545' : '#ced4da' }}
                        />
                        {fieldError('nom') && (
                            <p style={{ color: '#dc3545', fontSize: '0.8rem', marginTop: '0.25rem' }}>{fieldError('nom')}</p>
                        )}
                    </div>

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
                            style={{ ...inputStyle, borderColor: fieldError('email') ? '#dc3545' : '#ced4da' }}
                        />
                        {fieldError('email') && (
                            <p style={{ color: '#dc3545', fontSize: '0.8rem', marginTop: '0.25rem' }}>{fieldError('email')}</p>
                        )}
                        <p style={{ color: '#6c757d', fontSize: '0.8rem', marginTop: '0.25rem' }}>
                            Doit correspondre à l'adresse communiquée à Les Vélos d'Armor.
                        </p>
                    </div>

                    <div style={{ marginBottom: '1rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.4rem', fontWeight: 500, fontSize: '0.9rem' }}>
                            Mot de passe
                        </label>
                        <input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                            autoComplete="new-password"
                            style={{ ...inputStyle, borderColor: fieldError('password') ? '#dc3545' : '#ced4da' }}
                        />
                        {fieldError('password') && (
                            <p style={{ color: '#dc3545', fontSize: '0.8rem', marginTop: '0.25rem' }}>{fieldError('password')}</p>
                        )}
                    </div>

                    <div style={{ marginBottom: '1.5rem' }}>
                        <label style={{ display: 'block', marginBottom: '0.4rem', fontWeight: 500, fontSize: '0.9rem' }}>
                            Confirmer le mot de passe
                        </label>
                        <input
                            type="password"
                            value={passwordConfirmation}
                            onChange={(e) => setPasswordConfirmation(e.target.value)}
                            required
                            autoComplete="new-password"
                            style={{ ...inputStyle, borderColor: fieldError('password_confirmation') ? '#dc3545' : '#ced4da' }}
                        />
                        {fieldError('password_confirmation') && (
                            <p style={{ color: '#dc3545', fontSize: '0.8rem', marginTop: '0.25rem' }}>{fieldError('password_confirmation')}</p>
                        )}
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
                        {isLoading ? 'Création...' : 'Créer mon compte'}
                    </button>
                </form>

                <p style={{ textAlign: 'center', marginTop: '1.5rem', fontSize: '0.875rem', color: '#6c757d' }}>
                    Déjà un compte ?{' '}
                    <Link to="/login" style={{ color: '#0d6efd' }}>Se connecter</Link>
                </p>
            </div>
        </div>
    );
}
