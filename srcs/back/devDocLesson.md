Voici le déroulé complet, du premier appel jusqu'à la dernière écriture en base.

---

## Le cycle complet en deux grandes phases

Le système fonctionne en deux phases bien séparées. La première phase — que tu as déjà testée — consiste à récupérer une leçon. La deuxième phase — que tu viens de valider — consiste à renvoyer les résultats après la session. Ces deux phases forment un cycle qui se répète indéfiniment pour chaque apprenant.

---

## Phase 1 : récupérer une leçon
Voici le déroulé complet de la demande d'exercice, du début à la fin.

---

## Ce qui se passe quand le téléphone demande une leçon

Quand le téléphone envoie une requête GET vers `/api/lesson/next`, la première chose que fait Symfony est de lire le token JWT dans le header Authorization. Ce token contient l'identité de l'utilisateur encodée de façon sécurisée. Symfony le décode, vérifie qu'il n'est pas expiré ni falsifié, puis charge l'objet User correspondant depuis la base de données en utilisant le numéro de téléphone comme identifiant. Si le token est absent ou invalide, la requête est rejetée immédiatement avec une erreur 401 avant même d'entrer dans le contrôleur.

Une fois l'utilisateur identifié, le contrôleur doit décider quel module proposer. Il suit une priorité en trois niveaux. En premier, il vérifie si un header `X-Module-Id` est présent dans la requête — c'est le mode test qui permet de forcer un module précis depuis Postman, il sera retiré en production. En deuxième, si aucun module n'est forcé, il interroge `user_module_progress` pour savoir si un module a une fenêtre de révision SRS ouverte aujourd'hui — ce serait une révision urgente que l'apprenant risque d'oublier, elle passe en priorité absolue. En troisième, si aucune révision n'est urgente, il consulte la table `Progression` pour trouver le module en cours, c'est-à-dire une ligne dont `completedAt` est null. Si l'apprenant n'a aucune progression en cours, il prend le premier module jamais commencé en respectant l'ordre pédagogique des chapitres et des modules. Si absolument rien n'est disponible, il répond que l'apprenant a tout terminé et doit revenir plus tard.

Une fois le module déterminé, le contrôleur passe la main au LessonComposer avec l'utilisateur et le module. Le Composer commence par vérifier si ce module contient des sujets, des verbes ou des compléments dans les tables de jonction correspondantes. Cette vérification détermine quelle branche de construction prendre.

Si le module ne contient pas de phrases, on entre dans la branche graphèmes et mots. Le Composer récupère d'abord tous les graphèmes associés à ce module via la table `graphemes`, sans limite de quantité. Chaque graphème devient un exercice de type découverte. Ensuite il regarde si le module a un `word_level` renseigné. Si oui, il lance la sélection des mots. Si non, la leçon ne contient que des graphèmes.

La sélection des mots se fait en trois étapes successives. La première étape constitue la liste des graphèmes connus de l'apprenant en fusionnant deux sources — les graphèmes déjà maîtrisés dans les sessions précédentes, trouvés dans `user_item_mastery` avec un score supérieur à 0.5, et les graphèmes du module en cours, car l'apprenant vient justement de les travailler dans cette leçon et ils doivent donc être considérés comme disponibles. Cette fusion évite le problème du premier lancement où `user_item_mastery` est vide. La deuxième étape récupère jusqu'à vingt mots candidats correspondant au `word_level` du module depuis la table `words`. La troisième étape filtre ces candidats en vérifiant pour chacun que tous les éléments de sa segmentation — par exemple `{s,a}` pour le mot "sa" — sont présents dans la liste des graphèmes connus. Si un seul segment est inconnu, le mot est écarté. On s'arrête dès que cinq mots valides sont trouvés.

Si le module contient des phrases, on entre dans l'autre branche. Le Composer récupère les sujets, les verbes et les compléments associés au module via les tables de jonction, puis les croise pour générer des combinaisons de phrases jusqu'à un maximum de cinq.

Dans les deux cas, la liste d'exercices brute passe dans l'ExerciseWeaver qui applique trois règles pédagogiques. La règle de proportion garantit qu'au moins soixante-dix pour cent des exercices sont en mode découverte. La règle de respiration espace les exercices de révision pour qu'ils ne se suivent jamais directement. La règle de canal sensoriel s'assure qu'une révision audio ne tombe pas entre deux exercices moteurs.

La liste tissée passe ensuite dans le PayloadSerializer qui transforme chaque exercice en structure JSON lisible par le téléphone. Chaque exercice reçoit un identifiant unique, sa position dans la séquence, son type, son mode discovery ou review, et son `source_module_id` — ce dernier champ est crucial car le téléphone le recopiera dans ses événements lors de la synchronisation, permettant au serveur de savoir à quel module appartient chaque réponse même dans une leçon hybride. Le Serializer construit aussi le `media_manifest`, la liste dédupliquée des fichiers audio à pré-télécharger.

Le contrôleur reçoit ce payload et le retourne en JSON avec un code 200. Le téléphone a tout ce qu'il lui faut pour faire travailler l'apprenant hors ligne.
---

## Phase 2 : renvoyer les résultats

Quand la session est terminée et que le téléphone retrouve une connexion, il envoie une requête POST vers `/api/sync`. Cette requête transporte dans son body un objet JSON contenant l'identifiant de la leçon, l'identifiant du module, la date de fin de session, et la liste complète des événements — un événement par exercice, chacun portant l'identifiant de l'exercice, le type et l'identifiant de l'item, le mode discovery ou review, l'identifiant du module source, le temps de réponse en millisecondes, le nombre de tentatives et le résultat succès ou échec.

