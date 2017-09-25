#!/usr/bin/env bash

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
COMPOSE="docker-compose"
DO="run --rm"

if [ $# -gt 0 ]; then

  # run subscript when found
  if [ -f "mp/$1.sh" ]; then
    SCRIPT="$1"
    shift 1

    ./mp/${SCRIPT}.sh "$@"

  # run docker container commands
  elif [ "$1" == "php" ]; then
    shift 1

    ${COMPOSE} ${DO} php "$@"

  # default to docker-compose
  else
    ${COMPOSE} "$@"
  fi

fi
