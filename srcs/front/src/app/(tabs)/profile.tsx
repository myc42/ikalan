import { View, Text, Button, StyleSheet } from 'react-native';
import React from 'react';
// 1. Importe le raccourci useSession (ajuste le chemin selon ton projet)
import { useSession } from '../../context/AuthContext'; 

export default function Profile() {
  // 2. Récupère la fonction signOut depuis le "Wi-Fi" de ton AuthProvider
  const { signOut } = useSession();

  return (
    <View style={styles.container}>
      <Text style={styles.text}>Mon Profil</Text>

      {/* 3. Ajoute un bouton qui déclenche la déconnexion au clic */}
      <Button 
        title="Se déconnecter" 
        onPress={signOut} 
        color="#ff3b30" // Une couleur rouge pour la déconnexion
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#fff',
  },
  text: {
    fontSize: 20,
    marginBottom: 20,
  },
});