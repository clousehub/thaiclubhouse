#!/bin/bash -e

node automation update-links-from-facebook
node automation update-events
node automation parse-events
php gen.php > public/index.html