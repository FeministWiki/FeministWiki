<?php

require 'common.php';

$username = $_POST['username'];
$password = $_POST['password'];

$newUsername     = $_POST['newUsername'];
$newPassword     = generatePassword();
$newEmail        = $_POST['newEmail'];
$newEmail2       = $_POST['newEmail2'];
$saveEmail2      = $_POST['saveEmail2'];

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

if (strlen($newPassword) < 24) {
    technicalError('Password generation failed.');
}

$ldapLink = ldapBind($username, $password);

if (ldapCheckUserExists($newUsername, $ldapLink)) {
    printAndExit('The username you entered is already taken.');
}

$newRecoveryMail = '';
if ($newEmail != '') {
    $newRecoveryMail = $newEmail;
} else if ($saveEmail2 == 'yes') {
    $newRecoveryMail = $newEmail2;
}

println('Trying to add member...');
println('');

$retval = addMember($newUsername, $newPassword, $newEmail, $newRecoveryMail, $username);

if ($retval !== 0) {
    println('');
    technicalError('Adding user failed.');
}

println('-----');
println('');
println('MEMBER SUCCESSFULLY ADDED');
println('');

$address = '';
if ($newEmail2 != '') {
    $address = $newEmail2;
} else if ($newEmail != '') {
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
        println('Sending email failed.  Please notify the member yourself.');
        println('And please contact technician@feministwiki.org about this problem.');
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
    println('And notify to the new user that they can change their settings here:');
    println('https://account.feministwiki.org/settings.html');
    println('');
    println('You can then safely close this page.');
}

mail(
    'technician+accounts@feministwiki.org',
    "New member: $newUsername",
    "User $newUsername was just added by $username."
);

?>
