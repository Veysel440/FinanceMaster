import React, { createContext, useContext, useState, useEffect } from 'react';
import api from '../api/axios';

type User = {
    id: number;
    name: string;
    email: string;
    currency?: string;
};

type AuthContextType = {
    user: User | null;
    loading: boolean;
    login: (email: string, password: string) => Promise<boolean>;
    logout: () => Promise<void>;
};

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{children: React.ReactNode}> = ({ children }) => {
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/profile').then(res => setUser(res.data)).finally(() => setLoading(false));
    }, []);

    const login = async (email: string, password: string) => {
        try {
            await api.get('/sanctum/csrf-cookie'); // Sanctum için!
            const res = await api.post('/login', { email, password });
            setUser(res.data.user);
            return true;
        } catch (e) {
            return false;
        }
    };

    const logout = async () => {
        await api.post('/logout');
        setUser(null);
    };

    return (
        <AuthContext.Provider value={{ user, loading, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
};

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) throw new Error('useAuth must be used within AuthProvider');
    return context;
}
