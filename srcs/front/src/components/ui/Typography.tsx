// src/components/ui/Typography.tsx
import { Text, TextProps } from "react-native";

interface TypoProps extends TextProps {
  children: React.ReactNode;
  className?: string;
}

// H1 — Page / Screen Title — 32px Bold
export const H1 = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-bold text-h1 text-text-primary ${className}`} {...props}>
    {children}
  </Text>
);

// H2 — Section Title — 24px SemiBold
export const H2 = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-semibold text-h2 text-text-primary ${className}`} {...props}>
    {children}
  </Text>
);

// H3 — Card / Module Title — 20px SemiBold
export const H3 = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-semibold text-h3 text-text-primary ${className}`} {...props}>
    {children}
  </Text>
);

// H4 — Subheading — 16px Medium
export const H4 = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-medium text-h4 text-text-primary ${className}`} {...props}>
    {children}
  </Text>
);

// Body Large — Important content — 16px Regular
export const BodyLg = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-sans text-body-lg text-text-primary ${className}`} {...props}>
    {children}
  </Text>
);

// Body Medium — Body text — 14px Regular
export const BodyMd = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-sans text-body-md text-text-primary ${className}`} {...props}>
    {children}
  </Text>
);

// Body Small — Supporting text — 13px Regular
export const BodySm = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-sans text-body-sm text-text-secondary ${className}`} {...props}>
    {children}
  </Text>
);

// Caption — Labels, meta text — 11px Regular
export const Caption = ({ children, className = "", ...props }: TypoProps) => (
  <Text className={`font-sans text-caption text-text-secondary ${className}`} {...props}>
    {children}
  </Text>
);