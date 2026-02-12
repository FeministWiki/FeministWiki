<?php

#
# See mwfc-purge-dispatch.php for related documentation.
#

$workers = 8;
$workLimit = 8192;
$workDelayMs = 1000;

$mqKey = ftok('/var/www', 'p');

$msgTypeId = 1;
$msgSizeLimit = 8192;

pcntl_async_signals(true);

pcntl_signal(SIGTERM, 'shutdown', false);
pcntl_signal(SIGHUP, 'shutdown', false);
pcntl_signal(SIGINT, 'shutdown', false);

$q = msg_get_queue($mqKey);

$name = 'mwfc-purger';
$pids = [];

function errlog($msg) {
	global $name;
	$ts = date('Y-m-d H:i:s');
	error_log("$ts $name: $msg");
}

cli_set_process_title($name);

for ($i = 0; $i < $workers; ++$i) {
	spawn($i);
}

errlog("Started $i workers.");

while (true) {
	$i = wait($s);
	if (pcntl_wifexited($s)) {
		errlog("Respawning worker $i.");
		spawn($i);
	} else if (pcntl_wifsignaled($s)) {
		$status = pcntl_wexitstatus($s);
		$signal = pcntl_wtermsig($s);
		errlog("Worker $i died. Status: $status Signal: $signal");
		errlog("Respawning after a short delay.");
		sleep(2);
		spawn($i);
	}
	sleep(1);
	gc_collect_cycles();
}

function spawn($i) {
	global $pids;

	switch ($p = pcntl_fork()) {
	case -1:
		errlog("FATAL: Couldn't fork.");
		shutdown(false);
	case 0:
		worker_main($i);
		exit();
	}
	$pids[$p] = $i;
}

function wait(&$s) {
	global $pids;

	while ( ($p = pcntl_wait($s)) === -1 ) {
		$e = pcntl_strerror(pcntl_errno());
		errlog("Unexpected wait() error: $e");
		sleep(1);
	}
	$i = $pids[$p];
	unset($pids[$p]);
	return $i;
}

function shutdown($s) {
	global $pids;

	if ($s !== false) {
		errlog("Caught signal $s. Shutting down.");
	}

	foreach ($pids as $p => $_) {
		posix_kill($p, SIGTERM);
	}

	while ($pids) wait($_);

	exit();
}

function worker_main($i) {
	global $name;
	global $workLimit;

	$name = "mwfc-purger/worker-$i";

	cli_set_process_title($name);
	if (posix_isatty(STDIN)) {
		posix_setpgid(0, 0);
	}

	$stop = false;
	pcntl_signal(SIGTERM, function() use (&$stop) {
		errlog("Caught SIGTERM. Finishing.");
		$stop = true;
	});

	for ($i = 0; $i < $workLimit; ++$i) {
		$uri = receive($stop);
		if ($uri) handle($uri);
		if ($stop) break;
		gc_collect_cycles();
	}

	errlog("Exiting after $i tasks.");
}

function receive(&$stop) {
	global $q;
	global $msgTypeId;
	global $msgSizeLimit;

	//
	// The SysV message queue API has some nasty limitations:
	//
	// * A naive "if (!$stop) receive()" pattern would be vulnerable to
	//   race conditions, as the signal setting $stop could run the exact
	//   moment before the receive() starts waiting.  The elegant solution
	//   would be to block signals, and have receive() atomically unblock
	//   and handle them.  This is not supported by the API.  The only
	//   solution is to always use NOWAIT and sleep between calls.
	//
	// * A too-large message triggers an error, but is *not* discarded, and
	//   there's simply no API to have it discarded atomically; an explicit
	//   discard (through a bogus receive call with size = 1 and NOERROR)
	//   would once again be vulnerable to race conditions, since multiple
	//   processes may do it in parallel.  The only solution is to always
	//   use NOERROR and assume messages of maximum length to be truncated;
	//   the sender should then refuse to send messages of length equal to,
	//   not only greater than, the size limit.
	//

	$mt = $msgTypeId;
	$ms = $msgSizeLimit;
	$fl = MSG_IPC_NOWAIT | MSG_NOERROR;
	while (!msg_receive($q, $mt, $_, $ms, $msg, false, $fl, $e)) {
		if ($e !== MSG_ENOMSG) {
			$e = pcntl_strerror($e);
			errlog("Couldn't read message. Error: $e");
		}
		usleep(100 * 1000);
		if ($stop) return false;
	}
	// Shouldn't happen if sender is configured correctly, but still.
	if (strlen($msg) === $ms) {
		errlog("Discarding truncated message: $msg");
		return false;
	}
	return $msg;
}

function handle($uri) {
	global $workDelayMs;
	global $name;

	usleep($workDelayMs * 1000);

	purge($uri, $name);
	get($uri, "$name/phone");
	get($uri, "$name/desktop");
}

// Runs the real Nginx purger
function purge($uri, $ua) {
	$c = curl_init('http://127.0.0.1:1081');
	curl_setopt_array($c, [
		CURLOPT_CUSTOMREQUEST => 'PURGE',
		CURLOPT_REQUEST_TARGET => $uri,
		CURLOPT_WRITEFUNCTION => ignore_write(...),
		CURLOPT_USERAGENT => $ua,
	]);
	curl_exec($c);
}

// Requests a page to warm the cache
function get($uri, $ua) {
	$c = curl_init($uri);
	curl_setopt_array($c, [
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_CONNECT_TO => [ '::127.0.0.1:' ],
		CURLOPT_WRITEFUNCTION => ignore_write(...),
		CURLOPT_USERAGENT => $ua,
	]);
	curl_exec($c);
}

function ignore_write($c, $d) {
	return strlen($d);
}
