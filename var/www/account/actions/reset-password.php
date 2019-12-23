<?php

include 'common.php';

$username = $_POST['username'];
$email = $_POST['email'];

if ($username == '') {
    printAndExit('Error: You forgot to enter your username.');
}

if ($email == '') {
    printAndExit('Error: You forgot to enter your recovery e-mail address.');
}

$ldapLink = ldap_connect('localhost');

if ($ldapLink == FALSE) {
    printAndExit(
        'Error: Couldn\'t access member directory.',
        '       Please contact admin@feministwiki.org.'
    );
}

ldap_set_option($ldapLink, LDAP_OPT_PROTOCOL_VERSION, 3);

$ldapBind = 'cn=readonly,dc=feministwiki,dc=org';
include '.ldap-pass.php';

if (ldap_bind($ldapLink, $ldapBind, $ldapPass) !== TRUE) {
    printAndExit(
        'Error: Couldn\'t log in to member directory.',
        '       Please contact admin@feministwiki.org.'
    );
}

$baseDN = 'ou=members,dc=feministwiki,dc=org';
$usernameEsc = ldap_escape($username, '', LDAP_ESCAPE_FILTER);
$emailEsc = ldap_escape($email, '', LDAP_ESCAPE_FILTER);
$filter = "(&(cn=$usernameEsc)(fwRecoveryMail=$emailEsc))";
$attrs = array('fwRecoveryMail');

$result = ldap_search($ldapLink, $baseDN, $filter, $attrs);

if ($result === FALSE) {
    printAndExit(
        'Error: Couldn\'t read member directory.',
        '       Please contact admin@feministwiki.org.'
    );
}

if (ldap_count_entries($ldapLink, $result) == 0) {
    printAndExit(
        'Error: Couldn\'t find this combination of a username and recovery e-mail.'
    );
}

println('Resetting password...');
println('');

$password = generatePassword();

// All input should be sanity-checked by now, but use escapeshellarg() anyway.
$command = implode(' ', array(
    './bin/reset-password',
    escapeshellarg($username),
    escapeshellarg($password)
));

// Use the 'script' utility to get the same output we would get in a tty.
$scriptCommand = implode(' ', array(
    'script -qefc', escapeshellarg($command), '/dev/null'
));

passthru($scriptCommand, $retval);

if ($retval !== 0) {
    println('');
    printAndExit(
        'Error: Resetting password failed.',
        '       Please contact admin@feministwiki.org.'
    );
}

println('Password was reset.  Trying to send out e-mail...');
println('');

$subject = 'New FeministWiki password';
$headers = composeEmailHeaders(
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: FeministWiki <admin@feministwiki.org>',
    'Reply-To: FeministWiki Technician <admin@feministwiki.org>'
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
    printAndExit(
        'Error: Failed to send out e-mail.',
        '       Please contact admin@feministwiki.org.'
    );
}

println('-----');
println('');
println('PASSWORD RESET SUCCESSFUL.');
println('CHECK YOUR RECOVERY E-MAIL INBOX.');

?>
