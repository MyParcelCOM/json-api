#!/usr/bin/env bash

echo -e "\033[0;30;47m Running phpunit $@ \033[0m"
./mp.sh php vendor/bin/phpunit "$@"
