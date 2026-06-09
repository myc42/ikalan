1\. Création du projet

  

Pour commencer, j’ai créé un projet mobile avec Expo.

  

Étapes réalisées :

Création d’un compte sur Expo :

Inscription sur la plateforme Expo

Connexion à l’espace développeur

Création du projet via Expo CLI :

  

npm install --global eas-cli

npx create-expo-app ikalan

cd ikalan

  

Initialisation d’EAS (Expo Application Services) :

  

eas init --id xxxxxxxxx

  

👉 EAS permet de gérer :

  

les builds Android / iOS

la publication des applications

la configuration cloud du projet Expo

  

Problème rencontré / choix des méthodes

  

En suivant des tutoriels, j’ai remarqué deux façons de créer un projet Expo.

  

⚡ Méthode simple (par défaut)

  

npx create-expo-app ikalan

  

Ce que ça fait :

crée un projet Expo avec les paramètres par défaut

utilise la dernière version stable disponible automatiquement

✔️ Avantage :

très simple et rapide à utiliser

❌ Inconvénient :

les versions peuvent varier selon le moment où la commande est exécutée

moins de contrôle sur la configuration du projet

🧱 Méthode recommandée (projet stable)

  

npx create-expo-app@latest --template default@sdk-55

  
package.json

C'est le fichier principal de ton projet.
package-lock.json

Ce fichier est généré automatiquement par npm.

Il contient :

la version exacte de chaque package installé ;
les sous-dépendances de chaque package ;
l'arbre complet des dépendances.


Rôle de app.json

C'est le fichier de configuration de ton application Expo. Il indique à Expo comment construire et afficher ton application.

2\. lancement du projet 

  

npm start 

  

choisir le type de appareil 

  
  
  

pour par exemple  android , via android studio voici la config

  
  
  

Clique sur le bouton Create Device (Créer un appareil) ou sur le petit +.

  

