#include <iostream>
#include <stdio.h>

#define TCOUNT 10
#define WCOUNT 12

int count = 0;
char buffer[225];

pthread_mutex_t m = PTHREAD_MUTEX_INITIALIZER;
pthread_cond_t  c = PTHREAD_COND_INITIALIZER;

void *output(void *idp)
{
    int passed_in_value = *((int *) idp);

    pthread_mutex_lock(&m);

    while (count <= WCOUNT)
    {
        pthread_cond_wait(&c, &m);
        printf("watch_count(): Thread %d, Count %d, Buffer: %s  \n", passed_in_value, count, buffer);
    }

    pthread_mutex_unlock(&m);
}

void *inc_count(void *idp)
{
    int i;

    int passed_in_value = *((int *) idp);

    for (i = 0; i < TCOUNT; i++)
    {
        count++;
        pthread_mutex_lock(&m);
        printf("inc_count(): Thread %d, old count %d, new count %d\n", passed_in_value, count - 1, count);
        if (count == WCOUNT)
        {
            scanf("%s",buffer);
            printf("Thread %d signaled :: Read) %s \n", passed_in_value, buffer);
            pthread_cond_signal(&c);
        }

        pthread_mutex_unlock(&m);
    }
}

int main()
{
    int i, tids[3] = {0, 1, 2};
    pthread_t threads[3];

    pthread_cond_init(&c, NULL);

    pthread_create(&threads[0], NULL, inc_count, (void *) &tids[0]);
    pthread_create(&threads[1], NULL, inc_count, (void *) &tids[1]);
    pthread_create(&threads[2], NULL, output, (void *) &tids[2]);

    for (i = 0; i < 3; i++) {
        pthread_join(threads[i], NULL);
    }
    pthread_mutex_destroy(&m);
    pthread_cond_destroy(&c);
    return 0;
}
