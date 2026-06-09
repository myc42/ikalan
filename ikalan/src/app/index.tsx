import "../../global.css";
import { Text, View } from "react-native";


export default function Index() {
  return (
    <View className="flex-1 items-center justify-center bg-white">
      <Text className="text-2xl font-bold text-blue-500">
        NativeWind fonctionne ! 🎉
      </Text>
      <Text className="mt-4 text-base text-gray-500">
        Tailwind CSS dans React NativeX
      </Text>
    </View>
  );
}
