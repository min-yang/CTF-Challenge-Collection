import random
import hashlib
import string
from Crypto.Util.number import long_to_bytes

def rand(rng):
    return rng - random.randrange(rng)

m = [140, 96, 112, 178, 38, 180, 158, 240, 179, 202, 251, 138, 188, 185, 23, 67, 163, 22, 150, 18, 143, 212, 93, 87, 209, 139, 92, 252, 55, 137, 6, 231, 105, 12, 65, 59, 223, 25, 179, 101, 19, 215]
key = 2669175714787937 << 12 # base

for i in range(2**12):
    key_tmp = long_to_bytes(key + i)
    random.seed(int(hashlib.md5(key_tmp).hexdigest(), 16))

    flag = ''
    for j in range(len(m)):
        rand(256)
        char = chr(m[j] ^ rand(256))
        if char not in string.printable:
            break
        flag += char
    if len(flag) == len(m):
        print(flag)