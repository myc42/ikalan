import "../../global.css";
import { Text, View, StyleSheet, Button } from "react-native";
import { useEffect, useState } from "react";

export default function Index() {
  const [reponse, setReponse] = useState("En attente du test...");

  const testSymfony = async () => {
    setReponse("Appel en cours...");

    const URL = `http://${process.env.EXPO_PUBLIC_API_URL}:8000/api/ping`;
    
    // REGARDE TON TERMINAL EXPO AVEC ÇA :
    console.log("URL tentée par React Native :", URL); 

    try {
      const res = await fetch(URL);
      const data = await res.json();
      setReponse(`REÇU DE SYMFONY : \n${data.message}`);
    } catch (e: any) {
      setReponse(`ÉCHEC : \n${e.message}`);
    }
  };

  // Ce bloc déclenche la fonction automatiquement à l'ouverture de l'appli
  useEffect(() => {
    testSymfony();
  }, []);

  return (
    <View style={styles.container}>
      <Button title="RELANCER LE TEST" onPress={testSymfony} />
      <Text style={styles.box}>{reponse}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', padding: 20 },
  box: { marginTop: 20, padding: 15, backgroundColor: '#eee', fontWeight: 'bold', textAlign: 'center' }
});