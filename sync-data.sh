#!/bin/bash -e

cd data
git add store.json
git commit -m 'Update data state

Disclaimer: This commit contains user-generated contents and is not moderated.' || true
git push -f