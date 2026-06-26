Ce qui se passe quand tu appelles GET /api/lesson/next

Étape 1 — Identification de l'utilisateur
Symfony lit le token JWT dans le header Authorization. Il décode ce token, en extrait le telephone, et charge l'objet User correspondant depuis la base de données. C'est le #[CurrentUser] qui fait ce travail automatiquement. Si le token est absent ou expiré, Symfony répond 401 avant même d'entrer dans le contrôleur.

Étape 2 — Sélection du module
Le contrôleur vérifie d'abord si tu as envoyé un header X-Module-Id. Si oui, il charge ce module directement — c'est le mode test Postman. Si non, il suit la priorité pédagogique : il cherche d'abord un module en fenêtre de révision SRS ouverte aujourd'hui dans user_module_progress. Si aucune révision n'est urgente, il prend le premier module que l'apprenant n'a jamais commencé, en respectant l'ordre chapter_order puis module_order. Si tout est fait et rien n'est à réviser, il répond 200 avec un message "revenez plus tard".

Étape 3 — Le LessonComposer reçoit l'utilisateur et le module
C'est ici que la construction de la leçon commence. La première question que le Composer se pose est : est-ce que ce module contient des sujets, verbes ou compléments dans module_subjects ? La réponse détermine quelle branche prendre.

Étape 4a — Branche graphème + mots (si pas de phrases)
Le Composer récupère le graphème associé au module dans la table graphemes. Ensuite il détermine le niveau de l'apprenant en remontant sa Progression active — celle dont completed_at est NULL. Cette progression pointe vers un module, qui pointe vers un chapitre, dont le chapter_order devient le niveau. Avec ce niveau, il récupère jusqu'à 5 mots dans words. Si moins de 5 mots existent pour ce niveau, il prend tous les mots disponibles tous niveaux confondus.

Étape 4b — Branche phrases (si subjects/verbs/complements présents)
Le Composer récupère les sujets, verbes et compléments liés au module via les tables de jonction module_subjects, module_verbs, module_complements. Il les croise pour assembler des phrases dynamiques, jusqu'à 5 combinaisons maximum.


Étape 5 — L'ExerciseWeaver applique les règles pédagogiques
Le Weaver reçoit la liste brute d'exercices et applique trois règles dans l'ordre. La règle de proportion garantit qu'au moins 70 % des exercices sont en mode discovery — les révisions ne peuvent pas dépasser 30 %. La règle de respiration espace les exercices de révision en les intercalant régulièrement entre les exercices de découverte, jamais deux révisions consécutives. La règle de canal sensoriel vérifie qu'une révision audio ne tombe pas entre deux exercices moteurs — si c'est le cas, elle déplace l'exercice.


Étape 6 — Le PayloadSerializer construit le JSON final
Il transforme chaque exercice interne en structure JSON lisible par le téléphone. Pour un graphème, il produit un exercice listen_and_choose avec le display, l'audio_key, les choix de réponse et le correct_item_id. Pour un mot, même structure avec en plus la segmentation et les flags is_syllable / is_sight_word. Pour une phrase, il assemble les trois composants avec leurs audios séparés. Dans tous les cas, il inscrit mode et source_module_id sur chaque exercice — ce sont les deux champs que le téléphone recopiera dans ses raw_events lors de la synchronisation, ce qui permettra au serveur de savoir à quel module appartient chaque réponse. Il construit également le media_manifest : la liste des fichiers audio uniques à pré-télécharger, sans doublons.

Étape 7 — Le contrôleur retourne le JSON
La réponse 200 contient le lesson_id, le module_id, la liste des exercices ordonnés, les règles de rattrapage embarquées (max_retries_before_force_pass: 3), et le media_manifest. Le téléphone peut commencer à travailler hors ligne dès réception.

La prochaine étape logique serait l'endpoint POST /api/sync — le moment où le téléphone renvoie son carnet de bord après la session, et où le serveur analyse les résultats pour mettre à jour user_module_progress et user_item_mastery.