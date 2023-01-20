from pwn import *

context.log_level = 'DEBUG'
context.arch = 'amd64'

p = process('./kent_beef')

input('continue?')

p.recvline()

p.sendline(b'\0' * 4)

print(p.recvline().hex())

p.interactive()