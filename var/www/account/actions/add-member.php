<?php

include 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

$newMemberUsername = $_POST['newMemberUsername'];
$newMemberPassword = generatePassword();

if ($username == '') {
    printAndExit('Error: You forgot to enter your username.');
}

if ($password == '') {
    printAndExit('Error: You forgot to enter your password.');
}

if (preg_match('/^[a-z]+[a-z0-9]*$/i', $newMemberUsername) !== 1) {
    printAndExit(
        'Error: Invalid username.  Please only use letters and digits,',
        '       and make sure the name doesn\'t start with a digit.'
    );
}

if ($newMemberPassword == NULL) {
    printAndExit(
        'Error: Couldn\'t generate password.',
        '       Please contact admin@feministwiki.org.'
    );
}

$ldapLink = ldap_connect('localhost');

if ($ldapLink == FALSE) {
    printAndExit(
        'Error: Couldn\'t access member directory.',
        '       Please contact admin@feministwiki.org.'
    );
}

ldap_set_option($ldapLink, LDAP_OPT_PROTOCOL_VERSION, 3);

$userDN = "cn=$username,ou=members,dc=feministwiki,dc=org";

if (ldap_bind($ldapLink, $userDN, $password) !== TRUE) {
    printAndExit(
        'Error: Login failed.  Please check your username and password.',
        '',
        'If you\'re sure you entered your username and password correctly,',
        'contact admin@feministwiki.org and provide them this error code:',
        ldap_error($ldapLink)
    );
}

// All input should be sanity-checked by now, but use escapeshellarg() anyway.
$adduserCommand = implode(" ", array(
    './bin/add-member',
    '-m', escapeshellarg($username),
    escapeshellarg($newMemberUsername),
    escapeshellarg($newMemberPassword),
));

// Use the 'script()' utility to get the same output we would get in a tty.
$scriptCommand = implode(' ', array(
    'script -qefc', escapeshellarg($adduserCommand), '/dev/null'
));

println('Trying to add member...');
println('');

passthru($scriptCommand, $retval);

if ($retval !== 0) {
    println('');
    printAndExit(
        'Error: Adding user failed; please contact admin@feministwiki.org.'
    );
} else {
    println('-----');
    println('');
    println('MEMBER SUCCESSFULLY ADDED');
    println('');
    println("New member username: $newMemberUsername");
    println("New member password: $newMemberPassword");
    println('');
    println('Don\'t forget to write the password down or save it on your computer.');
    println('You can then safely close this page.');
}

?>
