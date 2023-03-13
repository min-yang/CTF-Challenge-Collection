from pwn import *

context.log_level = 'DEBUG'

p = remote('pwnable.kr', '9007')

p.recvuntil(b'Ready?')

for _ in range(100):
    p.recvuntil(b'N=')
    N = int(p.recvuntil(b' '))
    p.recvuntil(b'C=')
    C = int(p.recvuntil(b'\n'))

    start = 0
    end = N
    pivot = N // 2
    while True:
        idxs = ''
        for i in range(start, pivot):
            idxs += str(i) + ' '
        p.sendline(idxs.encode())
        weight = p.recvline()
        if b'Correct' in weight:
            break
        weight = int(weight)
        if weight < (pivot - start) * 10:
            start = start
            end = pivot
            pivot = start + ((end - start) // 2)
        else:
            start = pivot
            end = end
            pivot = pivot + ((end - start) // 2)
        if start == pivot:
            pivot += 1

p.interactive()