Le token JWT est là aussi dans le header Authorization — Symfony identifie l'utilisateur de la même façon qu'à la phase 1.

Le `SyncController` reçoit la requête et commence par décoder le JSON. Si le JSON est malformé, il répond 400 immédiatement. Sinon il passe le tableau de données brutes au `SyncRequestValidator`.

Le Validator est le premier rempart contre les données corrompues. Il vérifie que chaque champ obligatoire est présent et du bon type, que la date de fin n'est pas dans le futur, que le nombre d'événements n'est pas nul ni excessif, que chaque événement a un `item_type` parmi les valeurs autorisées, que chaque `mode` est bien `discovery` ou `review`, que le temps de réponse ne dépasse pas cinq minutes, et que le nombre de tentatives reste raisonnable. Si une seule validation échoue, il lève une exception avec un message précis indiquant exactement quel champ pose problème. Le contrôleur attrape cette exception et répond 422 avec le détail de l'erreur.

Si la validation réussit, le Validator construit deux objets typés — un `SyncPayloadDTO` qui représente l'ensemble de la session, et autant de `SyncEventDTO` qu'il y a d'événements. Ces objets sont immuables : une fois créés, leurs valeurs ne peuvent plus changer. Ils voyagent de service en service comme des enveloppes fermées et scellées.

Le contrôleur passe le DTO à l'`SyncIngestionService` qui orchestre toute la suite.

La première chose que fait l'Ingestion Service est de persister le `SessionLog` en base — il écrit le carnet de bord brut avec tous les événements sérialisés dans le champ `raw_events`, la date de réception, et `processedAt` à null. Ce flush immédiat garantit que même si une analyse plante ensuite, les données brutes sont déjà sauvegardées et pourront être retraitées manuellement.

Ensuite le service sépare les événements en deux groupes grâce aux méthodes du DTO. Les événements `discovery` concernent le module courant — celui que l'apprenant a découvert pendant cette session. Les événements `review` concernent des modules antérieurs qui ont été révisés dans la leçon hybride.

Le `GlobalScoreAnalyzer` reçoit uniquement les événements discovery et calcule la note globale du module courant. Il mesure trois choses : le taux de réussite au premier essai qui pèse pour cinquante pour cent du score final, la rapidité moyenne des réponses qui pèse pour trente pour cent, et la fluidité mesurée par l'absence de passages en file de rattrapage qui pèse pour vingt pour cent. Le résultat est un nombre entre zéro et un.

L'`ItemMasteryAnalyzer` reçoit tous les événements sans distinction et met à jour la maîtrise individuelle de chaque item rencontré. Pour chaque paire item-type et item-id, il cherche la ligne existante dans `user_item_mastery` ou en crée une nouvelle. Il calcule un score pour cette session selon que l'exercice a été réussi du premier coup, réussi après rattrapage, ou échoué. Il fusionne ce score de session avec l'historique via une moyenne glissante pondérée — l'ancien score compte pour soixante-dix pour cent et le score de session pour trente pour cent, ce qui préserve la stabilité : un bon résultat ponctuel ne masque pas une faiblesse ancienne. Il met également à jour le temps de réponse moyen et le compteur d'erreurs de la même façon.

Le `SrsScheduler` reçoit ensuite la note globale calculée pour le module courant. Il cherche la ligne correspondante dans `user_module_progress` ou en crée une. Il ajuste le facteur de facilité selon la formule SM-2 — un bon score fait monter le facteur, un mauvais score le fait descendre, avec des bornes à 1.3 minimum et 3.0 maximum. Il calcule le nouvel intervalle en jours : un pour un score trop faible, trois jours pour la première révision réussie, sept jours pour la deuxième, puis les suivants en multipliant l'intervalle précédent par le facteur de facilité. Il calcule enfin la fenêtre de révision en ajoutant une tolérance d'un jour avant la date cible et trois jours après, pour absorber les jours sans connexion. Il détermine le statut du module — `DONE` après trois scores parfaits consécutifs, `IN_PROGRESS` pour un bon score, `REVIEW` pour un score fragile. Tout cela est écrit dans `user_module_progress` mais sans flush immédiat.

La même opération est répétée pour chaque module distinct présent dans les événements review — chaque module révisé reçoit son propre recalcul de score et de fenêtre SRS.

Une fois toutes les analyses terminées, l'Ingestion Service marque le `SessionLog` comme traité en écrivant la date dans `processedAt`, puis déclenche un seul flush global qui écrit en base toutes les modifications accumulées en une seule transaction.

Le service retourne un résumé au contrôleur — le nombre d'événements traités, le score global calculé, la nouvelle fenêtre de révision et le facteur de facilité. Le contrôleur encapsule ce résumé dans une réponse 200 et la renvoie au téléphone.

---

## Ce qui relie tout ça

Les données voyagent sous trois formes différentes selon les étapes. Entre le téléphone et le serveur, elles voyagent en JSON dans le corps des requêtes HTTP. Entre le contrôleur et les services, elles voyagent sous forme d'objets PHP typés — les DTOs — qui garantissent qu'aucun champ ne manque et qu'aucun type n'est incorrect. Entre les services et la base de données, elles voyagent via Doctrine qui traduit les objets PHP en requêtes SQL et les résultats SQL en objets PHP.

Le `EntityManager` de Doctrine est le registre central de cette dernière couche. Quand un service appelle `persist`, il dit à Doctrine de surveiller cet objet. Quand il appelle `flush`, Doctrine génère et exécute toutes les requêtes SQL nécessaires pour synchroniser l'état des objets surveillés avec la base de données. C'est pourquoi les services peuvent travailler sur des objets PHP pendant toute la durée de l'analyse sans toucher la base, et tout écrire en une seule fois à la fin.