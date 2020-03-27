#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/wait.h>

/*
 * This program is meant to be be owned by root and have its setuid bit set.
 */

int
main (int argc, char *argv[])
{
  if (setuid(0) != 0) {
    printf("Error: Couldn't elevate privileges.\n");
    printf("       Please contact technician@feministwiki.org.\n");
    return 1;
  }

  if (argc != 3) {
    printf("Error: Wrong number of arguments passed to reset-password.\n");
    printf("       Please contact technician@feministwiki.org.\n");
    return 1;
  }

  char *username = argv[1];
  char *password = argv[2];

  execl("/root/bin/fw-moduser",
        "fw-moduser", "-q", username,
        "set", "userPassword", password,
        NULL);

  printf("Error: execl returned.  Please contact technician@feministwiki.org.\n");
  return 1;
}
