<?php

function ldapBind($username = NULL, $password = NULL) {
    $ldapLink = ldap_connect('localhost');

    if ($ldapLink === FALSE) {
        adminError('Couldn\'t access member directory.');
    }

    ldap_set_option($ldapLink, LDAP_OPT_PROTOCOL_VERSION, 3);

    if ($username === NULL) {
        $username = 'cn=readonly,dc=feministwiki,dc=org';
        include __DIR__ . '/.ldap-readonly-password.php';
        $password = $ldapReadonlyPassword;
        if (ldap_bind($ldapLink, $username, $password) !== TRUE) {
            adminError('Couldn\'t log in to member directory.');
        }
    } else {
        $username = "cn=$username,ou=members,dc=feministwiki,dc=org";
        if ($password === NULL) {
            adminError('Provided username but no password.');
        }
        if (ldap_bind($ldapLink, $username, $password) !== TRUE) {
            printAndExit(
                'Error: Login failed.  Please check your username and password.',
                '',
                'If you\'re sure you entered your username and password correctly,',
                'contact admin@feministwiki.org and give them this error code:',
                ldap_error($ldapLink)
            );
        }
    }

    return $ldapLink;
}

function ldapCheckUserExists($username, $ldapLink = NULL) {
    if ($ldapLink === NULL) {
        $ldapLink = ldapBind();
        if ($ldapLink === FALSE) {
            adminError('Couldn\'t log in to member directory.');
        }
    }

    $memberBase = 'ou=members,dc=feministwiki,dc=org';
    $filter = 'cn=' . ldap_escape($username, '', LDAP_ESCAPE_FILTER);

    $result = ldap_search($ldapLink, $memberBase, $filter, array());

    if ($result === FALSE) {
        adminError('Search of member directory failed.');
    }

    return ldap_count_entries($ldapLink, $result) > 0;
}

?>
