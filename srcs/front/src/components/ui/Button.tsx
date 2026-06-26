import { TouchableOpacity, Text, View } from "react-native";
import { useState } from "react";
import { Pressable } from "react-native";

type Variant =
  | "default"
  | "primary"
  | "primaryOutline"
  | "secondary"
  | "secondaryOutline"
  | "danger"
  | "dangerOutline"
  | "super"
  | "superOutline"
  | "ghost";

type Size = "sm" | "md" | "lg";

interface ButtonProps {
  label: string;
  onPress?: () => void;
  variant?: Variant;
  size?: Size;
  disabled?: boolean;
  fullWidth?: boolean;
}

// Couleurs hardcodées — NativeWind ne purge pas les valeurs inline
const styles: Record<Variant, {
  bg: string;
  border: string;
  shadow: string;
  text: string;
}> = {
  default: {
    bg:     "#FFFFFF",
    border: "#E5E7EB",
    shadow: "#C5C5C5",
    text:   "#0D1328",
  },
  primary: {
    bg:     "#6C4EF5",
    border: "#6C4EF5",
    shadow: "#4A2FCC",
    text:   "#FFFFFF",
  },
  primaryOutline: {
    bg:     "#FFFFFF",
    border: "#6C4EF5",
    shadow: "#6C4EF5",
    text:   "#6C4EF5",
  },
  secondary: {
    bg:     "#21C16B",
    border: "#21C16B",
    shadow: "#18A058",
    text:   "#FFFFFF",
  },
  secondaryOutline: {
    bg:     "#FFFFFF",
    border: "#21C16B",
    shadow: "#21C16B",
    text:   "#21C16B",
  },
  danger: {
    bg:     "#FF4D4F",
    border: "#FF4D4F",
    shadow: "#CC2E30",
    text:   "#FFFFFF",
  },
  dangerOutline: {
    bg:     "#FFFFFF",
    border: "#FF4D4F",
    shadow: "#FF4D4F",
    text:   "#FF4D4F",
  },
  super: {
    bg:     "#FFC800",
    border: "#FFC800",
    shadow: "#CC9F00",
    text:   "#FFFFFF",
  },
  superOutline: {
    bg:     "#FFFFFF",
    border: "#FFC800",
    shadow: "#FFC800",
    text:   "#FFC800",
  },
  ghost: {
    bg:     "transparent",
    border: "transparent",
    shadow: "transparent",
    text:   "#6B7280",
  },
};

const sizeMap: Record<Size, { px: number; py: number; fontSize: number; radius: number; shadowOffset: number }> = {
  sm: { px: 16, py: 8,  fontSize: 13, radius: 10, shadowOffset: 3 },
  md: { px: 24, py: 14, fontSize: 15, radius: 12, shadowOffset: 4 },
  lg: { px: 32, py: 18, fontSize: 17, radius: 14, shadowOffset: 5 },
};
export function Button({
  label,
  onPress,
  variant = "default",
  size = "md",
  disabled = false,
  fullWidth = false,
}: ButtonProps) {
  const s = styles[variant];
  const d = sizeMap[size];
  const [pressed, setPressed] = useState(false);

  return (
    <Pressable
      onPress={onPress}
      disabled={disabled}
      onPressIn={() => setPressed(true)}
      onPressOut={() => setPressed(false)}
      style={{
        width: fullWidth ? "100%" : undefined,
        alignSelf: fullWidth ? "stretch" : "flex-start",
      }}
    >
      {/* 1. L'OMBRE (Figée tout en bas en position absolue) */}
      <View
        style={{
          position: "absolute",
          top: d.shadowOffset, // Elle commence plus bas
          bottom: 0,
          left: 0,
          right: 0,
          backgroundColor: disabled ? "#E5E7EB" : s.shadow,
          borderRadius: d.radius,
          borderWidth: 2,
          borderColor: disabled ? "#E5E7EB" : s.border,
        }}
      />

      {/* 2. LA FACE DU BOUTON (C'elle qui descend visuellement) */}
      <View
        style={{
          backgroundColor: disabled ? "#F3F4F6" : s.bg,
          borderRadius: d.radius,
          borderWidth: 2,
          borderColor: disabled ? "#E5E7EB" : s.border,
          paddingHorizontal: d.px,
          paddingVertical: d.py,
          alignItems: "center",
          justifyContent: "center",

          // LA MAGIE EST ICI :
          marginBottom: d.shadowOffset, // 1. Réserve l'espace physique de l'ombre de manière statique
          transform: [{ translateY: pressed ? d.shadowOffset : 0 }], // 2. Déplace l'image sans toucher au layout
        }}
      >
        <Text
          style={{
            fontFamily: "Poppins_700Bold",
            fontSize: d.fontSize,
            color: disabled ? "#9CA3AF" : s.text,
            letterSpacing: 0.3,
          }}
        >
          {label}
        </Text>
      </View>
    </Pressable>
  );
}