<?php

function composeEmailHeaders() {
    $result = '';
    foreach (func_get_args() as $header) {
        $result .= "$header\r\n";
    }
    return $result;
}

function composeEmailBody() {
    $result = "<html><body>\n";
    foreach (func_get_args() as $line) {
        $result .= "<p>$line</p>\n";
    }
    return $result . '</body></html>';
}

function sendWelcomeEmail($address, $username, $password) {
    $subject = 'Your FeministWiki account has been created!';
    $headers = composeEmailHeaders(
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: FeministWiki <technician@feministwiki.org>',
        'Reply-To: FeministWiki Technician <technician@feministwiki.org>',
        'Cc: technician@feministwiki.org'
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

    return mail($address, $subject, $body, $headers);
}

?>
