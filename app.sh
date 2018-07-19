#!/usr/bin/env bash

export ip=$(/sbin/ifconfig docker0 | grep 'inet addr' | cut -d: -f2 | awk '{print $1}')
export uid=$(id -u)
export gid=$(id -g)
export XDEBUG_CONFIG="remote_host=$ip"

docker-compose up -d