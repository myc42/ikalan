import { Slot, useRouter, useSegments } from 'expo-router';
import { useEffect } from 'react';
import { View, ActivityIndicator } from 'react-native';

// N'oublie pas d'ajuster ce chemin vers l'endroit où tu as créé ton AuthContext
import { AuthProvider, useSession } from '../context/AuthContext'; 

// 1. On crée le composant interne qui va surveiller la session
function InitialLayout() {
  const { session, isLoading } = useSession();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    // Si on est en train de charger, on ne fait rien avec la navigation
    if (isLoading) return;

    // On vérifie si l'utilisateur est actuellement dans le groupe de pages de connexion (ex: /login)
    const inAuthGroup = segments[0] === '(auth)';

    if (!session && !inAuthGroup) {
      // Pas de session et on n'est PAS dans (auth) -> Redirection vers la page de connexion
      router.replace('/(auth)/signIn');
    } else if (session && inAuthGroup) {
      // Session active mais on est dans (auth) -> Redirection vers l'intérieur de l'application
      // Remplace "/(app)/home" par le vrai chemin de ta page d'accueil
      router.replace('/(tabs)/home'); 
    }
  }, [session, isLoading, segments]);

  // Si l'application vérifie encore les jetons au démarrage, on affiche un écran de chargement
  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" color="#0000ff" />
      </View>
    );
  }

  // Sinon, on affiche normalement les pages
  return <Slot />;
}

// Le composant exporté par défaut enveloppe notre layout avec le Provider
export default function RootLayout() {
  return (
    <AuthProvider>
      <InitialLayout />
    </AuthProvider>
  );
}