import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import { View, TextInput, Button, Text, ActivityIndicator, Alert } from 'react-native';
import { api } from '../../services/api'; 

const SignUp = () => {
  const router = useRouter();
  
  const [telephone, setTelephone] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleSignUp = async () => {
    if (!telephone || !password) {
      Alert.alert("Erreur", "Tous les champs sont obligatoires.");
      return;
    }

    setIsLoading(true);
    try {
      // 1. Appel vers ton endpoint d'inscription Symfony
      await api.post('/register', {
        telephone,
        password,
      });

      // 2. Alerte de succès
      Alert.alert(
        "Succès", 
        "Compte créé avec succès ! Vous pouvez maintenant vous connecter.",
        [
          { 
            text: "OK", 
            // 3. Redirection vers le login uniquement quand l'utilisateur clique sur OK
            onPress: () => router.replace('/(auth)/SignIn') 
          }
        ]
      );
      
    } catch (error: any) {
      console.error("Erreur lors de l'inscription :", error.response?.data || error.message);
      
      // On peut même personnaliser l'erreur selon le retour de Symfony (ex: téléphone déjà pris)
      const errorMessage = error.response?.data?.message || "Impossible de créer le compte. Veuillez réessayer.";
      Alert.alert("Erreur", errorMessage);
      
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center', padding: 20 }}>
      <Text style={{ fontSize: 24, marginBottom: 20, textAlign: 'center' }}>Créer un compte</Text>
      
      <TextInput 
        placeholder="Numéro de téléphone" 
        value={telephone} 
        onChangeText={setTelephone} 
        keyboardType="phone-pad"
        autoCapitalize="none"
        style={{ borderWidth: 1, borderColor: '#ccc', padding: 10, marginBottom: 15, borderRadius: 5 }}
      />
      
      <TextInput 
        placeholder="Mot de passe" 
        value={password} 
        onChangeText={setPassword} 
        secureTextEntry 
        style={{ borderWidth: 1, borderColor: '#ccc', padding: 10, marginBottom: 15, borderRadius: 5 }}
      />
      
      {isLoading ? (
        <ActivityIndicator size="large" color="#0000ff" />
      ) : (
        <Button title="S'inscrire" onPress={handleSignUp} />
      )}
    </View>
  );
};

export default SignUp;