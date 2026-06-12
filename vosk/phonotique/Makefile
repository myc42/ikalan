# On dit au compilateur que tout est dans le dossier actuel (.)
CFLAGS=-I. 
LDFLAGS=-L. -lvosk -lpthread -Wl,-rpath,.

all: test_vosk

test_vosk: test_vosk.o
	gcc $^ -o $@ $(LDFLAGS)

%.o: %.c
	gcc $(CFLAGS) -c -o $@ $<

clean:
	rm -f *.o test_vosk