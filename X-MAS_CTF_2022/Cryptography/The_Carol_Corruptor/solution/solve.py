import binascii
import base64

from pwn import *
from extend_mt19937_predictor import ExtendMT19937Predictor

from Untwister import Untwister

c = remote('challs.htsp.ro', '10003')

lines = []
rand_ints = []
for i in range(31):
    lines.append(c.recvline().strip())

def get_random_from_line(line):
    if line.strip() == '':
        return []
    v = int(line)

    out = []
    while v != 0:
        out.append(0xFFFFFFFF & v)
        v >>= 32

    return out

rand_ints = []
for line in lines:
    rand_ints += get_random_from_line(line)

t = Untwister()

assert len(rand_ints) == 620

for r in rand_ints:
    t.submit(bin(r)[2:])

t.submit('?' * 32)
t.submit('?' * 32)
t.submit('?' * 32)
t.submit('?' * 32)

r = t.get_random()

predictor = ExtendMT19937Predictor()

for _ in range(624):
    predictor.setrandbits(r.getrandbits(32), 32)

_ = [predictor.backtrack_getrandbits(32) for _ in range(624)]

predictor.backtrack_getrandbits(32 * 4)
secret_guess = str(predictor.predict_getrandbits(2048))[:32]

c.recvuntil(b'> ')
c.sendline(b'2')
c.recvuntil(b': ')
c.sendline(secret_guess.encode())

c.interactive()
# X-MAS{pr3d1ct1ng_M3rs3nne_d03sn't_h4v3_t0_b3_perf3c7!}