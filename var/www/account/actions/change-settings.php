<?php

include 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

$newPassword = $_POST['newPassword'];
$newPassword2 = $_POST['newPassword2'];
$displayName = $_POST['displayName'];
$email = $_POST['email'];
$recoveryMail = $_POST['recoveryMail'];

if ($username == "") {
  printAndExit("Error: You forgot to enter your username.");
}

if ($password == "") {
  printAndExit("Error: You forgot to enter your password.");
}

if ($newPassword != "" && strlen($newPassword) < 8) {
  printAndExit("Error: Your password must be at least 8 characters long.");
}

if ($newPassword != $newPassword2) {
  printAndExit("Error: The passwords you entered did not match.");
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

printf("Saving settings...\n");
printf("\n");

$settings = array();
if ($newPassword != "") {
  $settings['userPassword'] = $newPassword;
}
if ($displayName != "") {
  $settings['sn'] = $displayName;
}
if ($email != "") {
  $settings['mail'] = $email;
}
if ($recoveryMail != "") {
  $settings['fwRecoveryMail'] = $recoveryMail;
}

if (ldap_mod_replace($ldapLink, $userDN, $settings)) {
  printf("-----\n");
  printf("\n");
  printf("SETTINGS SUCCESSFULLY SAVED\n");
  printf("\n");
  printf("You may close this page.\n");
} else {
  printAndExit(
    "Error: Saving settings failed.",
    "Reason: " . ldap_error($ldapLink)
  );
}

?>
