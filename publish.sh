#!/usr/bin/env bash
set -euo pipefail

message="${*:-}"

if [[ -z "$message" ]]; then
  echo 'Usage: ./publish.sh "Your commit message"'
  exit 1
fi

export BUTTERFLY_COMMIT_MESSAGE="$message"

git submodule foreach --recursive '
  echo "Publishing $displaypath"
  git add -A

  if ! git diff --cached --quiet; then
    git commit -m "$BUTTERFLY_COMMIT_MESSAGE"
  fi

  git push
'

git add -A

if ! git diff --cached --quiet; then
  git commit -m "$message"
fi

git push