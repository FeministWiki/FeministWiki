<?php

require './lib/commands.php';
require './lib/email.php';
require './lib/ldap.php';
require './lib/print.php';
require './lib/pwdgen.php';
require './lib/verify.php';

header('Content-Type: text/plain');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

if (PHP_SAPI == 'cli') {
    parse_str(implode('&', array_slice($argv, 1)), $_POST);
    return;
}

if ($skipSecurityCheck === TRUE) {
    return;
}

require './riddle/validate.php';

?>
