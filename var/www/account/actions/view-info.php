<?php

require 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

if ($username == '') {
    printAndExit('Error: You forgot to enter your username.');
}

if ($password == '') {
    printAndExit('Error: You forgot to enter your password.');
}

$ldapLink = ldapBind($username, $password);

$userDN = "cn=$username,ou=members,dc=feministwiki,dc=org";

$res = ldap_read($ldapLink, $userDN, '(objectClass=*)');

if ($res === FALSE) {
    technicalError('Read operation failed. CODE: ' . ldap_error($ldapLink));
}

$entry = ldap_first_entry($ldapLink, $res);

if ($entry === FALSE) {
    technicalError('Couldn\'t get result entry. CODE: ' . ldap_error($ldapLink));
}

$attrs = ldap_get_attributes($ldapLink, $entry);

if ($entry === FALSE) {
    technicalError('Couldn\'t get entry attributes. CODE: ' . ldap_error($ldapLink));
}

println('Your database entry:');
println('');

for ($i = 0; $i < $attrs['count']; ++$i) {
    $key = $attrs[$i];
    $values = $attrs[$key];
    for ($j = 0; $j < $values['count']; ++$j) {
        printf("%s: %s\n", $key, $values[$j]);
    }
}

?>
