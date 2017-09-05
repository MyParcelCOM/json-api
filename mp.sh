#!/usr/bin/env bash

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
COMPOSE="docker-compose"
DO="run --rm php"

if [ $# -gt 0 ]; then
  # Run composer commands.
  if [ "$1" == "composer" ]; then
    shift 1

    ${COMPOSE} ${DO} composer "$@"

  # Run php tests.
  elif [ "$1" == "test" ]; then
    shift 1

    ${COMPOSE} ${DO} composer test "$@"

  # Run php commands.
  elif [ "$1" == "php" ]; then
    shift 1

    ${COMPOSE} ${DO} "$@"

  # Run docker-compose commands.
  else
    ${COMPOSE} "$@"
  fi
fi
