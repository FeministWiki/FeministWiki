<?php

$skipSecurityCheck = TRUE;

require 'common.php';

$requestID = $_GET['r'];
$pathname = "./requests/$requestID";
$contents = unserialize(file_get_contents($pathname));

if ($contents === FALSE) {
    printAndExit("Couldn't parse request: $requestID");
}

$username      = $contents['username'];
$password      = generatePassword();
$email         = $contents['email'];
$recoveryMail  = $contents['recoveryMail'];
$temporaryMail = $contents['temporaryMail'];

println("Trying to add member: $username");
println('');

$retval = addMember($username, $password, $email, $recoveryMail);

if ($retval !== 0) {
    println('');
    printAndExit('Error: Failed to add user.');
}

println('-----');
println('');
println('MEMBER SUCCESSFULLY ADDED');
println('');
println("New member username: $username");
println("New member password: $password");
println('');
    
unlink($pathname);

println('Trying to mail the technician...');

$retval = mail(
    'technician+accounts@feministwiki.org',
    "New member: $username",
    "Registration of $username was accepted."
);

if ($retval !== TRUE) {
    println('Couldn\'t mail the technician!');
    println('');
}

# Try the private email address first.
$address = $temporaryMail;
if ($address == '') {
    $address = $recoveryMail;
}
if ($address == '') {
    $address = $email;
}

println("Trying to mail the new member ($address)...");
println('');

$retval = sendWelcomeEmail($address, $username, $password);

if ($retval !== TRUE) {
    printAndExit('Error: Failed to send out e-mail.');
}

println('Success!');

?>
