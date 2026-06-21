import { Link } from 'expo-router'; // Plus besoin de useRouter ici !
import React, { useState } from 'react';
import { View, TextInput, Button, Text, Pressable, ActivityIndicator } from 'react-native';
import { api } from '../../services/api';
import { saveTokens } from '../../utils/token';
import { useSession } from '../../context/AuthContext'; // <-- 1. Import du contexte

interface LoginResponse {
  token: string;
  refresh_token: string;
}

const SignIn = () => {
  // 2. On récupère la fonction signIn depuis notre contexte
  const { signIn } = useSession(); 
  
  const [telephone, setTelephone] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [errorText, setErrorText] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(false);

  const handleLogin = async () => {
    if (!telephone || !password) {
      setErrorText("Veuillez remplir tous les champs.");
      return;
    }

    setIsLoading(true);
    setErrorText(null);

    try {
      const response = await api.post<LoginResponse>('/login', {
        telephone: telephone, 
        password: password,
      });

      const { token, refresh_token } = response.data;
      
      // 3. On sauvegarde les tokens dans le SecureStore
      await saveTokens(token, refresh_token);
      
      console.log("✅ Connecté avec succès !");
      
      // 4. On informe l'application que l'utilisateur est connecté
      // Le _layout.tsx va détecter ce changement d'état et faire la redirection tout seul
      signIn(token);      
      
    } catch (error: any) {
      setErrorText("Identifiants incorrects ou erreur réseau.");
      console.error("Erreur de connexion :", error.response?.data || error.message);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center' }}>
      <View style={{ padding: 20 }}>
        
        <TextInput 
          placeholder="Numéro de téléphone" 
          value={telephone} 
          onChangeText={setTelephone} 
          autoCapitalize="none"
          keyboardType="phone-pad"
          style={{ borderWidth: 1, borderColor: '#ccc', padding: 10, marginBottom: 15, borderRadius: 5 }}
        />
        
        <TextInput 
          placeholder="Mot de passe" 
          value={password} 
          onChangeText={setPassword} 
          secureTextEntry 
          style={{ borderWidth: 1, borderColor: '#ccc', padding: 10, marginBottom: 15, borderRadius: 5 }}
        />
        
        {errorText && (
          <Text style={{ color: 'red', marginVertical: 10, textAlign: 'center' }}>
            {errorText}
          </Text>
        )}
        
        {isLoading ? (
          <ActivityIndicator size="large" color="#0000ff" />
        ) : (
          <Button title="Se connecter" onPress={handleLogin} />
        )}
      </View>
      
      <View style={{ padding: 20 }}>
        <Link href="/(auth)/signUp" asChild>
          <Pressable className="mt-4 rounded bg-red-500 p-4">
            <Text className="text-white text-center">Sign up</Text>
          </Pressable>
        </Link> 
      </View>
    </View>
  );
};

export default SignIn;