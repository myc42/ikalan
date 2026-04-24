#include <vosk_api.h>
#include <stdio.h>

int main() {
    char buf[4000];
    int nread;




    VoskModel *model = vosk_model_new("model");


   /* 
   
   const char *grammar = "["
        "\"m\", \"mmm\", \"aime\", \"eum\","
        "\"n\", \"nnn\", \"enne\","
        "\"s\", \"sss\", \"esse\","
        "\"v\", \"vvv\", \"vve\","
        "\"k\", \"kkk\", \"ka\", \"ca\""
    "]";
   
   si tu faisais "Ssss", Vosk cherchait dans 100 000 mots français et finissait par dire "est-ce" ou "ce". Ici, tu lui dis : "Ne cherche QUE parmi ces 15 variantes". Il va donc forcément "tomber" sur une de tes lettres.

   En ajoutant cette variable grammar, tu lui mets des œillères. Tu lui dis : "Dans tout le dictionnaire français, seuls ces mots-là existent pour toi aujourd'hui."
   
   // On utilise vosk_recognizer_new_grm au lieu de vosk_recognizer_new
    VoskRecognizer *recognizer = vosk_recognizer_new_grm(model, 16000.0, grammar);
   
   */

    VoskRecognizer *recognizer = vosk_recognizer_new(model, 16000.0);



    // 🔥 lecture micro via sox (stdin)
    FILE *audio = popen("sox -d -t raw -r 16000 -c 1 -b 16 -e signed-integer -", "r");

    if (!audio) {
        printf("Micro not accessible\n");
        return 1;
    }

   while ((nread = fread(buf, 1, sizeof(buf), audio)) > 0) {

    if (nread < 800) continue; // ignore bruit trop faible

    if (vosk_recognizer_accept_waveform(recognizer, buf, nread)) {
        // Affiche le résultat quand il est sûr
        printf("%s\n", vosk_recognizer_result(recognizer));
    }
}
    printf("%s\n", vosk_recognizer_final_result(recognizer));

    pclose(audio);
    vosk_recognizer_free(recognizer);
    vosk_model_free(model);

    return 0;
}