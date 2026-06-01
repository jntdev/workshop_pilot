import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './hooks/useAuth';
import ProtectedRoute from './components/ProtectedRoute';
import LoginPage from './pages/LoginPage';
import InscriptionPage from './pages/InscriptionPage';
import DashboardPage from './pages/DashboardPage';

export default function App() {
    const { isAuthenticated, partenaire, login, register, logout } = useAuth();

    return (
        <BrowserRouter>
            <Routes>
                <Route
                    path="/login"
                    element={
                        isAuthenticated
                            ? <Navigate to="/dashboard" replace />
                            : <LoginPage onLogin={login} />
                    }
                />
                <Route
                    path="/inscription"
                    element={
                        isAuthenticated
                            ? <Navigate to="/dashboard" replace />
                            : <InscriptionPage onRegister={register} />
                    }
                />
                <Route
                    path="/dashboard"
                    element={
                        <ProtectedRoute isAuthenticated={isAuthenticated}>
                            <DashboardPage partenaire={partenaire} onLogout={logout} />
                        </ProtectedRoute>
                    }
                />
                <Route path="*" element={<Navigate to={isAuthenticated ? '/dashboard' : '/login'} replace />} />
            </Routes>
        </BrowserRouter>
    );
}
