#include <vosk_api.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include "cJSON.h"

int is_word_in_array(cJSON *array, const char *word) {
    if (!array || !word) return 0;
    cJSON *item = NULL;
    cJSON_ArrayForEach(item, array) {
        if (cJSON_IsString(item) && item->valuestring != NULL) {
            if (strcmp(item->valuestring, word) == 0) return 1;
        }
    }
    return 0;
}

void update_alphabet_json(const char *letter, const char *vosk_result) {
    cJSON *vosk_json = cJSON_Parse(vosk_result);
    if (!vosk_json) return;

    cJSON *text_item = cJSON_GetObjectItemCaseSensitive(vosk_json, "text");
    if (!cJSON_IsString(text_item) || (strlen(text_item->valuestring) == 0)) {
        cJSON_Delete(vosk_json);
        return;
    }

    // ✅ OPTIMISATION : extraire uniquement le PREMIER MOT
    char first_word[256] = {0};
    char *token = strtok(text_item->valuestring, " \t\n\r");
    if (!token) {
        cJSON_Delete(vosk_json);
        return;
    }
    strncpy(first_word, token, sizeof(first_word) - 1);

    // Trim fin par sécurité
    char *p = first_word + strlen(first_word) - 1;
    while (p >= first_word && isspace(*p)) *p-- = '\0';

    if (strlen(first_word) == 0) {
        cJSON_Delete(vosk_json);
        return;
    }

    // Chargement du JSON
    FILE *f = fopen("alphabet.json", "rb");
    cJSON *root = NULL;
    if (f) {
        fseek(f, 0, SEEK_END);
        long len = ftell(f);
        fseek(f, 0, SEEK_SET);
        char *data = malloc(len + 1);
        fread(data, 1, len, f);
        data[len] = '\0';
        root = cJSON_Parse(data);
        free(data);
        fclose(f);
    }
    if (!root) root = cJSON_CreateObject();

    cJSON *array = cJSON_GetObjectItemCaseSensitive(root, letter);
    if (!array) {
        array = cJSON_CreateArray();
        cJSON_AddItemToObject(root, letter, array);
    }

    if (!is_word_in_array(array, first_word)) {
        cJSON_AddItemToArray(array, cJSON_CreateString(first_word));
        char *rendered = cJSON_Print(root);
        f = fopen("alphabet.json", "w");
        if (f) {
            fputs(rendered, f);
            fclose(f);
            printf("✅ Nouveau mot pour [%s] : %s\n", letter, first_word);
        }
        free(rendered);
    } else {
        printf("🚫 Doublon ignoré : %s\n", first_word);
    }

    cJSON_Delete(root);
    cJSON_Delete(vosk_json);
}

int main(int argc, char *argv[]) {
    if (argc < 2) {
        fprintf(stderr, "Usage: %s <lettre>\n", argv[0]);
        return 1;
    }

    char *target_letter = argv[1];
    VoskModel *model = vosk_model_new("model");
    if (!model) return 1;

    VoskRecognizer *recognizer = vosk_recognizer_new(model, 16000.0);

    FILE *audio = popen("sox -d -t raw -r 16000 -c 1 -b 16 -e signed-integer -", "r");
    if (!audio) return 1;

    char buf[4000];
    int nread;
    printf("🎙️  Enregistrement pour la lettre '%s'...\n", target_letter);

    while ((nread = fread(buf, 1, sizeof(buf), audio)) > 0) {
        if (vosk_recognizer_accept_waveform(recognizer, buf, nread)) {
            update_alphabet_json(target_letter, vosk_recognizer_result(recognizer));
        }
    }

    pclose(audio);
    vosk_recognizer_free(recognizer);
    vosk_model_free(model);
    return 0;
}