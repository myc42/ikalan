#include <vosk_api.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

int main() {
    // 1. Définition des variantes (ton dictionnaire restreint)
    // On met tous les sons que Vosk pourrait interpréter pour M, N, S, V, K
    const char *grammar = "["
        "\"m\", \"aime\", \"eum\", \"mm\", \"mmm\"," // Variantes M
        "\"n\", \"enne\", \"ne\", \"nn\","           // Variantes N
        "\"s\", \"esse\", \"ss\", \"sss\", \"sa\","  // Variantes S
        "\"v\", \"vve\", \"ve\", \"vv\","           // Variantes V
        "\"k\", \"ka\", \"ca\", \"que\", \"kk\""     // Variantes K
    "]";

    // 2. Chargement du modèle
    VoskModel *model = vosk_model_new("vosk-model-small-fr");
    if (model == NULL) {
        fprintf(stderr, "Erreur: Modèle introuvable dans le dossier 'vosk-model-small-fr'\n");
        return -1;
    }

    // 3. Création du Recognizer avec la GRAMMAIRE
    // Cela force Vosk à ne chercher QUE dans les sons listés au-dessus
    VoskRecognizer *recognizer = vosk_recognizer_new_grm(model, 16000.0, grammar);

    printf("--- TEST DE PHONÈMES (M, N, S, V, K) ---\n");
    printf("Parlez dans le micro (Simulé via stdin ou fichier)...\n\n");

    // 4. Boucle de lecture (Simulation d'entrée audio 16kHz Mono)
    // Pour un test réel, tu peux piper ton micro vers ce programme
    char buf[3200];
    int nread;
    while ((nread = fread(buf, 1, sizeof(buf), stdin)) > 0) {
        int final = vosk_recognizer_accept_waveform(recognizer, buf, nread);
        if (final) {
            printf("RESULTAT FINAL : %s\n", vosk_recognizer_result(recognizer));
        } else {
            // Affiche ce qu'il comprend "pendant" que tu parles
            printf("En cours... : %s\r", vosk_recognizer_partial_result(recognizer));
            fflush(stdout);
        }
    }

    vosk_recognizer_free(recognizer);
    vosk_model_free(model);
    return 0;
}