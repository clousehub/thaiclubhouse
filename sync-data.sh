#!/bin/bash -e

mkdir -p private/git-last-updated
find private/git-last-updated -name time -type f -mmin +60 -delete
if [ ! -e private/git-last-updated/time ]
then

  COMMIT_MESSAGE="$(node automation.js generate-store-commit-message)"

  (
    cd data
    git add store_shards
    git commit -m "$COMMIT_MESSAGE

    Disclaimer: This commit contains user-generated contents and is not moderated." || true
    git push -f
  )

  touch private/git-last-updated/time

else

  echo "Not update now yet..."

fi