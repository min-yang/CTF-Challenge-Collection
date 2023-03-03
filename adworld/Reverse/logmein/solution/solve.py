from pwn import *

target = p64(0x5e54525f4c41223a) + p64(0x342f362b3f2e2a4c) + p8(0x36)
op = p64(0x65626d61726168)

flag = ''
for i, b in enumerate(target):
    flag += chr(b ^ op[i % 7])

print(flag)
