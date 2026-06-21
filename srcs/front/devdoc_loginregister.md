deja , faut creer tout les ceux qui ressemble dans le meme dossier par exemple inscription et connexion 
jai met dans dossier auth  les deux .

mtn , il faut creer comment les tokens doivent functionner , donc dans le dossier utils , je cree des templates de recupererations , suppression de templates ou meme ajout. 

ensuite je vais dans le dossiers services je creer le service correspondant , je cree le premier service api qui sera dans api.ts qui importera axios ,  npm install axios dont il faut installer deja . et aussi importanter les templates .

ok dans api.tsx

import axios

Axios est un facteur.

Il permet d'envoyer des messages HTTP  axios.get(...)
axios.post(...) "Bonjour serveur, donne-moi la liste des utilisateurs." AxiosError. C'est le type des erreurs Axios. Exemple :

serveur éteint
erreur 404
erreur 500
erreur 401

Que retourne axios.create() ? Axios crée une instance Axios. Parce qu'une instance Axios est en réalité une fonction objet.
Elle peut être appelée de deux façons : Méthode 1 api.get('/users'); Méthode 2 api({
  method: 'GET',
  url: '/users'
});
Quand la requête échoue : Axios stocke toute la configuration : {
  method: 'GET',
  url: '/profile',
  headers: {
    Authorization: 'Bearer ancienToken'
  }
} Cette configuration est récupérée ici : const originalRequest = error.config;


InternalAxiosRequestConfig C'est le type qui décrit une requête Axios. Une requête contient :
{
  url: "...",
  headers: {...},
  method: "GET"
}

Deuxième import

import { getAccessToken, getRefreshToken, saveTokens, clearTokens } : Ce sont tes fonctions personnelles.

Étape 2 : Adresse du serveur const API_URL = 'http://TON_IP_LOCALE:8000/api'; Ici on dit : "Mon serveur habite à cette adresse."

Étape 3 : Création d'une interface personnalisée

interface CustomAxiosRequestConfig extends InternalAxiosRequestConfig {
  _retry?: boolean;
}

Pourquoi ?

Axios ne connaît pas la propriété : Donc on crée notre propre version. _retry "Je prends tout ce qu'il y a dans InternalAxiosRequestConfig et j'ajoute quelque chose." : 

interface CustomAxiosRequestConfig extends InternalAxiosRequestConfig {
  _retry?: boolean;
}


Étape 4 : Définir la forme de la réponse du refresh

interface RefreshTokenResponse {
  token: string;
  refresh_token: string;
}

On explique à TypeScript :

"Quand je fais un refresh, le serveur me renvoie ça."

{
  "token": "abc123",
  "refresh_token": "xyz789"
}

Étape 5 : Création de l'instance Axios : export const api = axios.create
On crée notre propre Axios personnalisé. baseURL: API_URL, Maintenant : api.get('/users') devient automatiquement : http://TON_IP_LOCALE:8000/api/users

Headers par défaut headers: {
  'Content-Type': 'application/json',
},  Chaque message envoyé indique : "Je parle en JSON."


Étape 6 : Intercepteur de requête

api.interceptors.request.use  Un intercepteur est un garde. Il intercepte chaque requête AVANT son départ.
Application
     ↓
Intercepteur
     ↓
Serveur



Étape 7 : Récupérer le token

const token = await getAccessToken(); On va chercher le badge.

Étape 8 : Ajouter le token
if (token && config.headers) token existe ? headers existe ? 
config.headers.Authorization = `Bearer ${token}`; Devient : Authorization: Bearer eyJhbGc...
C'est comme montrer sa carte d'identité au gardien.

Étape 9 : Retourner la requête
return config; On dit :  "La requête est prête, tu peux partir." sinon Si la requête ne peut même pas partir : (error: AxiosError) => Promise.reject(error)

--------- 
Étape 11 : Intercepteur de réponse

api.interceptors.response.use
Cette fois-ci :
Serveur
   ↓
Intercepteur
   ↓
Application

Si tout va bien Réponse reçue : (response) => response 200 OK On la renvoie simplement.
Si erreur async (error: AxiosError) =>   On entre ici lorsqu'il y a :401 404 500 ...

Récupérer la requête d'origine : const originalRequest = error.config as CustomAxiosRequestConfig; Exemple : api.get('/profile') Cette requête est stockée dans : originalRequest

Vérifier le 401 if (
  error.response?.status === 401 &&
  originalRequest &&
  !originalRequest._retry
) 
401 signifie : "Ton badge n'est plus valide."
!originalRequest._retry = pour éviter la boucle infinie.  Marquer la tentative originalRequest._retry = true; "J'ai déjà essayé de réparer le problème."

-------------
Étape 17 : Récupérer Vérification de refresh token

const refreshToken = await getRefreshToken(); if (!refreshToken)
-----------

Étape 18 : Demander un nouveau token

const response = await axios.post< RefreshTokenResponse> On envoie : POST /token/refresh

Corps : {
  refresh_token: refreshToken
}
C'est comme dire :

"Mon badge est expiré mais voici mon badge de renouvellement."

Le serveur répond :
{
  "token": "NEW_ACCESS",
  "refresh_token": "NEW_REFRESH"
}

Récupération des valeurs dans une variable et Sauvegarder  On remplace les anciens badges.
await saveTokens(
  newAccessToken,
  newRefreshToken
);

On colle le nouveau badge sur la requête qui avait échoué.

originalRequest.headers.Authorization =
  `Bearer ${newAccessToken}`;
  Avant :  Bearer ANCIEN_TOKEN Après : Bearer NOUVEAU_TOKEN

  ----

  Étape 24 : Rejouer la requête

  return api(originalRequest); On reprend exactement la même requête : mais avec le nouveau token.