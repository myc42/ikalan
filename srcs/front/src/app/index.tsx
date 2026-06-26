import "../../global.css";
import { Text, View } from "react-native";
import { useEffect, useState } from "react";
import { router } from "expo-router"
import * as SecureStore from 'expo-secure-store';
import {Link} from 'expo-router'
import { Button } from "@/components/ui";





export default function Index() {

  
  return (
    <View className="flex-1 items-center justify-center bg-[#FFFFFF] px-6 gap-4"><Text className="text-h1 font-bold text-primary mb-2">
         title
        </Text>
        <Text className="text-h2 font-bold text-primary mb-2">
         title
        </Text>

  <Link href="/signUp">Sign Up</Link>
  <Link href="/home">Home</Link>

<Button label="Commencer" variant="primary"          fullWidth onPress={() => router.push("/signUp")} />
<Button label="Connexion" variant="primaryOutline"  fullWidth onPress={() => router.push("/home")} />

</View>

    
  );
}
