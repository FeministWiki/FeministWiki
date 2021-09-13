<?php

require 'common.php';

$username = $_POST['username'];
$email = $_POST['email'];

if ($username == '') {
    printAndExit('Error: You forgot to enter your username.');
}

if ($email == '') {
    printAndExit('Error: You forgot to enter your recovery e-mail address.');
}

$ldapLink = ldapBind();

$baseDN = 'ou=members,dc=feministwiki,dc=org';
$usernameEsc = ldap_escape($username, '', LDAP_ESCAPE_FILTER);
$emailEsc = ldap_escape($email, '', LDAP_ESCAPE_FILTER);
$filter = "(&(cn=$usernameEsc)(fwRecoveryMail=$emailEsc))";

$result = ldap_search($ldapLink, $baseDN, $filter, array());

if ($result === FALSE) {
    technicalError('Couldn\'t read member directory.');
}

if (ldap_count_entries($ldapLink, $result) == 0) {
    printAndExit(
        'Error: Couldn\'t find this combination of a username and recovery e-mail.'
    );
}

println('Resetting password...');
println('');

$password = generatePassword();

$retval = setPassword($username, $password);

if ($retval !== 0) {
    println('');
    technicalError('Resetting password failed.');
}

println('Password was reset.  Trying to send out e-mail...');
println('');

$subject = 'New FeministWiki password';
$headers = composeEmailHeaders(
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: FeministWiki <technician@feministwiki.org>',
    'Reply-To: Technician <technician@feministwiki.org>'
);
$body = composeEmailBody(
    "Dear $username,",
    '',
    'Your FeministWiki password has been reset.',
    'Your new randomly generated password is:',
    '',
    $password,
    '',
    'Remember that you can change it again to your liking here:',
    '',
    '<a href="https://account.feministwiki.org/settings.html">'
    . 'FeministWiki Account Settings'
    . '</a>',
    '',
    'Yours truly',
    'the FeministWiki Technician'
);

$retval = mail($email, $subject, $body, $headers);

if ($retval !== TRUE) {
    technicalError('Failed to send out e-mail.');
}

println('-----');
println('');
println('PASSWORD RESET SUCCESSFUL.');
println('CHECK YOUR RECOVERY E-MAIL INBOX.');

?>
