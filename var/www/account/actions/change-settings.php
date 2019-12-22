<?php

include 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

$newPassword = $_POST['newPassword'];
$newPassword2 = $_POST['newPassword2'];
$displayName = $_POST['displayName'];
$email = $_POST['email'];
$recoveryMail = $_POST['recoveryMail'];

if ($username == '') {
    printAndExit('Error: You forgot to enter your username.');
}

if ($password == '') {
    printAndExit('Error: You forgot to enter your password.');
}

if ($newPassword != '' && strlen($newPassword) < 8) {
    printAndExit('Error: Your password must be at least 8 characters long.');
}

if ($newPassword != $newPassword2) {
    printAndExit('Error: The passwords you entered did not match.');
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

println('Saving settings...');
println('');

$settings = array();
if ($newPassword != '') {
    println('New password: [secret]');
    $settings['userPassword'] = $newPassword;
}
if ($displayName != '') {
    println("Display name: $displayName");
    $settings['sn'] = $displayName;
}
if ($email != '') {
    println("Email address: $email");
    $settings['mail'] = $email;
}
if ($recoveryMail != '') {
    println("Recovery email: $recoveryMail");
    $settings['fwRecoveryMail'] = $recoveryMail;
}

println('');

if (ldap_mod_replace($ldapLink, $userDN, $settings)) {
    println('-----');
    println('');
    println('SETTINGS SUCCESSFULLY SAVED');
    println('');
    println('You may close this page.');
} else {
    printAndExit(
        'Error: Saving settings failed.',
        'Reason: ' . ldap_error($ldapLink)
    );
}

?>
