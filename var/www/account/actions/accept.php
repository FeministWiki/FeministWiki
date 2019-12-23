<?php

$skipSecurityCheck = TRUE;

include 'common.php';

$requestID = $_GET['r'];
$pathname = "requests/$requestID";
$contents = unserialize(file_get_contents($pathname));

if ($contents === FALSE) {
    printAndExit("Couldn't parse request: $requestID");
}

$username      = $contents['username'];
$password      = generatePassword();
$email         = $contents['email'];
$recoveryMail  = $contents['recoveryMail'];
$temporaryMail = $contents['temporaryMail'];

$command = array('./add-member');

if ($email != '') {
    $command[] = "-e";
    $command[] = $email;
}

if ($recoveryMail != '') {
    $command[] = "-r";
    $command[] = $recoveryMail;
}

$command[] = escapeshellarg($username);
$command[] = escapeshellarg($password);

$adduserCommand = implode(' ', $command);

// Use the 'script()' utility to get the same output we would get in a tty.
$scriptCommand = implode(' ', array(
    'script -qefc', escapeshellarg($adduserCommand), '/dev/null'
));

println('Trying to add member...');
println('');

passthru($scriptCommand, $retval);

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

println('Trying to mail the admin...');

$retval = mail(
    'admin@feministwiki.org',
    'New member accepted',
    "Username: $username\n"
    . "Password: $password\n"
);

if ($retval !== TRUE) {
    println('Couldn\'t mail the admin!');
    println('');
}

$address = $email;
if ($address == '') {
    $address = $recoveryMail;
}
if ($address == '') {
    $address = $temporaryMail;
}

println("Trying to mail the new member ($address)...");
println('');

$subject = 'Your FeministWiki account has been created!';
$headers = composeEmailHeaders(
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: FeministWiki <admin@feministwiki.org>',
    'Reply-To: FeministWiki Technician <admin@feministwiki.org>'
);
$body = composeEmailBody(
    "Welcome to the FeministWiki, $username!",
    '',
    'A password of random English words was automatically '
    . 'generated for your account:',
    '',
    $password,
    '',
    'You can change your password and other settings here:',
    '',
    '<a href="https://account.feministwiki.org/settings.html">'
    . 'FeministWiki Account Settings'
    . '</a>',
    '',
    'If you would like to learn more about the FeministWiki, '
    . 'please refer to the Welcome Page:',
    '',
    '<a href="https://feministwiki.org/wiki/FW:Welcome">'
    . 'Welcome to the FeministWiki!'
    . '</a>',
    '',
    'If you have any questions, don\'t be shy to ask! '
    . 'Replies to this email will be forwarded to the technician.'
);

$retval = mail($address, $subject, $body, $headers);

if ($retval !== TRUE) {
    printAndExit(
        'Error: Failed to send out e-mail.',
        '       Please contact admin@feministwiki.org.'
    );
}

println('Success!');

?>
