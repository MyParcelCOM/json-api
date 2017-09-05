#!/usr/bin/env bash

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
COMPOSE="docker-compose"
DO="run --rm php"

# Check if the file with environment variables exists, otherwise copy the default file.
if [ ! -f ${ROOT_DIR}/.env ]; then
  if [ ! -f ${ROOT_DIR}/.env.dist ]; then
    >&2 echo 'Unable to locate .env or .env.dist file'
    exit 1
  fi

  cp ${ROOT_DIR}/.env.dist ${ROOT_DIR}/.env
fi
export $(cat ${ROOT_DIR}/.env | xargs)

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
