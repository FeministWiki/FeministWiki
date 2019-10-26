<?php

include 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

$newMemberUsername = $_POST['newMemberUsername'];
$newMemberPassword = shell_exec("slappasswd -gn | tr './' 'xy'");

if ($username == "") {
  printAndExit("Error: You forgot to enter your username.");
}

if ($password == "") {
  printAndExit("Error: You forgot to enter your password.");
}

if (preg_match("/^[a-z]+[a-z0-9]*$/i", $newMemberUsername) !== 1) {
  printAndExit(
    "Error: Invalid username.  Please only use letters and digits,",
    "       and make sure the name doesn't start with a digit."
  );
}

if ($newMemberPassword == NULL) {
  printAndExit("Error: Couldn't generate password; please notify the technician.");
}

$ldapLink = ldap_connect("localhost");

if ($ldapLink == FALSE) {
  printAndExit("Error: Couldn't access member directory; please notify the technician.");
}

ldap_set_option($ldapLink, LDAP_OPT_PROTOCOL_VERSION, 3);

$userDN = "cn=" . $username . ",ou=members,dc=feministwiki,dc=org";

if (ldap_bind($ldapLink, $userDN, $password) !== TRUE) {
  printAndExit(
    "Error: Login failed.  Please check your username and password.",
    "",
    "If you're sure you entered your username and password correctly,",
    "contact the technician and give them this error code:",
    ldap_error($ldapLink)
  );
}

// All input should be sanity-checked by now, but use escapeshellarg() anyway.
$adduserCommand = implode(" ", array(
  "/var/www/settings/actions/add-member",
  escapeshellarg($username),
  escapeshellarg($newMemberUsername),
  escapeshellarg($newMemberPassword),
));

// Use the 'script()' utility to get the same output we would get in a tty.
$scriptCommand = implode(" ", array(
  "script -qefc", escapeshellarg($adduserCommand), "/dev/null"
));

printf("Trying to add member...\n");
printf("\n");

passthru($scriptCommand, $retval);

if ($retval !== 0) {
  printf("\n");
  printAndExit(
    "Error: Adding user failed.  See reason above.",
    "       (If you see nothing above, yell at the technician.)"
  );
} else {
  printf("-----\n");
  printf("\n");
  printf("MEMBER SUCCESSFULLY ADDED\n");
  printf("\n");
  printf("New member username: %s\n", $newMemberUsername);
  printf("New member password: %s\n", $newMemberPassword);
  printf("\n");
  printf("Don't forget to write the password down or save it on your computer.\n");
  printf("You can then safely close this page.\n");
}

?>
