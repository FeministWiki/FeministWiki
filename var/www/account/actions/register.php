<?php

include 'common.php';

$username    = $_POST['username'];
$email       = $_POST['email'];
$email2      = $_POST['email2'];
$saveEmail2  = $_POST['saveEmail2'];
$declaration = $_POST['declaration'];

if ($username == '') {
  printAndExit('Error: You forgot to enter a username.');
}

if (preg_match('/^[a-z]+[a-z0-9]*$/i', $username) !== 1) {
  printAndExit(
    'Error: Invalid username; please only use English letters.'
  );
}

if ( ($email == '') == ($email2 == '') ) {
  printAndExit('Error: Please fill in exactly one of the two e-mail fields.');
}

printf('Trying to send out e-mail...\n');
printf('\n');

$retval = mail(
  'admin+accountrequest@feministwiki.org',
  "Account request: $username",
  "Username: $username\n"
  . "Email: $email\n"
  . "Secret email: $email2\n"
  . "Save secret email: $saveEmail2\n"
  . "Declaration:\n$declaration"
);

if ($retval !== TRUE) {
  printAndExit('Error: Failed to send out e-mail; please contact admin@feministwiki.org.');
}

println('Success.');
println('');
println('Your request was mailed to the administrator.');
println('');
println('If you don\'t receive a response within a few days,');
println('feel free to directly contact admin@feministwiki.org.');

?>
