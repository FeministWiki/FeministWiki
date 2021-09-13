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

  char *args[20] = { NULL };
  args[0] = "fw-adduser";
  args[1] = "-q";

  size_t argsIndex = 2;

  char c;
  while ((c = getopt(argc, argv, "e:m:r:")) != -1) {
    switch (c) {
    case 'e':
      args[argsIndex] = "-e";
      args[argsIndex + 1] = optarg;
      argsIndex += 2;
      break;
    case 'm':
      args[argsIndex] = "-m";
      args[argsIndex + 1] = optarg;
      argsIndex += 2;
      break;
    case 'r':
      args[argsIndex] = "-r";
      args[argsIndex + 1] = optarg;
      argsIndex += 2;
      break;
    default:
      printf("Error: Wrong usage of add-member program.\n");
      printf("       Please contact technician@feministwiki.org.\n");
      return 1;
    }
  }

  if (optind + 2 != argc) {
      printf("Error: Too many arguments passed to add-member.\n");
      printf("       Please contact technician@feministwiki.org.\n");
      return 1;
  }

  args[argsIndex] = argv[optind];
  args[argsIndex + 1] = argv[optind + 1];

  execv("/root/bin/fw-adduser", args);

  printf("Error: exec() returned.  Please contact technician@feministwiki.org.\n");
  return 1;
}
