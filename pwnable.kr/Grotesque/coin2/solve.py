from pwn import *

def get_group(n, c):
    groups = [[] for _ in range(c)]
    for i in range(n):
        i_bin = bin(i)[2:]
        for j in range(1, len(i_bin)+1):
            if i_bin[-j] == '1':
                groups[-j].append(str(i))
    return groups

# context.log_level = 'debug'

p = remote('localhost', '9008')

p.recvuntil(b'Ready? starting in 3 sec ... -')

for i in range(100):
    print(i)
    p.recvuntil(b'N=')
    N = int(p.recvuntil(b' ', drop=True))
    p.recvuntil(b'C=')
    C = int(p.recvuntil(b'\n', drop=True))

    groups = get_group(N, C)
    groups = [' '.join(ele) for ele in groups]
    payload = '-'.join(groups)
    p.sendline(payload.encode())
    result = p.recvline().decode()
    target = ''
    for ele in result.split('-'):
        if int(ele) % 10 != 0:
            target += '1'
        else:
            target += '0'
    target = int(target, 2)
    p.sendline(str(target).encode())

p.interactive()