#!/usr/bin/env bash
set -eo pipefail

# init environment variables
set -o allexport
ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
COMPOSE="docker-compose"
DO="run --rm"
set +o allexport

# run commands
if [ $# -gt 0 ]; then
  if [ -f "mp/$1" ]; then
    SCRIPT="$1"
    shift 1
    ./mp/${SCRIPT} "$@"
  elif [ "$1" == "help" ]; then
    echo -e "\033[0;30;47m Available commands \033[0m"
    ls -1 mp
  else
    ${COMPOSE} "$@"
  fi
else
  ${COMPOSE} ps
fi
