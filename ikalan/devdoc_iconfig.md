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