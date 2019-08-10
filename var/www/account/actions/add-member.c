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
    printf("       Please contact the technician.\n");
    return 1;
  }

  if (argc != 4) {
    printf("Error: Wrong number of arguments passed to add-member.\n");
    printf("       Please contact the technician.\n");
    return 1;
  }

  char *addingUsername = argv[1];
  char *newMemberUsername = argv[2];
  char *newMemberPassword = argv[3];

  execl("/root/bin/fw-adduser",
        "fw-adduser",
        "-q", "-m", addingUsername,
        newMemberUsername,
        newMemberPassword,
        NULL);

  printf("Error: execl returned.  What the hell?  Yell at the technician.\n");
  return 1;
}
