import { Navigate } from 'react-router-dom';

interface Props {
    isAuthenticated: boolean;
    children: React.ReactNode;
}

export default function ProtectedRoute({ isAuthenticated, children }: Props) {
    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }
    return <>{children}</>;
}
