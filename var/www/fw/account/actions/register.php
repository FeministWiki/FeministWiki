<?php

require 'common.php';

$username    = $_POST['username'];
$email       = $_POST['email'];
$email2      = $_POST['email2'];
$saveEmail2  = $_POST['saveEmail2'];
$declaration = $_POST['declaration'];

if ($username == '') {
    printAndExit('Error: You forgot to enter a username.');
}

if (preg_match('/^[a-z]+[a-z0-9]*$/i', $username) !== 1) {
    printAndExit('Error: Invalid username; please only use English letters.');
}

$retval = getentPasswd($username);
if ($retval === 0) {
    printAndExit('Error: System-reserved username; please choose a different one.');
} else if ($retval !== 2) {
    technicalError('Could not validate username.');
}

if (isUsernameForbidden($username)) {
    printAndExit('Forbidden username; please choose a different one.');
}

if (ldapCheckUserExists($username)) {
    printAndExit('The username you entered is already taken.');
}

$requestID = date('Ymd-Gis-') . bin2hex(random_bytes(10));
$pathname = "./requests/$requestID";

$userData = array('username' => $username);

if ($email != '') {
    $userData['email'] = $email;
} else if ($saveEmail2 == 'yes') {
    $userData['recoveryMail'] = $email2;
} else {
    $userData['temporaryMail'] = $email2;
}

file_put_contents($pathname, serialize($userData));

println('Trying to send out e-mail...');
println('');

$address = 'technician+accounts@feministwiki.org';
$subject = "Account request: $username";
$headers = composeEmailHeaders(
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
);
$body = composeEmailBody(
    "Username: $username",
    "Email: $email",
    "Secret email: $email2",
    "Save secret email: $saveEmail2",
    "Declaration:\n$declaration",
    '',
    "<a href='https://account.feministwiki.org/actions/accept.php?r=$requestID'>"
    . "Click here to accept this request"
    . "</a>"
);

$retval = mail($address, $subject, $body, $headers);

if ($retval !== TRUE) {
    technicalError('Failed to send out e-mail.');
}

println('Success.');
println('');
println('Your request was mailed to the technician.');
println('');
println('If you don\'t receive a response within a few days,');
println('feel free to directly contact technician@feministwiki.org.');

?>
