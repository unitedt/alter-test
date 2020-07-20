#!/usr/bin/env bash

if [ -n "$1" ]; then
SERVER=$1;
else
SERVER="http://localhost:8080";
fi

ABSOLUTE_FILENAME=`readlink -e "$0"`
DIRECTORY=`dirname "$ABSOLUTE_FILENAME"`
cd ${DIRECTORY}

echo -e "$SERVER/accounts/transfer POST <transfer.json" > siege.urls
#echo -e "$SERVER/accounts/transfer/2000" > siege.urls
#echo -e "$SERVER/accounts/transfer/2001" >> siege.urls
echo -e "$SERVER/accounts/transfer POST <transfer2.json" >> siege.urls
#echo -e "$SERVER/accounts/transfer/3000" >> siege.urls
#echo -e "$SERVER/accounts/transfer/3001" >> siege.urls
echo -e "$SERVER/accounts/transfer POST <transfer3.json" >> siege.urls
#echo -e "$SERVER/accounts/transfer/4000" >> siege.urls
#echo -e "$SERVER/accounts/transfer/4001" >> siege.urls
echo -e "$SERVER/accounts/transfer POST <transfer4.json" >> siege.urls
#echo -e "$SERVER/accounts/transfer/5000" >> siege.urls
#echo -e "$SERVER/accounts/transfer/5001" >> siege.urls
#echo -e "$SERVER/accounts/transfer/6000" >> siege.urls
#echo -e "$SERVER/accounts/transfer/6001" >> siege.urls
echo -e "$SERVER/accounts/transfer POST <transfer5.json" >> siege.urls

siege -c 40 -r 200 -f siege.urls -T "application/json"
