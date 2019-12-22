<?php

include 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

if ($username == "") {
  printAndExit("Error: You forgot to enter your username.");
}

if ($password == "") {
  printAndExit("Error: You forgot to enter your password.");
}

$ldapLink = ldap_connect("localhost");

if ($ldapLink == FALSE) {
  printAndExit("Error: Couldn't access member directory; please contact admin@feministwiki.org.");
}

ldap_set_option($ldapLink, LDAP_OPT_PROTOCOL_VERSION, 3);

$userDN = "cn=" . $username . ",ou=members,dc=feministwiki,dc=org";

if (ldap_bind($ldapLink, $userDN, $password) !== TRUE) {
  printAndExit(
    "Error: Login failed.  Please check your username and password.",
    "",
    "If you're sure you entered your username and password correctly,",
    "contact admin@feministwiki.org and give them this error code:",
    ldap_error($ldapLink)
  );
}

$res = ldap_read($ldapLink, $userDN, "(objectClass=*)");

if ($res === FALSE) {
  printAndExit(
    "Error: Read operation failed.",
    "",
    "Please contact admin@feministwiki.org and give them this error code:",
    ldap_error($ldapLink)
  );
}

$entry = ldap_first_entry($ldapLink, $res);

if ($entry === FALSE) {
  printAndExit(
    "Error: Couldn't get result entry.",
    "",
    "Please contact admin@feministwiki.org and give them this error code:",
    ldap_error($ldapLink)
  );
}

$attrs = ldap_get_attributes($ldapLink, $entry);

if ($entry === FALSE) {
  printAndExit(
    "Error: Couldn't get entry attributes.",
    "",
    "Please contact admin@feministwiki.org and give them this error code:",
    ldap_error($ldapLink)
  );
}

for ($i = 0; $i < $attrs['count']; ++$i) {
  $key = $attrs[$i];
  $values = $attrs[$key];
  for ($j = 0; $j < $values['count']; ++$j) {
    printf("%s: %s\n", $key, $values[$j]);
  }
}

?>
