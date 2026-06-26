import { ScrollView, View, Text } from "react-native";
import { Button } from "@/components/ui";

export default function ButtonsPage() {
  return (
    <ScrollView className="flex-1 bg-[#FFFFFF]">
      <View className="px-6 pt-16 pb-10 gap-4">

        <Text style={{ fontFamily: "Poppins_700Bold", fontSize: 24, color: "#0D1328", marginBottom: 8 }}>
          Boutons
        </Text>

        {/* Remplis */}
        <Text style={{ fontFamily: "Poppins_600SemiBold", fontSize: 13, color: "#6B7280", marginTop: 8 }}>
          FILLED
        </Text>
        <Button label="Default"    variant="default"    fullWidth onPress={() => {}} />
        <Button label="Primary"    variant="primary"    fullWidth onPress={() => {}} />
        <Button label="Secondary"  variant="secondary"  fullWidth onPress={() => {}} />
        <Button label="Danger"     variant="danger"     fullWidth onPress={() => {}} />
        <Button label="Super"      variant="super"      fullWidth onPress={() => {}} />

        {/* Outline */}
        <Text style={{ fontFamily: "Poppins_600SemiBold", fontSize: 13, color: "#6B7280", marginTop: 16 }}>
          OUTLINE
        </Text>
        <Button label="Primary Outline"   variant="primaryOutline"   fullWidth onPress={() => {}} />
        <Button label="Secondary Outline" variant="secondaryOutline" fullWidth onPress={() => {}} />
        <Button label="Danger Outline"    variant="dangerOutline"    fullWidth onPress={() => {}} />
        <Button label="Super Outline"     variant="superOutline"     fullWidth onPress={() => {}} />

        {/* Ghost */}
        <Text style={{ fontFamily: "Poppins_600SemiBold", fontSize: 13, color: "#6B7280", marginTop: 16 }}>
          GHOST
        </Text>
        <Button label="Ghost" variant="ghost" fullWidth onPress={() => {}} />

        {/* Tailles */}
        <Text style={{ fontFamily: "Poppins_600SemiBold", fontSize: 13, color: "#6B7280", marginTop: 16 }}>
          TAILLES
        </Text>
        <Button label="Small"  variant="primary" size="sm" onPress={() => {}} />
        <Button label="Medium" variant="primary" size="md" onPress={() => {}} />
        <Button label="Large"  variant="primary" size="lg" onPress={() => {}} />

        {/* Disabled */}
        <Text style={{ fontFamily: "Poppins_600SemiBold", fontSize: 13, color: "#6B7280", marginTop: 16 }}>
          DISABLED
        </Text>
        <Button label="Désactivé" variant="primary" fullWidth disabled onPress={() => {}} />

      </View>
    </ScrollView>
  );
}