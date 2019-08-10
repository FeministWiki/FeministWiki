<?php

header("Content-Type: text/plain");

if (PHP_SAPI == "cli") {
  parse_str(implode('&', array_slice($argv, 1)), $_POST);
}

$username = $_POST['username'];
$email = $_POST['email'];

function printAndExit() {
  foreach (func_get_args() as $line) {
    printf("%s\n", $line);
  }
  printf("\n");
  printf("Use the 'back' button of your web browser to return to the previous page.\n");
  exit;
}

if ($username == "") {
  printAndExit("Error: You forgot to enter your username.");
}

if ($email == "") {
  printAndExit("Error: You forgot to enter your recovery e-mail address.");
}

$ldapLink = ldap_connect("localhost");

if ($ldapLink == FALSE) {
  printAndExit("Error: Couldn't access member directory; please notify the technician.");
}

ldap_set_option($ldapLink, LDAP_OPT_PROTOCOL_VERSION, 3);

$ldapBind = "cn=readonly,dc=feministwiki,dc=org";
include '.ldap-pass.php';

if (ldap_bind($ldapLink, $ldapBind, $ldapPass) !== TRUE) {
  printAndExit("Error: Couldn't log in to member directory; please notify the technician.");
}

$baseDN = "ou=members,dc=feministwiki,dc=org";
$usernameEsc = ldap_escape($username, "", LDAP_ESCAPE_FILTER);
$emailEsc = ldap_escape($email, "", LDAP_ESCAPE_FILTER);
$filter = "(&(cn=" . $usernameEsc . ")(fwRecoveryMail=" . $emailEsc . "))";
$attrs = array('fwRecoveryMail');

$result = ldap_search($ldapLink, $baseDN, $filter, $attrs);

if ($result === FALSE) {
  printAndExit("Error: Couldn't read member directory; please notify the technician.");
}

if (ldap_count_entries($ldapLink, $result) == 0) {
  printAndExit("Error: Couldn't find this combination of a username and recovery e-mail.");
}

printf("Resetting password...\n");
printf("\n");

$password = shell_exec("slappasswd -gn | tr './' 'xy'");

// All input should be sanity-checked by now, but use escapeshellarg() anyway.
$command = implode(" ", array(
  "/var/www/settings/actions/reset-password",
  escapeshellarg($username),
  escapeshellarg($password)
));

// Use the 'script' utility to get the same output we would get in a tty.
$scriptCommand = implode(" ", array(
  "script -qefc", escapeshellarg($command), "/dev/null"
));

passthru($scriptCommand, $retval);

if ($retval !== 0) {
  printf("\n");
  printAndExit(
    "Error: Resetting password failed.  See reason above.",
    "       (If you see nothing above, yell at the technician.)"
  );
}

printf("Password was reset.  Trying to send out e-mail...\n");
printf("\n");

$retval = mail($email, "New FeministWiki password", "Your new password is: " . $password);

if ($retval !== TRUE) {
  printAndExit("Error: Failed to send out e-mail.  Please contact the technician.");
}

printf("-----\n");
printf("\n");
printf("PASSWORD RESET SUCCESSFUL.  CHECK YOUR RECOVERY E-MAIL INBOX.\n");

?>
