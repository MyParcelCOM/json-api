#!/usr/bin/env bash

USER_ID=${UID-0}
GROUP_ID=${GROUPS-0}
COMPOSE="docker run --rm -it -w /opt -v $(pwd):/opt -u ${USER_ID}:${GROUP_ID}"
PHP="${COMPOSE} php:7.1-alpine"
COMPOSER="${COMPOSE} composer:1.5 composer"

if [ $# -gt 0 ]; then
  # Run composer commands.
  if [ "$1" == "composer" ]; then
    shift 1

    ${COMPOSER} "$@"

  # Run php tests.
  elif [ "$1" == "test" ]; then
    shift 1

    ${COMPOSER} test "$@"
  elif [ "$1" == "php" ]; then
    shift 1

    ${PHP} "$@"
  fi
fi
