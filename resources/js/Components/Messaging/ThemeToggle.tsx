import { useTheme } from '@/Contexts/ThemeContext';

export default function ThemeToggle() {
    const { isStarcraft, toggleTheme } = useTheme();

    return (
        <button
            type="button"
            className={`theme-toggle ${isStarcraft ? 'theme-toggle--active' : ''}`}
            onClick={toggleTheme}
            title={isStarcraft ? 'Désactiver le thème Starcraft' : 'Activer le thème Starcraft'}
        >
            <span className="theme-toggle__icon">
                {isStarcraft ? '🎮' : '🎨'}
            </span>
            <span className="theme-toggle__label">
                {isStarcraft ? 'SC: ON' : 'SC: OFF'}
            </span>
        </button>
    );
}
