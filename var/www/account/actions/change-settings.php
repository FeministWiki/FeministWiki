<?php

require 'common.php';

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

$ldapLink = ldapBind($username, $password);

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

$userDN = "cn=$username,ou=members,dc=feministwiki,dc=org";

if (ldap_mod_replace($ldapLink, $userDN, $settings)) {
    println('-----');
    println('');
    println('SETTINGS SUCCESSFULLY SAVED');
    println('');
    println('You may close this page.');
} else {
    adminError('Saving settings failed. CODE: ' . ldap_error($ldapLink));
}

?>
