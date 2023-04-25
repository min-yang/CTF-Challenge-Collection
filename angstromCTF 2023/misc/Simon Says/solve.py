from pwn import *

context.log_level = 'DEBUG'

p = remote('challs.actf.co', '31402')

while True:
    p.recvuntil(b'first 3 letters of ')
    word_1 = p.recvuntil(b' ', drop=True).decode()
    p.recvuntil(b'last 3 letters of ')
    word_2 = p.recvuntil(b'\n', drop=True).decode()

    ans = word_1[:3] + word_2[-3:] 
    p.sendline(ans.encode())

p.interactive()