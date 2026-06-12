# On dit au compilateur que tout est dans le dossier actuel (.)
CFLAGS=-I. 
LDFLAGS=-L. -lvosk -lpthread -Wl,-rpath,.

SRC = test_vosk.c cJSON.c
OBJ = test_vosk.o cJSON.o

all: test_vosk

test_vosk: $(OBJ)
	gcc $^ -o $@ $(LDFLAGS)

%.o: %.c
	gcc $(CFLAGS) -c -o $@ $<

clean:
	rm -f *.o test_vosk