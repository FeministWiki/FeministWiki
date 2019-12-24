<?php

include 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

$newUsername     = $_POST['newUsername'];
$newPassword     = generatePassword();
$newEmail        = $_POST['newEmail'];
$newRecoveryMail = $_POST['newRecoveryMail'];


if ($username == '') {
    printAndExit('Error: You forgot to enter your username.');
}

if ($password == '') {
    printAndExit('Error: You forgot to enter your password.');
}

if (preg_match('/^[a-z]+[a-z0-9]*$/i', $newUsername) !== 1) {
    printAndExit(
        'Error: Invalid username.  Please only use letters and digits,',
        '       and make sure the name doesn\'t start with a digit.'
    );
}

if ($newPassword == NULL) {
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

println('Trying to add member...');
println('');

$retval = addMember($newUsername, $newPassword, $newEmail, $newRecoveryMail, $username);

if ($retval !== 0) {
    println('');
    printAndExit(
        'Error: Adding user failed; please contact admin@feministwiki.org.'
    );
}

println('-----');
println('');
println('MEMBER SUCCESSFULLY ADDED');
println('');

# Try the "private" email address first.
$address = $newRecoveryMail;
if ($address == '') {
    $address = $newEmail;
}

$printPassword = FALSE;

if ($address == '') {
    $printPassword = TRUE;
} else {
    println("Trying to email the new member ($address)...");
    println('');

    $retval = sendWelcomeEmail($address, $newUsername, $newPassword);

    if ($retval !== TRUE) {
        $printPassword = TRUE;
        println('Sending email failed.  Please contact admin@feministwiki.org.');
        println('');
    } else {
        println('Success!');
        println('');
        println('You may safely close this page.');
    }
}

if ($printPassword) {
    println("New member username: $newUsername");
    println("New member password: $newPassword");
    println('');
    println('Don\'t forget to write these down or save them on your computer.');
    println('You can then safely close this page.');
}


?>
