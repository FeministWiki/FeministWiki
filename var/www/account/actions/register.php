<?php

header("Content-Type: text/plain");

if (PHP_SAPI == "cli") {
  parse_str(implode('&', array_slice($argv, 1)), $_POST);
}

$username     = $_POST['username'];
$email        = $_POST['email'];
$recoveryMail = $_POST['recoveryMail'];
$declaration  = $_POST['declaration'];

function printAndExit() {
  foreach (func_get_args() as $line) {
    printf("%s\n", $line);
  }
  printf("\n");
  printf("Use the 'back' button of your web browser to return to the previous page.\n");
  exit;
}

if ($username == "") {
  printAndExit("Error: You forgot to enter a username.");
}

if ( ($email == "") == ($recoveryMail == "") ) {
  printAndExit("Error: Please fill in exactly one of the two e-mail fields.");
}

printf("Trying to send out e-mail...\n");
printf("\n");

$retval = mail(
  "admin+accountrequest@feministwiki.org",
  "Account request: " . $username,
  "Username: " . $username . "\n"
  . "Email: " . $email . "\n"
  . "Recovery email: " . $recoveryMail . "\n"
  . "Declaration:\n" . $declaration
);

if ($retval !== TRUE) {
  printAndExit("Error: Failed to send out e-mail.  Please contact the technician.");
}

printf("Success.\n");
printf("\n");
printf("Your request was mailed to the administrator.\n");
printf("If you don't receive a response within a few days, feel free to directly contact admin@feministwiki.org.\n");

?>
