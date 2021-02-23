#!/bin/bash -e

node automation update-links-from-facebook
node automation update-links-from-google-forms
node automation update-links-from-twitter
node automation update-events
node automation parse-events
php gen.php