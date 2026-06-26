Dossier 1 : user_module_progress (Le Grand Calendrier)
Son rôle global : C'est le carnet de suivi général d'Amadou pour une leçon précise (par exemple, la leçon sur les syllabes avec "B"). Il sert à répondre à deux questions simples : "A-t-il fini cette leçon ?" et "Quand dois-je le tester à nouveau pour être sûr qu'il n'oublie pas ?". Il ne regarde pas les détails, il gère le rythme.

Le rôle de chaque ligne (attribut) dans ce dossier :

learner_id & module_id : Les noms sur l'étiquette du dossier. (Ex: "Élève : Amadou / Leçon : Syllabes en B").

status : L'étiquette de couleur sur le dossier pour savoir où on en est au premier coup d'œil.

New : Leçon jamais ouverte.

Active : Amadou est en train de l'apprendre.

Review : Amadou l'a apprise, il faut l'entretenir.

Mastered : C'est acquis à vie, on range le dossier aux archives.

global_score : La note globale (sur 100) du tout dernier exercice qu'Amadou a fait sur cette leçon.

consecutive_perfect_scores : Le compteur de victoires d'affilée. "Ça fait 3 fois qu'Amadou me fait un sans-faute sur cette leçon. À 4, je la déclare 'Mastered'."

ease_factor : Le coefficient de difficulté naturel d'Amadou pour cette leçon précise. S'il galère, ce chiffre baisse, indiquant au Professeur qu'il faut le tester plus souvent. Si c'est facile pour lui, ce chiffre monte, et le Professeur le laisse tranquille plus longtemps.

interval_days : Le nombre de jours de repos avant le prochain test (ex: on se revoit dans 7 jours).

window_start : Le cadenas de début. "Interdiction formelle de lui faire réviser cette leçon avant le 12 octobre, sinon ça ne sert à rien, c'est trop frais dans sa tête."

window_end : La date d'alerte rouge. "S'il révise après le 15 octobre, il risque d'avoir tout oublié, il faudra être indulgent."

last_seen_at : La date de la dernière fois qu'Amadou a ouvert cette leçon.
---------------------------------


Dossier 2 : user_item_mastery (Le Microscope)
Son rôle global : C'est le carnet de santé intime d'Amadou pour chaque petite brique de connaissance. Ce dossier se fiche complètement des leçons ou des dates de révision. Il sert à répondre à une seule question : "Qu'est-ce qui bloque exactement dans le cerveau d'Amadou ?". C'est grâce à ce dossier que le Professeur sait fabriquer des exercices de révision sur-mesure.

Le rôle de chaque ligne (attribut) dans ce dossier :

learner_id : Le nom de l'élève (Amadou).

item_type & item_id : De quoi parle-t-on exactement ? (Ex: Type = Syllabe, ID = "BA").

mastery_score : La jauge de santé de cette syllabe dans la tête d'Amadou (de 0 à 100%). "Pour le MA, il est à 95%, c'est solide. Pour le BA, il est à 30%, c'est critique."

error_count : Le nombre total de fois où Amadou s'est trompé sur cette syllabe depuis le premier jour. Si ce chiffre est énorme (ex: 15 erreurs), le Professeur sait qu'Amadou fait un blocage psychologique et qu'il faut changer de méthode (lui faire écouter au lieu de lire, par exemple).

avg_response_ms : Le chronomètre d'hésitation. C'est l'indicateur le plus vicieux et le plus utile : "Amadou a trouvé la bonne réponse, mais il a mis 8 secondes à cliquer. Ça veut dire qu'il a douté. Sa maîtrise n'est donc pas parfaite."

last_seen_at : La dernière fois que les yeux d'Amadou ont vu cette syllabe précise à l'écran.

---------------------------