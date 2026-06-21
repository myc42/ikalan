Pour faire simple, une interface est une sorte de "contrat" ou de "plan de construction". Elle ne fait rien par elle-même, mais elle impose des règles. Ici,
 elle dicte exactement à quoi doit ressembler l'objet qui va gérer l'authentification (la connexion) dans ton application.
 interface AuthContextType
 signIn: (token: string) => void; fonction qu'on lui fournisse un paramètre token fait son travail d'enregistrement, mais qu'elle ne renvoie aucune réponse ou valeur à la fin.
 signOut: () => void; elle n'a besoin d'aucune information.
 session: string | null; une variable cela veut dire qu'elle contient le "badge" (le token) de l'utilisateur, donc il est connecté. Soit c'est null : la variable est vide, ce qui signifie que l'utilisateur est déconnecté.


const AuthContext  = createContext< AuthContextType | null>(null);  On dit : "Cette boîte contiendra soit notre système de connexion (AuthContextType), soit rien du tout (null)".


useSession
Permettre à n'importe quel composant de ton application de dire "Hé, donne-moi l'état de la session actuelle !".
La sécurité : Si un développeur essaie d'utiliser ce raccourci en dehors de la zone où la "boîte" existe, le code plante volontairement avec une erreur claire 
('useSession doit être utilisé dans un AuthProvider').



Le Gardien (AuthProvider)
export function AuthProvider({ children }: { children: React.ReactNode })
Le mot { children } représente toutes les pages et tous les composants de ton application qui seront placés à l'intérieur de ce gardien.
En gros, tu enveloppes ton application dedans pour que tout le monde ait accès aux informations de connexion.

Le useEffect avec les crochets vides [] à la fin signifie : "Exécute ce code UNE SEULE FOIS, juste au moment exact où l'application s'allume".
À ce moment-là, il lance deux actions importantes : Action A : La vérification du badge session : Est-ce qu'on a un badge d'accès ?(Vide au départ). isLoading : Vrai au départ, car l'application vient de s'allumer et doit vérifier si l'utilisateur était déjà connecté hier.


Vérification : Il cherche dans le téléphone s'il y a un badge sauvegardé (getAccessToken). S'il en trouve un, il le met dans la mémoire (setSession) et dit que le chargement est terminé (setIsLoading(false)).

const subscription = DeviceEventEmitter.addListener('onSessionExpired', signOut);

Ici, on branche une alarme. On dit au téléphone : "Reste à l'écoute. Si jamais le système de réseau (Axios) api.ts crie 'onSessionExpired' (parce que le badge de l'utilisateur est devenu trop vieux ou a été banni du serveur), déclenche immédiatement la fonction signOut pour le déconnecter de force".


. Le nettoyage de fin

return () => {
  subscription.remove();
};

C'est une règle de sécurité en programmation. Si jamais ce composant venait à être détruit (par exemple si l'application redémarre ou se ferme), le return permet de débrancher l'alarme (subscription.remove()) pour éviter que le téléphone ne continue à écouter dans le vide et ne consomme de la mémoire pour rien.


1. La fonction de connexion (signIn)
Ce qu'elle fait : C'est l'action qui s'exécute quand l'utilisateur tape ses identifiants avec succès sur l'écran de Login.
L'écran de Login reçoit un badge secret (token) du serveur et le donne à cette fonction. La fonction prend ce badge et le range immédiatement dans la mémoire de l'application grâce à setSession(token). L'application sait instantanément que l'utilisateur est connecté. dans le cas contraire un null 



2. L'attribut value={{ ... }} : Le contenu du signal
value={{ signIn, signOut, session, isLoading }}
C'est ce que la station de radio diffuse. À l'intérieur de cette double accolade, tu mets l'objet qui contient tes 4 outils indispensables :

La fonction pour se connecter (signIn)

La fonction pour se déconnecter (signOut)

Le badge de l'utilisateur (session)

Le statut du chargement (isLoading) . 
Grâce à la ligne value, n'importe quel composant de ton application (que ce soit un bouton perdu au fond d'une page ou le menu principal) pourra se brancher sur ce signal pour récupérer et utiliser ces 4 outils d'un seul coup.

En React, children est un mot-clé magique qui signifie "tout ce qui se trouve à l'intérieur de mes balises".
Dans ton application, tu vas utiliser ton AuthProvider comme une boîte pour envelopper toute ton application (généralement dans ton fichier App.tsx ou _layout.tsx), de cette façon :
< AuthContext.Provider value={{...}}> {/* 1. J'allume la radio et je diffuse mes outils */}
  {children}                         {/* 2. Tout ce qui est écrit ici peut écouter la radio */}
</ AuthContext.Provider>              {/* 3. Fin de la zone de diffusion */}