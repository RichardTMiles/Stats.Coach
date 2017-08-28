//
// Created by Richard Miles on 8/23/17.
//

#define _OPEN_SYS
#include <stdio.h>
#include <cstdlib>
#include <errno.h>
#include <fcntl.h>
#include <string.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>


int main() {

    int     flags, ret_value, c_status;
    pid_t   pid;
    size_t  n_elements;
    char    char_ptr[32];
    char    str[] = "This is a testing signal";
    char    fifoname[] = "test.fifo";
    FILE    *rd_stream,*wr_stream;

    remove(fifoname);
    if ((mkfifo(fifoname,S_IRWXU)) != 0) {
        printf("Unable to create a fifo; errno=%d\n",errno);
        exit(1);                     /* Print error message and return */
    }

    /* issue fopen for read                                   */
    rd_stream = fopen(fifoname,"r");
    if (rd_stream == (FILE *) NULL)  {
        printf("In parent process\n");
        printf("fopen returned a NULL, expected valid pointer\n");
        exit(2);
    }
    /* get current flag settings of file                      */
    if ((flags = fcntl(fileno(rd_stream),F_GETFL)) == -1) {
        printf("fcntl returned -1 for %s\n",fifoname);
        exit(3);
    }

    /* clear O_NONBLOCK  and reset file flags                 */
    flags &= (O_NONBLOCK);
    if ((fcntl(fileno(rd_stream),F_SETFL,flags)) == -1) {
        printf("\nfcntl returned -1 for %s",fifoname);
        exit(4);
    }

    ret_value = fread(char_ptr,sizeof(char),strlen(str),rd_stream);

    char buffer[300];
    bzero(buffer, sizeof(buffer));
    scanf("%s",buffer);
    printf("%s\n",buffer);



    ret_value = fclose(rd_stream);
    if (ret_value != 0)  {
        printf("\nFclose failed for %s",fifoname);
        printf("\nerrno is %d",errno);
        exit(8);
    }
    ret_value = remove(fifoname);
    if (ret_value != 0)  {
        printf("\nremove failed for %s",fifoname);
        printf("\nerrno is %d",errno);
        exit(9);
    }

};