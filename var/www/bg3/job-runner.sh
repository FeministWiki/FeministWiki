#!/bin/bash

MW_INSTALL_PATH=/var/www/bg3/w

RUNJOBS=$MW_INSTALL_PATH/maintenance/runJobs.php

echo Starting job service...

# Wait a minute after the server starts up to give other processes time to get started
sleep 60

echo Started.

while sleep 2
do
	# Job types that need to be run ASAP
	php "$RUNJOBS" --type="enotifNotify"

	php "$RUNJOBS" --wait --maxjobs=20
done
