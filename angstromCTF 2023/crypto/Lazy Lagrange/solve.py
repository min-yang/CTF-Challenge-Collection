from pwn import *

context.log_level = 'DEBUG'

p = remote('challs.actf.co', '32100')

flag = {'c', 'a', '0', '7', '8', 't', '}', '6', 'f', '{', 'b'}
while True:
    try:
        p.sendlineafter(b': ', b'1')
        p.sendlineafter(b'> ', b'0')
        flag.add(chr(int(p.recvuntil(b'\n', drop=True).decode())))
        print(flag, len(flag))
    except:
        p = remote('challs.actf.co', '32100')
