<?php

/*
+-----------------------------------------------------------------------+
| Local configuration for the Roundcube Webmail installation.           |
|                                                                       |
| This is a sample configuration file only containing the minimum       |
| setup required for a functional installation. Copy more options       |
| from defaults.inc.php to this file to override the defaults.          |
|                                                                       |
| This file is part of the Roundcube Webmail client                     |
| Copyright (C) 2005-2013, The Roundcube Dev Team                       |
|                                                                       |
| Licensed under the GNU General Public License version 3 or            |
| any later version with exceptions for skins & plugins.                |
| See the README file for a full license statement.                     |
+-----------------------------------------------------------------------+
*/

require '.secrets.php';

$config = array();

$config['enable_installer'] = true;

// Database connection string (DSN) for read+write operations
// Format (compatible with PEAR MDB2): db_provider://user:password@host/database
// Currently supported db_providers: mysql, pgsql, sqlite, mssql, sqlsrv, oracle
// For examples see http://pear.php.net/manual/en/package.database.mdb2.intro-dsn.php
// NOTE: for SQLite use absolute path (Linux): 'sqlite:////full/path/to/sqlite.db?mode=0646'
//       or (Windows): 'sqlite:///C:/full/path/to/sqlite.db'
$config['db_dsnw'] = "mysql://feministmail:${fwRCDBPassword}@localhost/feministmail";

// enforce connections over https
// with this option enabled, all non-secure connections will be redirected.
// set the port for the ssl connection as value of this option if it differs from the default 443
$config['force_https'] = true;

$config['mail_domain'] = 'feministwiki.org';

// The mail host chosen to perform the log-in.
// Leave blank to show a textbox at login, give a list of hosts
// to display a pulldown menu or set one host as string.
// To use SSL/TLS connection, enter hostname with prefix ssl:// or tls://
// Supported replacement variables:
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %s - domain name after the '@' from e-mail address provided at login screen
// For example %n = mail.domain.tld, %t = domain.tld
$config['imap_host'] = 'ssl://imap.feministwiki.org';

// SMTP server host (for sending mails).
// To use SSL/TLS connection, enter hostname with prefix ssl:// or tls://
// If left blank, the PHP mail() function is used
// Supported replacement variables:
// %h - user's IMAP hostname
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %z - IMAP domain (IMAP hostname without the first part)
// For example %n = mail.domain.tld, %t = domain.tld
$config['smtp_host'] = 'ssl://smtp.feministwiki.org';

// SMTP username (if required) if you use %u as the username Roundcube
// will use the current username for login
$config['smtp_user'] = '%u';

// SMTP password (if required) if you use %p as the password Roundcube
// will use the current user's password for login
$config['smtp_pass'] = '%p';

// TODO: Temporary hack because of STRATO AG VPS fail.
// Once STRATO fixes the SSL config of their VPSs, the following
// two statements can be removed again.
$config['imap_conn_options'] = array(
    'ssl' => array(
        'cafile' => '/etc/ssl/certs/ca-certificates.crt'
    ),
);
$config['smtp_conn_options'] = array(
    'ssl' => array(
        'cafile' => '/etc/ssl/certs/ca-certificates.crt'
    ),
);

// provide an URL where a user can get support for this Roundcube installation
// PLEASE DO NOT LINK TO THE ROUNDCUBE.NET WEBSITE HERE!
$config['support_url'] = 'mailto:technician@feministwiki.org';

// Name your service. This is displayed on the login screen and in the window title
$config['product_name'] = 'FeministMail';

$config['ldap_public']['public'] = array(
    'name'              => 'FeministWiki',
    'hosts'             => array('localhost'),
    'user_specific'     => false,
    'base_dn'           => 'ou=members,dc=feministwiki,dc=org',
    'name_field'        => 'cn',
    'firstname_field'   => 'givenName',
    'surname_field'     => 'sn',
    'sort'              => 'cn',
    'email_field'       => 'mail',
    'filter'            => '(objectClass=inetOrgPerson)',
);

// this key is used to encrypt the users imap password which is stored
// in the session record (and the client cookie if remember password is enabled).
// please provide a string of exactly 24 chars.
// YOUR KEY MUST BE DIFFERENT THAN THE SAMPLE VALUE FOR SECURITY REASONS
$config['des_key'] = $fwRCDESKey;

// List of active plugins (in plugins/ directory)
$config['plugins'] = array(
'archive',
'persistent_login',
'zipdownload',
);

// skin name: folder from skins/
$config['skin'] = 'elastic';
