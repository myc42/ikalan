#include <vosk_api.h>
#include <stdio.h>

int main() {
    char buf[4000];
    int nread;

    VoskModel *model = vosk_model_new("model");
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
        printf("%s\n", vosk_recognizer_result(recognizer));
    }
}
    printf("%s\n", vosk_recognizer_final_result(recognizer));

    pclose(audio);
    vosk_recognizer_free(recognizer);
    vosk_model_free(model);

    return 0;
}