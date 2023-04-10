from pwn import *

context.log_level = 'debug'

p = remote('pwnable.kr', '9008')

p.recvuntil(b'Ready? starting in 3 sec ... -')
p.recvuntil(b'N=')
N = int(p.recvuntil(b' ', drop=True))
p.recvuntil(b'C=')
C = int(p.recvuntil(b'\n', drop=True))

l = list(range(0, N, N//C))
l = ['%s %s' %(l[i], l[i+1]) for i in range(len(l)-1)]
payload = '-'.join(l)
p.sendline(payload.encode())

p.interactive()