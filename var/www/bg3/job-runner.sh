#!/bin/sh

MW_INSTALL_PATH=/var/www/bg3/w

RUNJOBS=$MW_INSTALL_PATH/maintenance/runJobs.php

echo Starting job service...

renice +19 $$

# Wait a minute after the server starts up to give other processes time to get started
sleep 60

echo Started.

while true
do
	# Job types that need to be run ASAP
	php "$RUNJOBS" --type="enotifNotify"

	php "$RUNJOBS" --wait --maxjobs=20

	sleep 10
done
