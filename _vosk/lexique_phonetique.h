#ifndef LEXIQUE_PHONETIQUE_H
#define LEXIQUE_PHONETIQUE_H

#include <stddef.h>

// Structure pour lier une lettre à ses variantes
typedef struct {
    char lettre;
    const char *variantes[10]; // Jusqu'à 10 variantes par lettre
} GroupePhonetique;

// L'alphabet complet avec les variantes Vosk probables
static const GroupePhonetique ALPHABET[] = {
    {'A', {"a", "ah", "ha", "as", NULL}},
    {'B', {"b", "be", "beu", "bb", NULL}},
    {'C', {"k", "ka", "ca", "que", NULL}},
    {'D', {"d", "de", "deu", "dd", NULL}},
    {'E', {"e", "eu", "euh", NULL}},
    {'F', {"f", "effe", "ff", NULL}},
    {'G', {"g", "ge", "gue", NULL}},
    {'H', {"ash", "h", NULL}},
    {'I', {"i", "y", "ii", NULL}},
    {'J', {"j", "je", "jeu", "ji", NULL}},
    {'K', {"k", "ka", "kk", NULL}},
    {'L', {"l", "elle", "ll", NULL}},
    {'M', {"m", "mmm", "aime", "eum", NULL}},
    {'N', {"n", "nnn", "enne", NULL}},
    {'O', {"o", "oh", "eau", "au", NULL}},
    {'P', {"p", "pe", "peu", "pp", NULL}},
    {'Q', {"q", "qu", "que", NULL}},
    {'R', {"r", "erre", "rr", NULL}},
    {'S', {"s", "sss", "esse", "sa", NULL}},
    {'T', {"t", "te", "teu", "tt", NULL}},
    {'U', {"u", "uh", "uu", NULL}},
    {'V', {"v", "vve", "vvv", NULL}},
    {'W', {"double v", "w", NULL}},
    {'X', {"ix", "x", NULL}},
    {'Y', {"i grek", "y", NULL}},
    {'Z', {"z", "zed", "zz", NULL}}
};

static const int TAILLE_ALPHABET = sizeof(ALPHABET) / sizeof(ALPHABET[0]);

#endif