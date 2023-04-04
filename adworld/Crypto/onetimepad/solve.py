from Crypto.Util.number import *

c1 = 0xaf3fcc28377e7e983355096fd4f635856df82bbab61d2c50892d9ee5d913a07f
c2 = 0x630eb4dce274d29a16f86940f2f35253477665949170ed9e8c9e828794b5543c
c3 = 0xe913db07cbe4f433c7cdeaac549757d23651ebdccf69d7fbdfd5dc2829334d1b

fake_secret1 = bytes_to_long(b"I_am_not_a_secret_so_you_know_me")
fake_secret2 = bytes_to_long(b"feeddeadbeefcafefeeddeadbeefcafe")

k2 = c2 ^ fake_secret1
k3 = c3 ^ fake_secret2
print(k2, k3)

P = 0x10000000000000000000000000000000000000000000000000000000000000425

# process(k2, seed) == k3
def process(m, k):
    tmp = m ^ k
    res = 0
    for i in bin(tmp)[2:]:
        res = res << 1;
        if (int(i)):
            res = res ^ tmp
        if (res >> 256):
            res = res ^ P
    return res

kt = k3
for i in range(255):
    kt = process(kt, 0)

seed = kt ^ k2
assert process(k2, seed) == k3

kt = k2
for i in range(255):
    kt = process(kt, 0)

k1 = kt ^ seed
assert process(k1, seed) == k2

m = k1 ^ c1
print(long_to_bytes(m))