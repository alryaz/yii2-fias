#!/usr/bin/env bash

export uid=$(id -u)
export gid=$(id -g)

docker-compose run --rm --user $UID:$GID php sh