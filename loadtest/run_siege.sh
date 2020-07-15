#!/usr/bin/env bash

if [ -n "$1" ]; then
SERVER=$1;
else
SERVER="http://localhost:8080";
fi

ABSOLUTE_FILENAME=`readlink -e "$0"`
DIRECTORY=`dirname "$ABSOLUTE_FILENAME"`
cd ${DIRECTORY}

echo "$SERVER/accounts/transfer POST <transfer.json" > siege.urls

siege -c 40 -r 250 -f siege.urls -T "application/json"
