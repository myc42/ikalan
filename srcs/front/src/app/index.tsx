import "../../global.css";
import { Text, View, Button } from "react-native";
import { useEffect, useState } from "react";
import { router } from "expo-router"
import * as SecureStore from 'expo-secure-store';
import {Link} from 'expo-router'





export default function Index() {

  
  return (
    <View className="flex-1 items-center justify-center bg-white">
      <View>
            <Link href="/signUp">Sign Up</Link>
              <Link href="/home">Home</Link>

      
      </View>

    </View>

    
  );
}
