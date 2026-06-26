src/
├── app/
│   ├── _layout.tsx          ← polices + providers
│   └── (tabs)/
│       └── index.tsx
├── components/
│   └── ui/
│       ├── Button.tsx
│       ├── Card.tsx
│       ├── Typography.tsx
│       └── index.ts         ← barrel export
├── global.css               ← @tailwind directives (inchangé)
tailwind.config.js           ← 🎨 CHARTE GRAPHIQUE (couleurs, fonts, spacing)



Résumé de la règle d'or
Ce que tu veux définirOù le mettreCouleurs, typographie, spacing, radiustailwind.config.js →
theme.extendChargement des polices_layout.tsx (une seule fois)
Composants réutilisablessrc/components/ui/CSS global (reset, base)global.css