from ctypes import CDLL
from pwn import *
from Crypto.Util.number import long_to_bytes, bytes_to_long

if not os.path.exists('crypt1.png'):
    cdll = CDLL('/usr/lib/x86_64-linux-gnu/libc.so.6')
    cdll.srand(1416667590)

    data = open('ecrypt1.bin', 'rb').read()
    plainText = b''
    for ele in data:
        plainText += p8(ele ^ (cdll.rand() % 256))

    open('crypt1.png', 'wb').write(plainText)

# 这里通过爆破拿到flag，另一种方法是通过中国剩余定理推导出flag

N = 0xB8AE199365
B = 0xFFEEE
C = 0x8D5051562B
m = bytes_to_long(b'SECCO')
if (m * (m + B)) % N == C:
    print('SECCO', end='')

candidates = string.printable
N = 0xB86E78C811
B = 0xFFFEE
C = 0x5FFA0AC1A2
m = 'N{'
try:
    for i in candidates:
        for j in candidates:
            for k in candidates:
                tmp = m + i + j + k
                tmp = bytes_to_long(tmp.encode())
                if (tmp * (tmp + B)) % N == C:
                    print(m + i + j + k, end='')
                    raise ValueError('end')
except:
    pass

N = 0x7BD4071E55
B = 0xFEFEF
C = 0x6008DDF867
try:
    for i in candidates:
        for j in candidates:
            for k in candidates:
                for l in candidates:
                    tmp = i + j + k + l + '}'
                    tmp = bytes_to_long(tmp.encode())
                    if (tmp * (tmp + B)) % N == C:
                        print(i + j + k + l + '}')
                        raise ValueError('end')
except:
    pass