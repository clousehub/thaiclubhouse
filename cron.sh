#!/bin/bash -e

PATH=$HOME/.volta/bin:$PATH
cd "$(dirname "$0")"

./update.sh
./deploy.sh
./sync-data.sh
node automation.js ingest-active-rooms