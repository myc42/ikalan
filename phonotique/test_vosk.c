#include <vosk_api.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include "lexique_phonetique.h"

// Fonction utilitaire pour extraire le texte du JSON Vosk {"text" : "mot"}
void extraire_mot(const char *json, char *destination) {
    sscanf(json, " { \"text\" : \"%[^\"]\"", destination);
}

char identifier_lettre(const char *mot_vosk) {
    for (int i = 0; i < TAILLE_ALPHABET; i++) {
        for (int j = 0; ALPHABET[i].variantes[j] != NULL; j++) {
            if (strcmp(mot_vosk, ALPHABET[i].variantes[j]) == 0) {
                return ALPHABET[i].lettre;
            }
        }
    }
    return '?'; 
}

int main() {
    VoskModel *model = vosk_model_new("model");
    if (!model) {
        printf("Erreur : Dossier 'model' introuvable.\n");
        return 1;
    }

    // On prépare la grammaire JSON à partir du .h
    char grammar[4096] = "[";
    for (int i = 0; i < TAILLE_ALPHABET; i++) {
        for (int j = 0; ALPHABET[i].variantes[j] != NULL; j++) {
            strcat(grammar, "\"");
            strcat(grammar, ALPHABET[i].variantes[j]);
            strcat(grammar, "\",");
        }
    }
    grammar[strlen(grammar) - 1] = ']'; 

    VoskRecognizer *recognizer = vosk_recognizer_new_grm(model, 16000.0, grammar);

    // Capture audio via sox/rec
    FILE *audio = popen("rec -q -t raw -r 16000 -c 1 -b 16 -e signed-integer -", "r");
    if (!audio) return 1;

    char buf[3200];
    int nread;
    char mot_extrait[64];

    printf("=== SYSTÈME PRÊT : PARLEZ ===\n");

    while ((nread = fread(buf, 1, sizeof(buf), audio)) > 0) {
        if (vosk_recognizer_accept_waveform(recognizer, buf, nread)) {
            extraire_mot(vosk_recognizer_result(recognizer), mot_extrait);
            
            if (strlen(mot_extrait) > 0) {
                char L = identifier_lettre(mot_extrait);
                if (L != '?') {
                    printf("\n[OK] Son reconnu : '%s' -> LETTRE : %c\n", mot_extrait, L);
                } else {
                    printf("\n[?] Son inconnu : '%s'\n", mot_extrait);
                }
            }
        }
    }

    pclose(audio);
    vosk_recognizer_free(recognizer);
    vosk_model_free(model);
    return 0;
}