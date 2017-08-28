#include <stdio.h>
#include <string.h>
#include <pthread.h>

#define TCOUNT 10
#define WCOUNT 12

int count = 0;

pthread_mutex_t m = PTHREAD_MUTEX_INITIALIZER;
pthread_cond_t  c = PTHREAD_COND_INITIALIZER;

void *watch_count(void *idp)
{
    int passed_in_value = *((int *) idp);

    pthread_mutex_lock(&m);

    while (count <= WCOUNT)
    {
        pthread_cond_wait(&c, &m);
        printf("watch_count(): Thread %d, Count %d\n", passed_in_value, count);
    }

    pthread_mutex_unlock(&m);
}

void *inc_count(void *idp)
{
    int i;

    int passed_in_value = *((int *) idp);

    for (i = 0; i < TCOUNT; i++)
    {
        pthread_mutex_lock(&m);
        count++;
        printf("inc_count(): Thread %d, old count %d, new count %d\n", passed_in_value, count - 1, count);

        if (count == WCOUNT)
        {
            printf("Thread %d signaled\n", passed_in_value);
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
    pthread_create(&threads[2], NULL, watch_count, (void *) &tids[2]);


    for (i = 0; i < 3; i++)
    {
        pthread_join(threads[i], NULL);
    }

    pthread_mutex_destroy(&m);

    pthread_cond_destroy(&c);

    return 0;
}
