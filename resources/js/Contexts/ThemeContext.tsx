import { createContext, useContext, useEffect, useState, ReactNode } from 'react';

export type MessagingTheme = 'default' | 'starcraft';

interface ThemeContextType {
    theme: MessagingTheme;
    setTheme: (theme: MessagingTheme) => void;
    toggleTheme: () => void;
    isStarcraft: boolean;
}

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

const STORAGE_KEY = 'messaging-theme';

export function ThemeProvider({ children }: { children: ReactNode }) {
    const [theme, setThemeState] = useState<MessagingTheme>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved === 'starcraft' || saved === 'default') {
                return saved;
            }
        }
        return 'default';
    });

    useEffect(() => {
        localStorage.setItem(STORAGE_KEY, theme);
    }, [theme]);

    const setTheme = (newTheme: MessagingTheme) => {
        setThemeState(newTheme);
    };

    const toggleTheme = () => {
        setThemeState(prev => prev === 'default' ? 'starcraft' : 'default');
    };

    return (
        <ThemeContext.Provider value={{
            theme,
            setTheme,
            toggleTheme,
            isStarcraft: theme === 'starcraft',
        }}>
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme(): ThemeContextType {
    const context = useContext(ThemeContext);
    if (context === undefined) {
        throw new Error('useTheme must be used within a ThemeProvider');
    }
    return context;
}
