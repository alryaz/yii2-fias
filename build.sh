#!/usr/bin/env bash

export uid=$(id -u)
export gid=$(id -g)

docker-compose build