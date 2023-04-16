#!/bin/bash

if [[ -n "$POW_DIFFICULTY" ]]; then
    if ! /chall/pow.py ask "$POW_DIFFICULTY"; then
        echo 'pow fail'
        exit 1
    fi
fi

exec "$@"
