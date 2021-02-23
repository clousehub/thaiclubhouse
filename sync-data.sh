#!/bin/bash -e

COMMIT_MESSAGE="$(node automation.js generate-store-commit-message)"

cd data
git add store.json store_shards
git commit -m "$COMMIT_MESSAGE

Disclaimer: This commit contains user-generated contents and is not moderated." || true
git push -f