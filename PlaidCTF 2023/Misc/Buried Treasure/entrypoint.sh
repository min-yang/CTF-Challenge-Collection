#!/bin/bash

# Setup jail cgroup
mkdir /sys/fs/cgroup/jailparentgroup/
echo "0" > /sys/fs/cgroup/jailparentgroup/cgroup.procs
cgcreate -a root -t root -g pids,memory,cpu:jailchildgroup

socat TCP-LISTEN:1337,reuseaddr,fork EXEC:"timeout 180 /chall/wrapper.sh python3 chall.py",stderr,pty,echo=0
