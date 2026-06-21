import React, { createContext, useContext, useEffect, useState } from 'react';
import { DeviceEventEmitter } from 'react-native';
import { getAccessToken, clearTokens } from '../utils/token'; // Ajuste le chemin

interface AuthContextType {
  signIn: (token: string) => void;
  signOut: () => void;
  session: string | null;
  isLoading: boolean;
}

const AuthContext = createContext<AuthContextType | null>(null);

export function useSession() {
  const value = useContext(AuthContext);
  if (!value) {
    throw new Error('useSession doit être utilisé dans un AuthProvider');
  }
  return value;
}

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [session, setSession] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    // 1. Vérification initiale au lancement de l'app
    getAccessToken().then((token) => {
      setSession(token);
      setIsLoading(false);
    });

    // 2. Écoute de l'événement de déconnexion forcé d'Axios
    const subscription = DeviceEventEmitter.addListener('onSessionExpired', signOut);

    return () => {
      subscription.remove();
    };
  }, []);

  const signIn = (token: string) => {
    setSession(token);
  };

  const signOut = async () => {
    await clearTokens();
    setSession(null);
  };

  return (
    <AuthContext.Provider value={{ signIn, signOut, session, isLoading }}>
      {children}
    </AuthContext.Provider>
  );
}