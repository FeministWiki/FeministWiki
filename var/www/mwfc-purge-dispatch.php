<?php

#
# Important notes:
#
# This script is invoked by MediaWiki right after it invokes an SQL command
# to invalidate the parser cache by updating the page_touched column in the
# database, which has a resolution of seconds.  This happens synchronously,
# i.e., MediaWiki waits for the response of this script.
#
# Also, this may be invoked in rapid succession by any script or job that
# mass-purges the caches of large numbers of pages.
#
# To mitigate any potential issues with with re-entrancy of MediaWiki code,
# race conditions, and off-by-one errors in timestamp comparisons, we will
# immediately send a response to the client, and wait 1 second.
#
# (The exact reason why this is needed has not been verified; it's most
# likely because the page_touched update isn't committed to the database
# until after MediaWiki receives a response from this script.)
#
# We perform the 1-second delay, and the actual work, in a separate process,
# so we don't keep the PHP-FPM worker busy.  This is achieved by sending a
# message to a System V message queue that a daemon listens on.
#

fastcgi_finish_request();

$mqKey = ftok('/var/www', 'p');

$msgTypeId = 1;
$msgSizeLimit = 8192;

$msg = $_SERVER['URI'];
$log = $_SERVER['LOG'];

ini_set('error_log', $log);

# The greater-than-or-equal is intentional!  See comments in the receive()
# function of mwfc-purge-process.php, explaining the truncation logic.
if (strlen($msg) >= $msgSizeLimit) {
	error_log("URI too long: $msg");
	exit();
}

$q = msg_get_queue($mqKey);
if (!msg_send($q, $msgTypeId, $msg, false, false, $e)) {
	if ($e === MSG_EAGAIN) {
		$e = "Queue is full.";
	} else {
		$e = posix_strerror($e);
	}
	error_log("ERROR: Couldn't send message: $e");
	error_log("Message was: $msg");
}