Choisis un modèle de téléphone (un Pixel fera très bien l'affaire pour simuler un environnement Android) et clique sur Next.

  

Tu vas devoir choisir une version d'Android. Clique sur l'icône de téléchargement à côté d'une version récente. Une fois le téléchargement terminé, sélectionne cette version et clique sur Next, puis sur Finish.

  

Ton appareil virtuel est maintenant prêt ! Dans la liste, clique sur le petit bouton "Lecture" (Play ▷) pour l'allumer.

  
  

ensuite  installer x code et le fixer le sudo xcode-select -s /Applications/Xcode.app/Contents/Developer

  

nettoyer le projet et partir de zero il faut : 

  

npm run reset-project pour  repartir de zero . 

3 


4  nativewind css 


1. Installez Nativewind


Vous devrez installer nativewindet ses dépendances homologues tailwindcss, react-native-reanimatedet react-native-safe-area-context.
JE VERIFIE SI JAI PRIS LINSTALLATION DE EXPO 

npx expo install nativewind react-native-reanimated react-native-safe-area-context

et la je fais npx tailwindcss init ?
son role ?
Il crée un fichier tailwind.config.js vide, avec juste le squelette. Il ne sait pas encore où sont tes fichiers, ni que tu utilises NativeWind. On doit lui dire.
Pourquoi le content est crucial ?
Tailwind fonctionne en mode purge : il scanne tes fichiers source pour trouver toutes les classes utilisées (ex: bg-blue-500, flex-1...), puis génère uniquement le CSS nécessaire. Si un fichier n'est pas dans content, ses classes seront invisibles → elles n'auront aucun effet.


Pourquoi le preset NativeWind ?
React Native n'est pas un navigateur. Certaines classes Tailwind web n'existent pas sur mobile (display: grid, float, etc.). Le preset NativeWind remplace les valeurs par défaut de Tailwind par des équivalents React Native compatibles.


Étape 3 — Créer babel.config.js

C'est quoi Babel ?
Babel est le compilateur de ton projet. Quand tu écris du JSX, du TypeScript, des imports modernes — Babel traduit tout ça en JavaScript que le moteur JavaScript du téléphone comprend.
Pourquoi NativeWind en a besoin ?
En React Native, les composants n'acceptent pas de prop className. Ils ne connaissent que style={{}}. NativeWind intercepte ça au moment de la compilation via un plugin Babel :

Ton code                          →  Babel + plugin NativeWind  →  Code final
<View className="bg-blue-500">   →       [transformation]       →  <View style={{backgroundColor: '#3b82f6'}}>

Sans ce fichier, Babel ne sait pas que NativeWind existe → className est ignoré silencieusement → aucun style ne s'applique.


Décryptage ligne par ligne
api.cache(true) → Babel mémorise la config compilée. Sans ça, il la recalcule à chaque fichier — ton projet démarrerait 10x plus lentement.
babel-preset-expo → Le preset de base d'Expo : gère TypeScript, JSX, les imports ES modules, etc.
jsxImportSource: "nativewind" → Remplace le système JSX de React par celui de NativeWind. C'est ce qui permet à className d'exister sur <View>, <Text>, etc.
"nativewind/babel" → Le plugin qui analyse chaque fichier à la compilation, détecte tes classes Tailwind, et les pré-traite.



✅ Étape 3 — Validée parfaitement !

babel.config.js est exactement comme il doit être. On enchaîne.
Étape 4 — Créer metro.config.js
C'est quoi Metro ?

Metro est le bundler d'Expo/React Native — l'équivalent de Webpack pour le web. C'est lui qui :

    surveille tes fichiers en temps réel (hot reload)
    assemble tous tes modules JS en un seul bundle
    sert ce bundle à ton téléphone/simulateur

Pourquoi Metro a besoin d'être configuré pour NativeWind ?

Par défaut, Metro ne sait pas lire les fichiers .css. Il ne comprend que JavaScript. NativeWind a besoin que Metro :


global.css                           Metro seul        Metro + NativeWind
@tailwind base;        →   ❌ erreur !      →   ✅ transformé en JS valide
@tailwind components;
@tailwind utilities;

La fonction withNativeWind() enveloppe la config Metro existante et lui ajoute tout ce qu'il faut pour traiter le CSS Tailwind.
Ce qui se passe en arrière-plan

Metro démarre
    ↓
withNativeWind() s'active
    ↓
Metro détecte global.css
    ↓
Tailwind CLI scanne tes fichiers (selon tailwind.config.js)
    ↓
Génère les styles nécessaires
    ↓
NativeWind injecte ces styles dans le runtime React Native
    ↓
Tes className="..." fonctionnent 🎉



Décryptage ligne par ligne
getDefaultConfig(__dirname) → Récupère la config Metro par défaut d'Expo. Elle contient déjà plein de choses (support TypeScript, assets, etc.). On part de cette base plutôt que de tout réécrire.
withNativeWind(config, ...) → Enveloppe cette config et y ajoute le support CSS. C'est le pattern "wrapper" — très courant dans l'écosystème Expo.
{ input: "./global.css" } → Dit à NativeWind quel fichier CSS est le point d'entrée. C'est le fichier qu'on va créer à l'étape suivante.


Partie A — Créer global.css
C'est quoi ce fichier ?
C'est le point d'entrée Tailwind. Ces trois lignes ne sont pas du CSS ordinaire — ce sont des directives Tailwind qui sont interprétées par le compilateur Tailwind CLI.
Ce qui se passe en arrière-plan
@tailwind base;        → styles de base (reset, variables CSS...)
@tailwind components;  → classes de composants personnalisés (si tu en crées)
@tailwind utilities;   → TOUTES tes classes utilitaires (bg-blue-500, flex-1, etc.)
Quand Metro démarre, withNativeWind() prend ce fichier, le donne à Tailwind CLI qui le transforme en styles React Native, puis les injecte dans le runtime de ton app.


Partie B — Importer global.css dans le layout principal
Pourquoi cette étape ?
Créer le fichier CSS ne suffit pas — il faut qu'il soit chargé au démarrage de l'app. Avec Expo Router, le point d'entrée de toute l'application est app/_layout.tsx. C'est le premier fichier exécuté, donc c'est là qu'on importe le CSS.

⚠️ Important : l'import doit être tout en haut, avant tous les autres imports. Ça garantit que les styles sont disponibles dès le premier rendu.
import "./global.css" avec surtout le bon chemin dans app/srcs/_layout .


dans 


Étape 6 — Modifier app.json
Pourquoi cette modification ?
Par défaut, Expo peut utiliser différents bundlers pour le web. On doit explicitement lui dire d'utiliser Metro (et non Webpack) pour que withNativeWind() qu'on a configuré dans metro.config.js soit bien actif sur toutes les plateformes, y compris le web.
La modification
Ton app.json a déjà une section "web" :

ajouter "bundler": "metro" :

"web": {
  "bundler": "metro",
  "output": "static",
  "favicon": "./assets/images/favicon.png"
}


Étape 7 — Configuration TypeScript


Pourquoi cette étape ?
Sans ça, TypeScript ne reconnaît pas className sur les composants React Native et affiche une erreur rouge dans ton éditeur :
Property 'className' does not exist on type 'ViewProps' ❌
NativeWind étend les types React Native via declaration merging — il ajoute className à tous les composants existants. Pour que TypeScript le sache, on crée un fichier de référence.


NE PAS 