<?php

function verifyEmail($email) {
    return strlen($email) > 4 && strpos($email, '@') !== FALSE;
}

?>
