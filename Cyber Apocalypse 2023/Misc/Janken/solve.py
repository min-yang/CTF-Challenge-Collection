from pwn import *

context.log_level = 'DEBUG'

p = process('./janken')
p = remote('159.65.94.38', '31948')
p.sendlineafter(b'>> ', b'1')

for _ in range(100):
    p.sendlineafter(b'>> ', b'rockpaperscissors')

print(p.recv())

p.interactive()